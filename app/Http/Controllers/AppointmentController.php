<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function create(Request $request): View
    {
        return view('appointments.create', [
            'clients' => $request->user()->clients()->orderBy('name')->get(),
            'properties' => $request->user()->properties()->orderBy('name')->get(),
            'selectedClient' => $request->integer('client_id'),
            'selectedProperty' => $request->integer('property_id'),
        ]);
    }

    public function store(AppointmentRequest $request): RedirectResponse
    {
        $client = $request->user()->clients()->findOrFail($request->validated('client_id'));
        $property = $request->user()->properties()->findOrFail($request->validated('property_id'));
        $appointment = $request->user()->appointments()->create([...$request->validated(), 'status' => 'scheduled']);

        return to_route('appointments.show', $appointment)->with('success', 'สร้างนัดดูแล้ว');
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);
        $appointment->load(['client', 'property']);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('update', $appointment);

        return view('appointments.edit', [
            'appointment' => $appointment,
            'clients' => request()->user()->clients()->orderBy('name')->get(),
            'properties' => request()->user()->properties()->orderBy('name')->get(),
        ]);
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('update', $appointment);
        $request->user()->clients()->findOrFail($request->validated('client_id'));
        $request->user()->properties()->findOrFail($request->validated('property_id'));
        $appointment->update($request->validated());

        return to_route('appointments.show', $appointment)->with('success', 'แก้ไขนัดดูแล้ว');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $appointment);
        $appointment->update(['status' => 'cancelled']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกนัดดูแล้ว',
            ]);
        }

        return to_route('appointments.show', $appointment)->with('success', 'ยกเลิกนัดดูแล้ว');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('delete', $appointment);
        $appointment->delete();

        return to_route('today')->with('success', 'ลบนัดดูเรียบร้อยแล้ว');
    }
}
