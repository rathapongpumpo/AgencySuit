<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowUpRequest;
use App\Models\Client;
use App\Models\FollowUp;
use App\Services\PostHogAnalytics;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function create(Request $request): View
    {
        return view('followups.create', ['clients' => $request->user()->clients()->orderBy('name')->get()]);
    }

    public function storeQuickAdd(FollowUpRequest $request, PostHogAnalytics $analytics): RedirectResponse|JsonResponse
    {
        $client = $request->user()->clients()->findOrFail($request->validated('client_id'));
        [$followUp, $created] = $this->save($request, $client);
        if ($created) {
            $analytics->track($request, 'followup_created');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ตั้งเวลาติดตามแล้ว',
                'follow_up' => [
                    'id' => $followUp->id,
                    'client_id' => $client->id,
                    'due_date' => $followUp->due_date->format('d/m/Y'),
                    'due_date_raw' => $followUp->due_date->toDateString(),
                    'note' => $followUp->note,
                    'status' => $followUp->status,
                ],
                'posthog_events' => $analytics->pullEvents($request),
            ]);
        }

        return to_route('clients.show', $client)->with('success', 'ตั้งเวลาติดตามแล้ว');
    }

    public function store(FollowUpRequest $request, Client $client, PostHogAnalytics $analytics): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $client);
        [$followUp, $created] = $this->save($request, $client);
        if ($created) {
            $analytics->track($request, 'followup_created');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ตั้งเวลาติดตามแล้ว',
                'follow_up' => [
                    'id' => $followUp->id,
                    'client_id' => $client->id,
                    'due_date' => $followUp->due_date->format('d/m/Y'),
                    'due_date_raw' => $followUp->due_date->toDateString(),
                    'note' => $followUp->note,
                    'status' => $followUp->status,
                ],
                'posthog_events' => $analytics->pullEvents($request),
            ]);
        }

        return to_route('clients.show', $client)->with('success', 'ตั้งเวลาติดตามแล้ว');
    }

    /** @return array{0: FollowUp, 1: bool} */
    private function save(FollowUpRequest $request, Client $client): array
    {
        $data = $request->validated();
        $dueDate = isset($data['days']) ? today()->addDays((int) $data['days']) : Carbon::parse($data['due_date']);
        $dateStr = $dueDate->toDateString();
        $note = ! empty($data['note']) ? trim((string) $data['note']) : null;

        // Deduplicate: If there is already a pending follow-up on this date for this client, update it instead of creating duplicates
        $existing = $request->user()->followUps()
            ->where('client_id', $client->id)
            ->where('status', 'pending')
            ->whereDate('due_date', $dateStr)
            ->first();

        if ($existing) {
            if ($note !== null) {
                $existing->update(['note' => $note]);
            }

            return [$existing, false];
        }

        return [$request->user()->followUps()->create([
            'client_id' => $client->id,
            'due_date' => $dateStr,
            'note' => $note,
            'status' => 'pending',
        ]), true];
    }

    public function complete(Request $request, FollowUp $followUp): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $followUp);
        $followUp->update(['status' => 'completed']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ทำรายการติดตามแล้ว',
                'id' => $followUp->id,
            ]);
        }

        return back()->with('success', 'ทำรายการติดตามแล้ว');
    }

    public function destroy(Request $request, FollowUp $followUp): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $followUp);
        $followUp->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบรายการติดตามแล้ว',
                'id' => $followUp->id,
            ]);
        }

        return back()->with('success', 'ลบรายการติดตามแล้ว');
    }

    public function destroyAll(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $client);
        $client->followUps()->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ล้างประวัติติดตามทั้งหมดของลูกค้านี้เรียบร้อยแล้ว',
            ]);
        }

        return back()->with('success', 'ล้างประวัติติดตามทั้งหมดของลูกค้านี้เรียบร้อยแล้ว');
    }
}
