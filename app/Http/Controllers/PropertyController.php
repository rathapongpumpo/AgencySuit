<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyPhotoRequest;
use App\Http\Requests\PropertyRequest;
use App\Http\Requests\PropertyStatusRequest;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Services\MatchingService;
use App\Services\PlanService;
use App\Services\PropertyPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->properties()->with(['photos', 'primaryPhoto']);

        if ($q = trim((string) $request->input('q', ''))) {
            $query->where(function ($b) use ($q): void {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('unit_number', 'like', "%{$q}%")
                    ->orWhere('owner_name', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            if (in_array($type, ['sale', 'rent'], true)) {
                $query->where('transaction_type', $type);
            }
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['available', 'reserved', 'sold', 'paused'], true)) {
                $query->where('status', $status);
            }
        }

        return view('properties.index', [
            'properties' => $query->latest()->get(),
            'currentSearch' => $request->input('q', ''),
            'currentType' => $request->input('type', 'all'),
            'currentStatus' => $request->input('status', 'all'),
        ]);
    }

    public function create(Request $request, PlanService $planService): View
    {
        $limit = $planService->limit($request->user(), 'properties');
        $propertyCount = $request->user()->properties()->count();

        return view('properties.create', [
            'limit' => $limit,
            'limitReached' => $planService->reached($request->user(), 'properties', $propertyCount),
        ]);
    }

    public function store(PropertyRequest $request, PlanService $planService): RedirectResponse
    {
        $limit = $planService->limit($request->user(), 'properties');
        $propertyCount = $request->user()->properties()->count();

        if ($planService->reached($request->user(), 'properties', $propertyCount)) {
            return to_route('properties.create')
                ->withInput()
                ->with('limit_reached', "แพ็กเกจฟรีเพิ่มทรัพย์ได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");
        }

        $property = $request->user()->properties()->create([
            ...$request->validated(),
            'status' => Property::DEFAULT_STATUS,
        ]);

        return to_route('properties.show', $property)->with('success', 'บันทึกทรัพย์แล้ว');
    }

    public function show(Property $property, MatchingService $matchingService, PlanService $planService): View
    {
        Gate::authorize('view', $property);
        $property->load(['photos', 'primaryPhoto']);

        return view('properties.show', [
            'property' => $property,
            'statusOptions' => config('properties.statuses', []),
            'clientMatches' => $matchingService->forProperty($property),
            'photoLimit' => $planService->limit(request()->user(), 'photos_per_property'),
        ]);
    }

    public function edit(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('properties.edit', compact('property'));
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);
        $property->update($request->validated());

        return to_route('properties.show', $property)->with('success', 'แก้ไขทรัพย์แล้ว');
    }

    public function destroy(Property $property): RedirectResponse
    {
        Gate::authorize('delete', $property);

        foreach ($property->photos as $photo) {
            Storage::disk('local')->delete(array_filter([$photo->path, $photo->thumbnail_path]));
        }

        $property->delete();

        return to_route('properties.index')->with('success', 'ลบทรัพย์เรียบร้อยแล้ว');
    }

    public function updateStatus(PropertyStatusRequest $request, Property $property): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $property);
        $property->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'เปลี่ยนสถานะแล้ว',
                'status' => $property->status,
                'status_label' => $property->status_label,
            ]);
        }

        return to_route('properties.show', $property)->with('success', 'เปลี่ยนสถานะแล้ว');
    }

    public function storePhoto(PropertyPhotoRequest $request, Property $property, PropertyPhotoService $photoService, PlanService $planService): RedirectResponse
    {
        Gate::authorize('update', $property);

        $files = array_values($request->file('photos', []));
        $limit = $planService->limit($request->user(), 'photos_per_property');
        $existing = $property->photos()->count();

        if ($limit !== null && $existing + count($files) > $limit) {
            return back()->withInput()->withErrors([
                'photos' => "แพ็กเกจฟรีเก็บรูปได้สูงสุด {$limit} รูปต่อทรัพย์ รูปเดิมยังอยู่ครบ",
            ]);
        }

        $photoService->storeMany(
            $property,
            $files,
            $property->photos()->where('is_primary', true)->exists(),
        );

        return to_route('properties.show', $property)->with('success', 'เพิ่มรูปแล้ว');
    }

    public function setPrimaryPhoto(Request $request, Property $property, PropertyPhoto $photo): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $property);
        $photo = $this->ownedPhoto($property, $photo);

        $property->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'เปลี่ยนภาพหลักแล้ว',
                'photo_id' => $photo->id,
            ]);
        }

        return to_route('properties.show', $property)->with('success', 'เปลี่ยนภาพหลักแล้ว');
    }

    public function destroyPhoto(Request $request, Property $property, PropertyPhoto $photo): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $property);
        $photo = $this->ownedPhoto($property, $photo);
        $wasPrimary = $photo->is_primary;

        Storage::disk('local')->delete(array_filter([$photo->path, $photo->thumbnail_path]));
        $photo->delete();

        $newPrimaryId = null;
        if ($wasPrimary) {
            $newPrimary = $property->photos()->where('is_primary', false)->orderBy('id')->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
                $newPrimaryId = $newPrimary->id;
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบรูปแล้ว',
                'photo_id' => $photo->id,
                'new_primary_id' => $newPrimaryId,
            ]);
        }

        return to_route('properties.show', $property)->with('success', 'ลบรูปแล้ว');
    }

    public function showPhoto(Property $property, PropertyPhoto $photo): mixed
    {
        Gate::authorize('view', $property);
        $photo = $this->ownedPhoto($property, $photo);

        return $this->photoResponse($photo, $photo->path);
    }

    public function showPhotoThumbnail(Property $property, PropertyPhoto $photo): mixed
    {
        Gate::authorize('view', $property);
        $photo = $this->ownedPhoto($property, $photo);

        return $this->photoResponse($photo, $photo->thumbnail_path ?? $photo->path);
    }

    private function photoResponse(PropertyPhoto $photo, string $path): mixed
    {

        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $photo->mime_type,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    private function ownedPhoto(Property $property, PropertyPhoto $photo): PropertyPhoto
    {
        abort_unless($photo->property_id === $property->id && $photo->user_id === request()->user()->id, 404);

        return $photo;
    }
}
