<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => [
                'required',
                'file',
                'max:'.(int) config('photos.max_upload_kb'),
                'mimetypes:'.implode(',', (array) config('photos.allowed_mimes', [])),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'กรุณาเลือกรูปอย่างน้อย 1 รูป',
            'photos.array' => 'รูปภาพที่เลือกไม่ถูกต้อง',
            'photos.*.file' => 'ไฟล์รูปภาพที่เลือกไม่ถูกต้อง',
            'photos.*.max' => 'รูปแต่ละไฟล์ต้องมีขนาดไม่เกิน '.((int) config('photos.max_upload_kb') / 1024).' MB',
            'photos.*.mimetypes' => 'รองรับเฉพาะไฟล์ JPG, PNG หรือ WebP',
        ];
    }
}
