<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function ics(Appointment $appointment): Response
    {
        Gate::authorize('view', $appointment);
        $appointment->load(['client', 'property']);

        $dateStr = $appointment->appointment_date instanceof \DateTimeInterface
            ? $appointment->appointment_date->format('Y-m-d')
            : (string) $appointment->appointment_date;
        $timeStr = (string) $appointment->appointment_time;

        $start = \Carbon\Carbon::parse("{$dateStr} {$timeStr}");
        $end = (clone $start)->addHour();

        $title = 'นัดดู: '.($appointment->property?->name ?? 'ทรัพย์');
        $location = $appointment->property?->location ?? '';
        $description = 'ลูกค้า: '.($appointment->client?->name ?? '-').($appointment->client?->phone ? ' ('.$appointment->client->phone.')' : '').($appointment->notes ? "\nหมายเหตุ: ".$appointment->notes : '');

        $ics = "BEGIN:VCALENDAR\r\n"
            ."VERSION:2.0\r\n"
            ."PRODID:-//AgencySuit//Appointments//TH\r\n"
            ."CALSCALE:GREGORIAN\r\n"
            ."METHOD:PUBLISH\r\n"
            ."BEGIN:VEVENT\r\n"
            ."UID:appointment-{$appointment->id}@agencysuit\r\n"
            ."DTSTAMP:".gmdate('Ymd\THis\Z')."\r\n"
            ."DTSTART:".$start->utc()->format('Ymd\THis\Z')."\r\n"
            ."DTEND:".$end->utc()->format('Ymd\THis\Z')."\r\n"
            ."SUMMARY:".addcslashes($title, ",;\\")."\r\n"
            ."LOCATION:".addcslashes($location, ",;\\")."\r\n"
            ."DESCRIPTION:".str_replace(["\r\n", "\n"], "\\n", addcslashes($description, ",;\\"))."\r\n"
            ."STATUS:CONFIRMED\r\n"
            ."END:VEVENT\r\n"
            ."END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="appointment-'.$appointment->id.'.ics"',
        ]);
    }
}
