<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        return view('feedback.create');
    }

    public function store(FeedbackRequest $request): RedirectResponse
    {
        $request->user()->feedback()->create([...$request->validated(), 'route' => $request->input('route', url()->previous()), 'status' => 'new']);

        return to_route('feedback.create')->with('success', 'ส่งความคิดเห็นแล้ว ขอบคุณที่ช่วยพัฒนา AgencySuit');
    }
}
