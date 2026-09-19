<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $query = Feedback::with('user');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search): void {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('route', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['new', 'reviewed', 'planned', 'done'], true)) {
                $query->where('status', $status);
            }
        }

        if ($type = $request->input('type')) {
            if (in_array($type, ['ใช้งานยาก', 'เจอปัญหา', 'อยากให้เพิ่ม'], true)) {
                $query->where('type', $type);
            }
        }

        $feedbacks = $query->latest('id')->paginate(15)->withQueryString();

        $counts = [
            'total' => Feedback::count(),
            'new' => Feedback::where('status', 'new')->count(),
            'reviewed' => Feedback::where('status', 'reviewed')->count(),
            'planned' => Feedback::where('status', 'planned')->count(),
            'done' => Feedback::where('status', 'done')->count(),
        ];

        return view('admin.feedback.index', compact('feedbacks', 'counts'));
    }

    public function updateStatus(Request $request, Feedback $feedback): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,reviewed,planned,done'],
        ]);

        $feedback->update(['status' => $validated['status']]);

        $statusLabels = [
            'new' => 'ใหม่',
            'reviewed' => 'รับเรื่องแล้ว',
            'planned' => 'วางแผนทำ',
            'done' => 'เสร็จสิ้น',
        ];

        $label = $statusLabels[$validated['status']] ?? $validated['status'];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "เปลี่ยนสถานะข้อเสนอแนะเป็น '{$label}' เรียบร้อยแล้ว",
                'status' => $validated['status'],
                'label' => $label,
            ]);
        }

        return back()->with('success', "เปลี่ยนสถานะข้อเสนอแนะเป็น '{$label}' เรียบร้อยแล้ว");
    }

    public function destroy(Request $request, Feedback $feedback): RedirectResponse|JsonResponse
    {
        $feedback->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อเสนอแนะเรียบร้อยแล้ว',
            ]);
        }

        return redirect()->route('admin.feedback.index')->with('success', 'ลบข้อเสนอแนะเรียบร้อยแล้ว');
    }
}
