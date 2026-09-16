<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Http\Requests\PropertyStatusRequest;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        return view('properties.index', [
            'properties' => $request->user()->properties()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $limit = (int) config('plans.free.limits.properties');
        $propertyCount = $request->user()->properties()->count();

        return view('properties.create', [
            'limit' => $limit,
            'limitReached' => $propertyCount >= $limit,
        ]);
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        $limit = (int) config('plans.free.limits.properties');
        $propertyCount = $request->user()->properties()->count();

        if ($propertyCount >= $limit) {
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

    public function show(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('properties.show', [
            'property' => $property,
            'statusOptions' => config('properties.statuses', []),
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

    public function updateStatus(PropertyStatusRequest $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);
        $property->update($request->validated());

        return to_route('properties.show', $property)->with('success', 'เปลี่ยนสถานะแล้ว');
    }
}
