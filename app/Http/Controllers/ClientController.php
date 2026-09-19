<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\MatchingService;
use App\Services\PlanService;
use App\Services\PostHogAnalytics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->clients();

        if ($q = trim((string) $request->input('q', ''))) {
            $query->where(function ($b) use ($q): void {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('locations', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            if (in_array($type, ['buy', 'rent'], true)) {
                $query->where('transaction_type', $type);
            }
        }

        return view('clients.index', [
            'clients' => $query->latest()->get(),
            'currentSearch' => $request->input('q', ''),
            'currentType' => $request->input('type', 'all'),
        ]);
    }

    public function create(Request $request, PlanService $planService): View
    {
        $limit = $planService->limit($request->user(), 'clients');
        $clientCount = $request->user()->clients()->count();

        return view('clients.create', [
            'limit' => $limit,
            'limitReached' => $planService->reached($request->user(), 'clients', $clientCount),
        ]);
    }

    public function store(ClientRequest $request, PlanService $planService, PostHogAnalytics $analytics): RedirectResponse
    {
        $limit = $planService->limit($request->user(), 'clients');

        $clientCount = $request->user()->clients()->count();
        if ($planService->reached($request->user(), 'clients', $clientCount)) {
            $analytics->track($request, 'free_limit_reached');

            return to_route('clients.create')
                ->withInput()
                ->with('limit_reached', "แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");
        }

        $client = $request->user()->clients()->create($request->validated());

        if ($clientCount === 0) {
            $analytics->track($request, 'first_client_created');
        }

        return to_route('clients.show', $client)->with('success', 'บันทึกลูกค้าแล้ว');
    }

    public function show(Client $client, MatchingService $matchingService, PostHogAnalytics $analytics): View
    {
        Gate::authorize('view', $client);
        $client->load([
            'followUps' => fn ($query) => $query->latest('due_date'),
            'deals' => fn ($query) => $query->latest(),
        ]);

        $propertyMatches = $matchingService->forClient($client);
        if ($propertyMatches->isNotEmpty()) {
            $analytics->track(request(), 'match_viewed');
        }

        return view('clients.show', [
            'client' => $client,
            'propertyMatches' => $propertyMatches,
        ]);
    }

    public function edit(Client $client): View
    {
        Gate::authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        Gate::authorize('update', $client);
        $client->update($request->validated());

        return to_route('clients.show', $client)->with('success', 'แก้ไขข้อมูลลูกค้าแล้ว');
    }

    public function destroy(Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);
        $client->delete();

        return to_route('clients.index')->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}
