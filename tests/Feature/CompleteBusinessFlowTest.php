<?php

namespace Tests\Feature;

use App\Models\FeeTemplate;
use App\Models\Invoice;
use App\Models\Kindergarten;
use App\Models\Payment;
use App\Models\PayrollRun;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\LeaveService;
use App\Services\PaymentService;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteBusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_kindergarten_operations_and_financial_lifecycle(): void
    {
        // 0. Seed Roles
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // 1. Setup Kindergarten & Admin
        $kindergarten = Kindergarten::create([
            'name' => 'Tadika Murni Impian',
            'invoice_prefix' => 'TMI',
        ]);

        $admin = User::factory()->create([
            'kindergarten_id' => $kindergarten->id,
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // 2. Setup Staff Member
        $staff = Staff::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => 'Cikgu Zulaikha',
            'gender' => 'female',
            'phone' => '0123334444',
            'position' => 'Head Teacher',
            'employment_type' => 'full_time',
            'join_date' => '2026-01-01',
            'basic_salary' => 2800.00,
            'epf_category' => 1,
        ]);

        // 3. Setup Student and Tuition Fee Template
        $student = Student::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => 'Ahmad Uwais',
            'date_of_birth' => '2021-05-10',
            'gender' => 'male',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $feeTemplate = FeeTemplate::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => 'Monthly Tuition Standard',
            'amount' => 360.00,
            'frequency' => 'monthly',
            'type' => 'recurring',
            'is_active' => true,
        ]);

        StudentFee::create([
            'student_id' => $student->id,
            'fee_template_id' => $feeTemplate->id,
            'discount_amount' => 20.00,
            'discount_reason' => 'Promotion Rebate',
            'effective_from' => '2026-01-01',
        ]);

        // 4. Run Monthly Invoicing Batch
        $invoiceService = app(InvoiceService::class);
        $count = $invoiceService->generateMonthlyInvoices($kindergarten, '2026-09-01');
        $this->assertEquals(1, $count);

        $invoice = Invoice::where('student_id', $student->id)->whereDate('billing_month', '2026-09-01')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(340.00, (float) $invoice->total_amount);
        $this->assertEquals(340.00, (float) $invoice->balance_due);
        $this->assertEquals('draft', $invoice->status);

        // 5. Send Invoice to Parent & Record Payment
        $invoice->update(['status' => 'sent']);
        $paymentService = app(PaymentService::class);
        $payment = $paymentService->recordManualPayment($invoice, [
            'amount' => 340.00,
            'method' => 'bank_transfer',
            'reference_number' => 'MBB-TRX-99881',
            'paid_at' => '2026-09-05',
        ], $admin->id);

        $this->assertEquals('completed', $payment->status);
        $this->invoice = $invoice->fresh();
        $this->assertEquals('paid', $this->invoice->status);
        $this->assertEquals(0.00, (float) $this->invoice->balance_due);

        // 6. Run Monthly Staff Payroll
        $payrollService = app(PayrollService::class);
        $payrollRun = $payrollService->generatePayrollRun($kindergarten, 9, 2026);
        $this->assertEquals('draft', $payrollRun->status);

        $payrollItem = $payrollRun->items()->where('staff_id', $staff->id)->first();
        $this->assertNotNull($payrollItem);
        // EPF 11% of 2800 = 308.00
        $this->assertEquals(308.00, (float) $payrollItem->epf_employee);
        // Net pay = 2800 - 308 - 14(socso) - 5.60(eis) = 2472.40
        $this->assertEquals(2472.40, (float) $payrollItem->net_pay);

        // 7. Confirm Payroll
        $payrollService->confirmPayrollRun($payrollRun, $admin->id);
        $this->assertEquals('confirmed', $payrollRun->fresh()->status);

        // 8. Verify Financial Reporting / Dashboard Metrics
        $totalCollected = Payment::where('status', 'completed')->sum('amount');
        $this->assertEquals(340.00, (float) $totalCollected);

        $totalPayroll = PayrollRun::where('status', 'confirmed')->sum('total_net');
        $this->assertEquals(2472.40, (float) $totalPayroll);
    }
}
