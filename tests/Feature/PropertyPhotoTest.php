<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_user_can_upload_valid_photos_and_first_photo_is_primary(): void
    {
        [$user, $property] = $this->propertyContext();

        $response = $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [$this->jpegUpload('front.jpg'), $this->jpegUpload('living-room.jpg')],
        ]);

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseCount('property_photos', 2);
        $photos = PropertyPhoto::query()->orderBy('id')->get();
        $this->assertTrue($photos[0]->is_primary);
        $this->assertFalse($photos[1]->is_primary);
        Storage::disk('local')->assertExists($photos[0]->path);
    }

    public function test_primary_photo_order_is_deterministic_after_upload_and_reload(): void
    {
        [$user, $property] = $this->propertyContext();
        $this->uploadThree($user, $property);
        $photos = PropertyPhoto::query()->orderBy('id')->get();
        $ordered = $property->fresh()->load('photos')->photos;
        $urls = $photos->map(fn (PropertyPhoto $photo): string => route('properties.photos.thumbnail', [$property, $photo]))->all();

        $this->assertSame($photos->pluck('id')->all(), $ordered->pluck('id')->all());
        $this->assertTrue($ordered->first()->is_primary);
        $this->actingAs($user)->get(route('properties.show', $property))
            ->assertSeeInOrder($urls, false);
        $this->actingAs($user)->get(route('properties.index'))
            ->assertSee($urls[0], false)
            ->assertDontSee($urls[1], false)
            ->assertDontSee($urls[2], false);
    }

    public function test_change_and_delete_primary_keep_detail_and_list_on_deterministic_photo(): void
    {
        [$user, $property] = $this->propertyContext();
        $this->uploadThree($user, $property);
        $photos = PropertyPhoto::query()->orderBy('id')->get();

        $this->actingAs($user)->patch(route('properties.photos.primary', [$property, $photos[2]]));
        $changedUrls = [$photos[2], $photos[0], $photos[1]];
        $changedUrls = array_map(fn (PropertyPhoto $photo): string => route('properties.photos.thumbnail', [$property, $photo]), $changedUrls);

        $this->assertSame($photos[2]->id, $property->fresh()->load('photos')->photos->first()->id);
        $this->actingAs($user)->get(route('properties.show', $property))->assertSeeInOrder($changedUrls, false);
        $this->actingAs($user)->get(route('properties.index'))->assertSee($changedUrls[0], false);

        $this->actingAs($user)->delete(route('properties.photos.destroy', [$property, $photos[2]]));
        $fallback = $photos[0]->fresh();
        $fallbackUrl = route('properties.photos.thumbnail', [$property, $fallback]);

        $this->assertTrue($fallback->is_primary);
        $this->assertSame($fallback->id, $property->fresh()->load('photos')->photos->first()->id);
        $this->actingAs($user)->get(route('properties.show', $property))
            ->assertSeeInOrder([$fallbackUrl, route('properties.photos.thumbnail', [$property, $photos[1]])], false);
        $this->actingAs($user)->get(route('properties.index'))->assertSee($fallbackUrl, false);
    }

    public function test_invalid_file_is_rejected_with_a_clear_message(): void
    {
        [$user, $property] = $this->propertyContext();

        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [UploadedFile::fake()->create('not-image.jpg', 10, 'text/plain')],
        ])->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('property_photos', 0);
    }

    public function test_photo_validation_error_is_rendered_near_the_uploader(): void
    {
        [$user, $property] = $this->propertyContext();

        $this->from(route('properties.show', $property))
            ->followingRedirects()
            ->actingAs($user)
            ->post(route('properties.photos.store', $property), [
                'photos' => [UploadedFile::fake()->create('not-image.jpg', 10, 'text/plain')],
            ])
            ->assertSeeInOrder(['data-photo-upload', 'รองรับเฉพาะไฟล์ JPG, PNG หรือ WebP'], false);
    }

    public function test_oversized_file_is_rejected(): void
    {
        [$user, $property] = $this->propertyContext();
        $kilobytes = (int) config('photos.max_upload_kb') + 1;

        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [UploadedFile::fake()->create('large.jpg', $kilobytes, 'image/jpeg')],
        ])->assertSessionHasErrors('photos.0');

        $this->assertDatabaseCount('property_photos', 0);
    }

    public function test_free_photo_limit_blocks_new_photos_and_preserves_existing_photos(): void
    {
        [$user, $property] = $this->propertyContext();
        $limit = (int) config('plans.free.limits.photos_per_property');

        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => array_map(fn (int $index): UploadedFile => $this->jpegUpload("photo-{$index}.jpg"), range(1, $limit)),
        ])->assertRedirect();

        $paths = PropertyPhoto::query()->pluck('path')->all();
        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [$this->jpegUpload('too-many.jpg')],
        ])->assertSessionHasErrors('photos');

        $this->assertDatabaseCount('property_photos', $limit);
        foreach ($paths as $path) {
            Storage::disk('local')->assertExists($path);
        }
    }

    public function test_user_can_change_primary_photo(): void
    {
        [$user, $property] = $this->propertyContext();
        $this->uploadTwo($user, $property);
        $photos = PropertyPhoto::query()->orderBy('id')->get();

        $this->actingAs($user)->patch(route('properties.photos.primary', [$property, $photos[1]]))
            ->assertRedirect(route('properties.show', $property));

        $this->assertFalse($photos[0]->fresh()->is_primary);
        $this->assertTrue($photos[1]->fresh()->is_primary);
    }

    public function test_user_can_delete_photo_and_next_photo_becomes_primary(): void
    {
        [$user, $property] = $this->propertyContext();
        $this->uploadTwo($user, $property);
        $photos = PropertyPhoto::query()->orderBy('id')->get();

        $this->actingAs($user)->delete(route('properties.photos.destroy', [$property, $photos[0]]))
            ->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseCount('property_photos', 1);
        $this->assertTrue($photos[1]->fresh()->is_primary);
        Storage::disk('local')->assertMissing($photos[0]->path);
    }

    public function test_photo_routes_are_isolated_between_users(): void
    {
        [$owner, $property] = $this->propertyContext();
        $this->uploadTwo($owner, $property);
        $photo = PropertyPhoto::query()->firstOrFail();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)->patch(route('properties.photos.primary', [$property, $photo]))->assertForbidden();
        $this->actingAs($otherUser)->delete(route('properties.photos.destroy', [$property, $photo]))->assertForbidden();
        $this->actingAs($otherUser)->get(route('properties.photos.file', [$property, $photo]))->assertForbidden();
        $this->assertDatabaseHas('property_photos', ['id' => $photo->id]);
    }

    public function test_guest_cannot_upload_or_read_property_photo(): void
    {
        [$user, $property] = $this->propertyContext();
        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [$this->jpegUpload('guest-test.jpg')],
        ]);
        $photo = PropertyPhoto::query()->firstOrFail();
        Auth::logout();

        $this->post(route('properties.photos.store', $property), [])->assertRedirect();
        $this->get(route('properties.photos.file', [$property, $photo]))->assertRedirect();
    }

    /** @return array{0: User, 1: Property} */
    private function propertyContext(): array
    {
        $user = User::factory()->create();

        return [$user, Property::factory()->for($user)->create()];
    }

    private function uploadTwo(User $user, Property $property): void
    {
        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [$this->jpegUpload('one.jpg'), $this->jpegUpload('two.jpg')],
        ])->assertRedirect();
    }

    private function uploadThree(User $user, Property $property): void
    {
        $this->actingAs($user)->post(route('properties.photos.store', $property), [
            'photos' => [$this->jpegUpload('one.jpg'), $this->jpegUpload('two.jpg'), $this->jpegUpload('three.jpg')],
        ])->assertRedirect();
    }

    private function jpegUpload(string $name): UploadedFile
    {
        $bytes = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAH/AP/EABQQAQAAAAAAAAAAAAAAAAAAACD/2gAIAQEAAT8hP//EABQRAQAAAAAAAAAAAAAAAAAAABD/2gAIAQIBAT8hP//EABQRAQAAAAAAAAAAAAAAAAAAABD/2gAIAQMBAT8hP//Z', true);

        return UploadedFile::fake()->createWithContent($name, $bytes ?: '');
    }
}
