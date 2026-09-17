<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PropertyPhotoService
{
    /**
     * Validate and store a batch without writing any file when one item is invalid.
     * GD is used when available; otherwise already-optimized images within the
     * configured dimensions are accepted and oversized images are rejected.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, PropertyPhoto>
     */
    public function storeMany(Property $property, array $files, bool $hasPrimary): array
    {
        $prepared = [];

        try {
            foreach ($files as $file) {
                $prepared[] = $this->prepare($file);
            }

            $storedPaths = [];
            $photos = DB::transaction(function () use ($property, $prepared, $hasPrimary, &$storedPaths): array {
                $photos = [];
                $makePrimary = ! $hasPrimary;

                foreach ($prepared as $index => $item) {
                    $id = (string) Str::uuid();
                    $directory = "properties/{$property->user_id}/{$property->id}";
                    $extension = $item['extension'];
                    $path = "{$directory}/{$id}.{$extension}";

                    $storedPaths[] = $path;
                    $this->writePath($path, $item['path']);

                    $thumbnailPath = null;
                    if ($item['thumbnail_path'] !== null) {
                        $thumbnailPath = "{$directory}/{$id}_thumb.{$extension}";
                        $storedPaths[] = $thumbnailPath;
                        $this->writePath($thumbnailPath, $item['thumbnail_path']);
                    }

                    $photos[] = $property->photos()->create([
                        'user_id' => $property->user_id,
                        'path' => $path,
                        'thumbnail_path' => $thumbnailPath,
                        'original_name' => $item['original_name'],
                        'mime_type' => $item['mime_type'],
                        'size' => Storage::disk('local')->size($path),
                        'width' => $item['width'],
                        'height' => $item['height'],
                        'is_primary' => $makePrimary && $index === 0,
                    ]);
                }

                return $photos;
            });

            return $photos;
        } catch (\Throwable $exception) {
            foreach ($storedPaths ?? [] as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        } finally {
            foreach ($prepared as $item) {
                @unlink($item['path']);
                if ($item['thumbnail_path'] !== null) {
                    @unlink($item['thumbnail_path']);
                }
            }
        }
    }

    /**
     * @return array{path: string, thumbnail_path: string|null, original_name: string, mime_type: string, extension: string, width: int, height: int}
     */
    private function prepare(UploadedFile $file): array
    {
        $source = $file->getRealPath();
        $mime = $source ? (new \finfo(FILEINFO_MIME_TYPE))->file($source) : false;
        $allowed = (array) config('photos.allowed_mimes', []);

        if (! $source || ! is_string($mime) || ! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages([
                'photos' => 'รองรับเฉพาะไฟล์ JPG, PNG หรือ WebP ที่เป็นรูปภาพจริงเท่านั้น',
            ]);
        }

        $dimensions = @getimagesize($source);
        if (! is_array($dimensions) || ($dimensions[0] ?? 0) < 1 || ($dimensions[1] ?? 0) < 1) {
            throw ValidationException::withMessages([
                'photos' => 'ไฟล์รูปภาพเสียหายหรืออ่านข้อมูลภาพไม่ได้',
            ]);
        }

        $width = (int) $dimensions[0];
        $height = (int) $dimensions[1];
        $maxWidth = (int) config('photos.max_width');
        $maxHeight = (int) config('photos.max_height');
        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'bin',
        };

        if (! $this->gdAvailable() && ($width > $maxWidth || $height > $maxHeight)) {
            throw ValidationException::withMessages([
                'photos' => 'รูปมีขนาดใหญ่เกินไปสำหรับเซิร์ฟเวอร์นี้ กรุณาเลือกภาพไม่เกิน '.($maxWidth).'×'.$maxHeight.' พิกเซล',
            ]);
        }

        $optimized = $this->optimize($source, $mime, $width, $height, $extension);

        return [
            ...$optimized,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $mime,
            'extension' => $extension,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @return array{path: string, thumbnail_path: string|null}
     */
    private function optimize(string $source, string $mime, int $width, int $height, string $extension): array
    {
        $main = tempnam(sys_get_temp_dir(), 'agencysuit-photo-');
        if ($main === false) {
            throw ValidationException::withMessages(['photos' => 'ไม่สามารถเตรียมไฟล์รูปภาพได้ กรุณาลองใหม่']);
        }

        if (! $this->gdAvailable()) {
            if (! copy($source, $main)) {
                @unlink($main);
                throw ValidationException::withMessages(['photos' => 'ไม่สามารถเตรียมไฟล์รูปภาพได้ กรุณาลองใหม่']);
            }

            return ['path' => $main, 'thumbnail_path' => null];
        }

        $sourceImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
            default => false,
        };

        if (! $sourceImage) {
            @unlink($main);
            throw ValidationException::withMessages(['photos' => 'ไฟล์รูปภาพเสียหายหรือประมวลผลไม่ได้']);
        }

        $target = $this->resize($sourceImage, $width, $height, (int) config('photos.max_width'), (int) config('photos.max_height'), $mime);
        $this->save($target, $main, $mime);
        imagedestroy($target);
        imagedestroy($sourceImage);

        $thumbnail = tempnam(sys_get_temp_dir(), 'agencysuit-thumb-');
        if ($thumbnail === false) {
            @unlink($main);
            throw ValidationException::withMessages(['photos' => 'ไม่สามารถเตรียมภาพตัวอย่างได้ กรุณาลองใหม่']);
        }

        $thumbSource = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($main),
            'image/png' => @imagecreatefrompng($main),
            'image/webp' => @imagecreatefromwebp($main),
            default => false,
        };
        if (! $thumbSource) {
            @unlink($main);
            @unlink($thumbnail);
            throw ValidationException::withMessages(['photos' => 'ไม่สามารถสร้างภาพตัวอย่างได้ กรุณาลองใหม่']);
        }

        $thumb = $this->resize($thumbSource, imagesx($thumbSource), imagesy($thumbSource), (int) config('photos.thumbnail_width'), (int) config('photos.thumbnail_height'), $mime);
        $this->save($thumb, $thumbnail, $mime);
        imagedestroy($thumb);
        imagedestroy($thumbSource);

        return ['path' => $main, 'thumbnail_path' => $thumbnail];
    }

    private function gdAvailable(): bool
    {
        return function_exists('imagecreatefromjpeg')
            && function_exists('imagecreatefrompng')
            && function_exists('imagecreatefromwebp')
            && function_exists('imagecreatetruecolor')
            && function_exists('imagejpeg')
            && function_exists('imagepng')
            && function_exists('imagewebp');
    }

    private function resize($source, int $width, int $height, int $maxWidth, int $maxHeight, string $mime)
    {
        $scale = min(1, $maxWidth / $width, $maxHeight / $height);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($mime !== 'image/jpeg') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $target;
    }

    private function save($image, string $path, string $mime): void
    {
        $saved = match ($mime) {
            'image/jpeg' => imagejpeg($image, $path, (int) config('photos.jpeg_quality')),
            'image/png' => imagepng($image, $path, (int) config('photos.png_compression')),
            'image/webp' => imagewebp($image, $path, (int) config('photos.webp_quality')),
            default => false,
        };

        if (! $saved) {
            @unlink($path);
            throw ValidationException::withMessages(['photos' => 'ไม่สามารถบันทึกรูปภาพได้ กรุณาลองใหม่']);
        }
    }

    private function writePath(string $path, string $source): void
    {
        $handle = @fopen($source, 'rb');
        $written = $handle !== false && Storage::disk('local')->put($path, $handle);

        if (is_resource($handle)) {
            fclose($handle);
        }

        if (! $written) {
            throw ValidationException::withMessages(['photos' => 'ไม่สามารถบันทึกรูปภาพได้ กรุณาลองใหม่']);
        }
    }
}
