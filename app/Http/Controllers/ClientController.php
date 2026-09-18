<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\MatchingService;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        return view('clients.index', [
            'clients' => $request->user()->clients()->latest()->get(),
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

    public function store(ClientRequest $request, PlanService $planService): RedirectResponse
    {
        $limit = $planService->limit($request->user(), 'clients');

        if ($planService->reached($request->user(), 'clients', $request->user()->clients()->count())) {
            return to_route('clients.create')
                ->withInput()
                ->with('limit_reached', "แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");
        }

        $client = $request->user()->clients()->create($request->validated());

        return to_route('clients.show', $client)->with('success', 'บันทึกลูกค้าแล้ว');
    }

    public function show(Client $client, MatchingService $matchingService): View
    {
        Gate::authorize('view', $client);
        $client->load([
            'followUps' => fn ($query) => $query->latest('due_date'),
            'deals' => fn ($query) => $query->latest(),
        ]);

        return view('clients.show', [
            'client' => $client,
            'propertyMatches' => $matchingService->forClient($client),
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
