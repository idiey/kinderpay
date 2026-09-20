<?php

namespace Tests\Feature;

use App\Models\Kindergarten;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\User;
use App\Services\LeaveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAndLeaveTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kindergarten $kindergarten;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create(['name' => 'Tadika Damai']);
        $this->admin = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_create_staff(): void
    {
        $response = $this->actingAs($this->admin)->post(route('staff.store'), [
            'name' => 'Cikgu Aisyah',
            'employee_id' => 'STF-001',
            'ic_number' => '940512-10-5432',
            'gender' => 'female',
            'phone' => '0123456789',
            'position' => 'Teacher',
            'employment_type' => 'full_time',
            'join_date' => '2026-01-01',
            'basic_salary' => 2500.00,
            'epf_category' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('staff', [
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Cikgu Aisyah',
            'position' => 'Teacher',
        ]);
    }

    public function test_leave_application_and_approval_workflow(): void
    {
        $staff = Staff::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Ustaz Hafiz',
            'gender' => 'male',
            'phone' => '0198765432',
            'position' => 'Teacher',
            'employment_type' => 'full_time',
            'join_date' => '2026-01-01',
            'basic_salary' => 2200.00,
            'epf_category' => 1,
        ]);

        $leaveType = LeaveType::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Annual Leave',
            'code' => 'AL',
            'is_paid' => true,
        ]);

        // Submit leave
        $leaveService = app(LeaveService::class);
        $req = $leaveService->applyLeave($staff, [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-09-08',
            'end_date' => '2026-09-09',
            'reason' => 'Personal matters',
        ]);

        $this->assertEquals('pending', $req->status);
        $this->assertEquals(2.0, (float) $req->days);

        // Approve leave
        $reviewed = $leaveService->reviewLeave($req, 'approve', 'Approved by principal', $this->admin->id);
        $this->assertEquals('approved', $reviewed->status);
    }
}
