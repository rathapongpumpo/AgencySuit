<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
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

    public function create(Request $request): View
    {
        $limit = (int) config('plans.free.limits.clients');

        return view('clients.create', [
            'limit' => $limit,
            'limitReached' => $request->user()->clients()->count() >= $limit,
        ]);
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        $limit = (int) config('plans.free.limits.clients');

        if ($request->user()->clients()->count() >= $limit) {
            return to_route('clients.create')
                ->withInput()
                ->with('limit_reached', "แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");
        }

        $client = $request->user()->clients()->create($request->validated());

        return to_route('clients.show', $client)->with('success', 'บันทึกลูกค้าแล้ว');
    }

    public function show(Client $client): View
    {
        Gate::authorize('view', $client);

        return view('clients.show', compact('client'));
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
}
