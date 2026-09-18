<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount(['properties', 'clients', 'deals']);

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->input('plan')) {
            if (in_array($plan, ['free', 'pro'], true)) {
                $query->where('plan', $plan);
            }
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();

        $counts = [
            'total' => User::count(),
            'free' => User::where('plan', 'free')->count(),
            'pro' => User::where('plan', 'pro')->count(),
            'admin' => User::where('is_admin', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function updatePlan(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['nullable', 'string', 'in:free,pro'],
        ]);

        $newPlan = $validated['plan'] ?? ($user->plan === 'pro' ? 'free' : 'pro');
        $user->update(['plan' => $newPlan]);

        return back()->with('success', "อัปเดตแผนของ {$user->name} เป็น ".strtoupper($newPlan).' เรียบร้อยแล้ว');
    }
}
