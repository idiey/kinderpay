<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PublicHoliday;
use App\Models\Staff;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function index(Request $request): Response
    {
        $user = Auth::user();
        $query = LeaveRequest::with(['staff', 'leaveType', 'reviewer']);

        // If teacher, show only own requests
        if ($user->hasRole('teacher') && !$user->hasRole(['admin', 'super_admin'])) {
            $staff = Staff::where('user_id', $user->id)->first();
            $query->where('staff_id', $staff?->id ?? 0);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $leaveTypes = LeaveType::orderBy('name')->get();
        $staffList = Staff::where('status', 'active')->orderBy('name')->get();
        $holidays = PublicHoliday::orderBy('date')->get();

        return Inertia::render('Leave/Index', [
            'requests' => $requests,
            'leaveTypes' => $leaveTypes,
            'staffList' => $staffList,
            'holidays' => $holidays,
            'filters' => $request->only(['status']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_half_day' => 'boolean',
            'half_day_period' => 'nullable|in:morning,afternoon',
            'reason' => 'nullable|string',
        ]);

        try {
            $staff = Staff::findOrFail($validated['staff_id']);
            $this->leaveService->applyLeave($staff, $validated);

            return redirect()->route('leave.index')->with('success', 'Leave application submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function review(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'review_notes' => 'nullable|string',
        ]);

        try {
            $this->leaveService->reviewLeave(
                $leaveRequest,
                $validated['action'],
                $validated['review_notes'] ?? null,
                Auth::id()
            );

            return back()->with('success', "Leave request marked as {$leaveRequest->fresh()->status}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
