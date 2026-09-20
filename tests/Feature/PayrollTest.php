<?php

namespace Tests\Feature;

use App\Models\Kindergarten;
use App\Models\PayrollRun;
use App\Models\Staff;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kindergarten $kindergarten;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create(['name' => 'Tadika Sejahtera']);
        $this->admin = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_payroll_generation_calculates_statutory_deductions(): void
    {
        $staff = Staff::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Teacher Sarah',
            'gender' => 'female',
            'phone' => '01122334455',
            'position' => 'Teacher',
            'employment_type' => 'full_time',
            'join_date' => '2026-01-01',
            'basic_salary' => 3000.00,
            'epf_category' => 1,
        ]);

        $payrollService = app(PayrollService::class);
        $run = $payrollService->generatePayrollRun($this->kindergarten, 9, 2026);

        $this->assertEquals('draft', $run->status);
        $this->assertEquals(1, $run->items()->count());

        $item = $run->items()->first();
        $this->assertEquals(3000.00, (float) $item->basic_salary);
        $this->assertEquals(3000.00, (float) $item->gross_pay);

        // EPF 11% of 3000 = 330.00
        $this->assertEquals(330.00, (float) $item->epf_employee);
        // EPF employer 13% of 3000 = 390.00
        $this->assertEquals(390.00, (float) $item->epf_employer);
        // SOCSO employee 0.5% = 15.00
        $this->assertEquals(15.00, (float) $item->socso_employee);
        // EIS employee 0.2% = 6.00
        $this->assertEquals(6.00, (float) $item->eis_employee);

        // Net pay = 3000 - 330 - 15 - 6 = 2649.00
        $this->assertEquals(2649.00, (float) $item->net_pay);

        // Confirm run
        $confirmedRun = $payrollService->confirmPayrollRun($run, $this->admin->id);
        $this->assertEquals('confirmed', $confirmedRun->status);
    }
}
