<?php

namespace App\Services;

use App\Models\Kindergarten;
use App\Models\PayrollAllowanceItem;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function __construct(
        protected StatutoryService $statutory,
        protected LeaveService $leave
    ) {}

    /**
     * Run monthly payroll for all active staff in a kindergarten.
     */
    public function generatePayrollRun(Kindergarten $kindergarten, int $month, int $year): PayrollRun
    {
        return DB::transaction(function () use ($kindergarten, $month, $year) {
            $existingRun = PayrollRun::withoutGlobalScopes()
                ->where('kindergarten_id', $kindergarten->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingRun && $existingRun->status === 'confirmed') {
                throw new \Exception("Payroll run for {$month}/{$year} is already confirmed and locked.");
            }

            $payrollRun = $existingRun ?? PayrollRun::create([
                'kindergarten_id' => $kindergarten->id,
                'month' => $month,
                'year' => $year,
                'status' => 'draft',
            ]);

            // Clear old items if re-running a draft
            if ($existingRun) {
                $payrollRun->items()->delete();
            }

            $activeStaff = Staff::withoutGlobalScopes()
                ->where('kindergarten_id', $kindergarten->id)
                ->where('status', 'active')
                ->with(['allowances.allowanceType'])
                ->get();

            // Total working days in month (standard 26 days or working days count)
            $totalDaysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $workingDaysInMonth = 26.0;

            foreach ($activeStaff as $staff) {
                $basicSalary = (float) $staff->basic_salary;

                // Allowances
                $totalAllowances = 0.0;
                $allowanceBreakdown = [];
                foreach ($staff->allowances as $allowance) {
                    $amt = (float) $allowance->amount;
                    $totalAllowances += $amt;
                    $allowanceBreakdown[] = [
                        'allowance_type_id' => $allowance->allowance_type_id,
                        'amount' => $amt,
                    ];
                }

                $grossPay = $basicSalary + $totalAllowances;

                // Unpaid leave deduction
                $unpaidDays = $this->leave->getUnpaidLeaveDays($staff, $month, $year);
                $dailyRate = $basicSalary / $workingDaysInMonth;
                $unpaidDeduction = round($dailyRate * $unpaidDays, 2);

                $adjustedGross = max(0, $grossPay - $unpaidDeduction);

                // Statutory calculations
                $epfEmployee = $this->statutory->epfEmployee($adjustedGross, $staff->epf_category);
                $epfEmployer = $this->statutory->epfEmployer($adjustedGross, $staff->epf_category);
                $socsoEmployee = $this->statutory->socsoEmployee($adjustedGross);
                $socsoEmployer = $this->statutory->socsoEmployer($adjustedGross);
                $eisEmployee = $this->statutory->eisEmployee($adjustedGross);
                $eisEmployer = $this->statutory->eisEmployer($adjustedGross);
                $pcbAmount = 0.0; // manual or table

                $totalDeductions = $epfEmployee + $socsoEmployee + $eisEmployee + $pcbAmount;
                $netPay = max(0, $adjustedGross - $totalDeductions);

                $payrollItem = PayrollItem::create([
                    'payroll_run_id' => $payrollRun->id,
                    'staff_id' => $staff->id,
                    'basic_salary' => $basicSalary,
                    'total_allowances' => $totalAllowances,
                    'overtime_amount' => 0.0,
                    'bonus_amount' => 0.0,
                    'gross_pay' => $grossPay,
                    'unpaid_leave_days' => $unpaidDays,
                    'unpaid_leave_deduction' => $unpaidDeduction,
                    'adjusted_gross' => $adjustedGross,
                    'epf_employee' => $epfEmployee,
                    'epf_employer' => $epfEmployer,
                    'socso_employee' => $socsoEmployee,
                    'socso_employer' => $socsoEmployer,
                    'eis_employee' => $eisEmployee,
                    'eis_employer' => $eisEmployer,
                    'pcb_amount' => $pcbAmount,
                    'other_deductions' => 0.0,
                    'total_deductions' => $totalDeductions,
                    'net_pay' => $netPay,
                ]);

                foreach ($allowanceBreakdown as $ab) {
                    $ab['payroll_item_id'] = $payrollItem->id;
                    PayrollAllowanceItem::create($ab);
                }
            }

            $payrollRun->recalculateTotals();

            return $payrollRun->fresh(['items.staff']);
        });
    }

    /**
     * Confirm and lock payroll run.
     */
    public function confirmPayrollRun(PayrollRun $run, int $userId): PayrollRun
    {
        $run->update([
            'status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
        ]);

        return $run->fresh();
    }
}
