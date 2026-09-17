<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use App\Models\Client;
use App\Models\Deal;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DealController extends Controller
{
    public function create(Request $request, Client $client): View
    {
        Gate::authorize('view', $client);

        return view('deals.create', ['client' => $client, 'properties' => $request->user()->properties()->orderBy('name')->get(), 'stages' => config('deals.stages', [])]);
    }

    public function store(DealRequest $request, Client $client, PlanService $planService): RedirectResponse
    {
        Gate::authorize('view', $client);
        $limit = $planService->limit($request->user(), 'active_deals');
        $active = $request->user()->deals()->where('stage', '!=', 'closed')->count();
        if ($planService->reached($request->user(), 'active_deals', $active)) {
            return to_route('clients.deals.create', $client)->withInput()->with('limit_reached', "แพ็กเกจของคุณมีดีลที่กำลังดำเนินการได้สูงสุด {$limit} รายการ");
        }
        $propertyId = $request->validated('property_id');
        if ($propertyId !== null) {
            $request->user()->properties()->findOrFail($propertyId);
        }
        $data = $request->validated();
        $data['client_id'] = $client->id;
        $data['co_agent_split'] = $data['co_agent_split'] ?? 0;
        $data['closed_at'] = ($data['stage'] ?? 'new') === 'closed' ? now() : null;
        $deal = $request->user()->deals()->create($data);

        return to_route('deals.show', $deal)->with('success', 'สร้างดีลแล้ว');
    }

    public function show(Deal $deal): View
    {
        Gate::authorize('view', $deal);
        $deal->load(['client', 'property']);

        return view('deals.show', compact('deal'));
    }

    public function edit(Deal $deal): View
    {
        Gate::authorize('update', $deal);

        return view('deals.edit', ['deal' => $deal, 'properties' => request()->user()->properties()->orderBy('name')->get(), 'stages' => config('deals.stages', [])]);
    }

    public function update(DealRequest $request, Deal $deal): RedirectResponse
    {
        Gate::authorize('update', $deal);
        $newStage = $request->validated('stage');
        if ($deal->stage === 'closed' && $newStage !== 'closed') {
            $limit = app(PlanService::class)->limit($request->user(), 'active_deals');
            $active = $request->user()->deals()->where('stage', '!=', 'closed')->count();
            if (app(PlanService::class)->reached($request->user(), 'active_deals', $active)) {
                return to_route('deals.edit', $deal)->withInput()->with('limit_reached', "แพ็กเกจของคุณมีดีลที่กำลังดำเนินการได้สูงสุด {$limit} รายการ");
            }
        }
        if (($propertyId = $request->validated('property_id')) !== null) {
            $request->user()->properties()->findOrFail($propertyId);
        }
        $data = $request->validated();
        $data['co_agent_split'] = $data['co_agent_split'] ?? 0;
        $data['closed_at'] = ($data['stage'] ?? $deal->stage) === 'closed' ? ($deal->closed_at ?? now()) : null;
        $deal->update($data);

        return to_route('deals.show', $deal)->with('success', 'แก้ไขดีลแล้ว');
    }
}
