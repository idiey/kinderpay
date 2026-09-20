<?php

namespace Tests\Feature;

use App\Models\FeeTemplate;
use App\Models\Invoice;
use App\Models\Kindergarten;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kindergarten $kindergarten;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create([
            'name' => 'Tadika Mentari',
            'invoice_prefix' => 'TDM',
        ]);

        $this->admin = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_create_fee_template(): void
    {
        $response = $this->actingAs($this->admin)->post(route('fee-templates.store'), [
            'name' => 'Monthly Tuition Standard',
            'type' => 'recurring',
            'amount' => 380.00,
            'frequency' => 'monthly',
            'description' => 'Inclusive of basic worksheets',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('fee-templates.index'));
        $this->assertDatabaseHas('fee_templates', [
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Monthly Tuition Standard',
            'amount' => 380.00,
        ]);
    }

    public function test_batch_monthly_invoice_generation(): void
    {
        $template = FeeTemplate::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Monthly Tuition',
            'type' => 'recurring',
            'amount' => 350.00,
            'frequency' => 'monthly',
            'is_active' => true,
        ]);

        $student = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Ahmad Faiz',
            'date_of_birth' => '2021-02-14',
            'gender' => 'male',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        StudentFee::create([
            'student_id' => $student->id,
            'fee_template_id' => $template->id,
            'discount_amount' => 50.00,
            'discount_reason' => 'Staff Child',
            'effective_from' => '2026-01-01',
        ]);

        $invoiceService = app(InvoiceService::class);
        $count = $invoiceService->generateMonthlyInvoices($this->kindergarten, '2026-09-01');

        $this->assertEquals(1, $count);

        $invoice = Invoice::where('student_id', $student->id)
            ->whereDate('billing_month', '2026-09-01')
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals(350.00, (float) $invoice->subtotal);
        $this->assertEquals(50.00, (float) $invoice->discount_total);
        $this->assertEquals(300.00, (float) $invoice->total_amount);
        $this->assertEquals(300.00, (float) $invoice->balance_due);
        $this->assertStringStartsWith('TDM-202609-', $invoice->invoice_number);
    }

    public function test_admin_can_create_ad_hoc_invoice(): void
    {
        $student = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Sarah Wong',
            'date_of_birth' => '2021-03-22',
            'gender' => 'female',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'student_id' => $student->id,
            'billing_month' => '2026-09-01',
            'due_date' => '2026-09-15',
            'notes' => 'Concert outfit & photography package',
            'items' => [
                [
                    'description' => 'Concert Costume',
                    'quantity' => 1,
                    'unit_price' => 80.00,
                    'discount' => 0.00,
                ],
                [
                    'description' => 'Photo Album',
                    'quantity' => 2,
                    'unit_price' => 35.00,
                    'discount' => 10.00,
                ],
            ]
        ]);

        $invoice = Invoice::where('student_id', $student->id)->latest()->first();
        $this->assertNotNull($invoice);
        // Subtotal: 80 + 70 = 150. Discount: 10. Total: 140.
        $this->assertEquals(150.00, (float) $invoice->subtotal);
        $this->assertEquals(10.00, (float) $invoice->discount_total);
        $this->assertEquals(140.00, (float) $invoice->total_amount);
        $this->assertEquals(140.00, (float) $invoice->balance_due);
    }
}
