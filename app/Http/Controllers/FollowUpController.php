<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowUpRequest;
use App\Models\Client;
use App\Models\FollowUp;
use Carbon\Carbon;
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

    public function storeQuickAdd(FollowUpRequest $request): RedirectResponse
    {
        $client = $request->user()->clients()->findOrFail($request->validated('client_id'));
        $this->save($request, $client);

        return to_route('clients.show', $client)->with('success', 'ตั้งเวลาติดตามแล้ว');
    }

    public function store(FollowUpRequest $request, Client $client): RedirectResponse
    {
        Gate::authorize('view', $client);
        $this->save($request, $client);

        return to_route('clients.show', $client)->with('success', 'ตั้งเวลาติดตามแล้ว');
    }

    private function save(FollowUpRequest $request, Client $client): void
    {
        $data = $request->validated();
        $dueDate = isset($data['days']) ? today()->addDays((int) $data['days']) : Carbon::parse($data['due_date']);
        $request->user()->followUps()->create(['client_id' => $client->id, 'due_date' => $dueDate->toDateString(), 'note' => $data['note'] ?? null, 'status' => 'pending']);
    }

    public function complete(FollowUp $followUp): RedirectResponse
    {
        Gate::authorize('update', $followUp);
        $followUp->update(['status' => 'completed']);

        return back()->with('success', 'ทำรายการติดตามแล้ว');
    }
}
