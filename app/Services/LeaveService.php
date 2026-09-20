<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PublicHoliday;
use App\Models\Staff;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    /**
     * Calculate working days between two dates, excluding weekends and public holidays.
     */
    public function calculateWorkingDays(string $startDate, string $endDate, bool $isHalfDay = false): float
    {
        if ($isHalfDay) {
            return 0.5;
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $holidays = PublicHoliday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $days = 0;
        foreach ($period as $date) {
            // Check weekend (Saturday = 6, Sunday = 0)
            if ($date->isWeekend()) {
                continue;
            }
            // Check public holiday
            if (in_array($date->toDateString(), $holidays)) {
                continue;
            }
            $days++;
        }

        return (float) $days;
    }

    /**
     * Apply for leave.
     */
    public function applyLeave(Staff $staff, array $data): LeaveRequest
    {
        return DB::transaction(function () use ($staff, $data) {
            $leaveType = LeaveType::findOrFail($data['leave_type_id']);
            $startDate = Carbon::parse($data['start_date'])->toDateString();
            $endDate = Carbon::parse($data['end_date'])->toDateString();
            $isHalfDay = (bool) ($data['is_half_day'] ?? false);

            $days = $this->calculateWorkingDays($startDate, $endDate, $isHalfDay);
            $year = Carbon::parse($startDate)->year;

            // Check balance if paid leave
            if ($leaveType->is_paid) {
                $balance = LeaveBalance::firstOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                    ],
                    [
                        'entitled' => 12.0, // default
                        'carried_forward' => 0.0,
                        'used' => 0.0,
                        'pending' => 0.0,
                        'balance' => 12.0,
                    ]
                );

                if ($balance->balance < $days) {
                    throw new \Exception("Insufficient leave balance. Remaining: {$balance->balance} days, Requested: {$days} days.");
                }

                $balance->pending += $days;
                $balance->save();
            }

            return LeaveRequest::create([
                'kindergarten_id' => $staff->kindergarten_id,
                'staff_id' => $staff->id,
                'leave_type_id' => $leaveType->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'is_half_day' => $isHalfDay,
                'half_day_period' => $data['half_day_period'] ?? null,
                'reason' => $data['reason'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Approve or reject leave request.
     */
    public function reviewLeave(LeaveRequest $request, string $action, ?string $notes = null, ?int $reviewerId = null): LeaveRequest
    {
        return DB::transaction(function () use ($request, $action, $notes, $reviewerId) {
            $year = Carbon::parse($request->start_date)->year;
            $leaveType = $request->leaveType;

            if ($request->status !== 'pending') {
                throw new \Exception("Leave request is already {$request->status}.");
            }

            $request->status = $action === 'approve' ? 'approved' : 'rejected';
            $request->reviewed_by = $reviewerId;
            $request->reviewed_at = now();
            $request->review_notes = $notes;
            $request->save();

            if ($leaveType->is_paid) {
                $balance = LeaveBalance::where('staff_id', $request->staff_id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $year)
                    ->first();

                if ($balance) {
                    $balance->pending = max(0, $balance->pending - $request->days);
                    if ($action === 'approve') {
                        $balance->used += $request->days;
                        $balance->recalculate();
                    } else {
                        $balance->save();
                    }
                }
            }

            return $request;
        });
    }

    /**
     * Get unpaid leave days for a staff in a given month and year.
     */
    public function getUnpaidLeaveDays(Staff $staff, int $month, int $year): float
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        return (float) LeaveRequest::where('staff_id', $staff->id)
            ->where('status', 'approved')
            ->whereHas('leaveType', fn($q) => $q->where('is_paid', false))
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->sum('days');
    }
}
