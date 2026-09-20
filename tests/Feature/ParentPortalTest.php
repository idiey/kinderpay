<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Kindergarten;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected Kindergarten $kindergarten;
    protected User $parentUser;
    protected Student $myChild;
    protected Student $otherChild;
    protected Invoice $myInvoice;
    protected Invoice $otherInvoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create([
            'name' => 'Tadika Kasih',
        ]);

        $this->parentUser = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'email' => 'parent.kasih@test.com',
            'role' => 'parent',
        ]);
        $this->parentUser->assignRole('parent');

        $this->myChild = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Imanina',
            'date_of_birth' => '2021-06-01',
            'gender' => 'female',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $guardian = Guardian::create([
            'kindergarten_id' => $this->kindergarten->id,
            'user_id' => $this->parentUser->id,
            'name' => 'Puan Aminah',
            'phone' => '0191234567',
            'email' => 'parent.kasih@test.com',
            'relationship' => 'mother',
        ]);

        $this->myChild->guardians()->attach($guardian->id, ['relationship' => 'mother']);

        $this->myInvoice = Invoice::create([
            'kindergarten_id' => $this->kindergarten->id,
            'student_id' => $this->myChild->id,
            'invoice_number' => 'INV-MY-001',
            'billing_month' => '2026-09-01',
            'subtotal' => 350.00,
            'discount_total' => 0.00,
            'total_amount' => 350.00,
            'paid_amount' => 0.00,
            'balance_due' => 350.00,
            'status' => 'sent',
            'due_date' => '2026-09-15',
        ]);

        // Other child & invoice
        $this->otherChild = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Other Kid',
            'date_of_birth' => '2021-01-01',
            'gender' => 'male',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $this->otherInvoice = Invoice::create([
            'kindergarten_id' => $this->kindergarten->id,
            'student_id' => $this->otherChild->id,
            'invoice_number' => 'INV-OTHER-001',
            'billing_month' => '2026-09-01',
            'subtotal' => 350.00,
            'total_amount' => 350.00,
            'paid_amount' => 0.00,
            'balance_due' => 350.00,
            'status' => 'sent',
            'due_date' => '2026-09-15',
        ]);
    }

    public function test_parent_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('parent.dashboard'));
        $response->assertOk();
    }

    public function test_parent_can_view_own_invoices(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('parent.invoices'));
        $response->assertOk();
    }

    public function test_parent_can_view_own_invoice_detail(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('parent.invoices.show', $this->myInvoice->id));
        $response->assertOk();
    }

    public function test_parent_cannot_view_other_students_invoice(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('parent.invoices.show', $this->otherInvoice->id));
        $response->assertForbidden();
    }
}
