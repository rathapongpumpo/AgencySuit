<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TodayController extends Controller
{
    public function index(Request $request): View
    {
        $today = today();
        $user = $request->user();

        return view('today', [
            'overdueFollowUps' => $user->followUps()->with('client')->where('status', 'pending')->whereDate('due_date', '<', $today)->orderBy('due_date')->get(),
            'todayFollowUps' => $user->followUps()->with('client')->where('status', 'pending')->whereDate('due_date', $today)->orderBy('due_date')->get(),
            'todayAppointments' => $user->appointments()->with(['client', 'property'])->where('status', 'scheduled')->whereDate('appointment_date', $today)->orderBy('appointment_time')->get(),
        ]);
    }
}
