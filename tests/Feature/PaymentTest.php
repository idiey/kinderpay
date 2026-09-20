<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Kindergarten;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kindergarten $kindergarten;
    protected Student $student;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create([
            'name' => 'Tadika Indah',
        ]);

        $this->admin = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');

        $this->student = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Rayyan Mikael',
            'date_of_birth' => '2021-07-11',
            'gender' => 'male',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $this->invoice = Invoice::create([
            'kindergarten_id' => $this->kindergarten->id,
            'student_id' => $this->student->id,
            'invoice_number' => 'INV-202609-0001',
            'billing_month' => '2026-09-01',
            'subtotal' => 400.00,
            'discount_total' => 0.00,
            'total_amount' => 400.00,
            'paid_amount' => 0.00,
            'balance_due' => 400.00,
            'status' => 'sent',
            'due_date' => '2026-09-15',
        ]);
    }

    public function test_admin_can_record_manual_payment(): void
    {
        $response = $this->actingAs($this->admin)->post(route('payments.store', $this->invoice->id), [
            'amount' => 200.00,
            'method' => 'cash',
            'paid_at' => '2026-09-10',
            'reference_number' => 'CASH-001',
            'notes' => 'Half payment for September',
        ]);

        $response->assertRedirect(route('invoices.show', $this->invoice->id));

        $this->invoice->refresh();
        $this->assertEquals(200.00, (float) $this->invoice->paid_amount);
        $this->assertEquals(200.00, (float) $this->invoice->balance_due);
        $this->assertEquals('partially_paid', $this->invoice->status);

        $payment = Payment::where('invoice_id', $this->invoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertStringStartsWith('RCP-', $payment->receipt_number);
    }

    public function test_full_payment_marks_invoice_as_paid(): void
    {
        $this->actingAs($this->admin)->post(route('payments.store', $this->invoice->id), [
            'amount' => 400.00,
            'method' => 'bank_transfer',
            'paid_at' => '2026-09-10',
            'reference_number' => 'FT260910001',
        ]);

        $this->invoice->refresh();
        $this->assertEquals(400.00, (float) $this->invoice->paid_amount);
        $this->assertEquals(0.00, (float) $this->invoice->balance_due);
        $this->assertEquals('paid', $this->invoice->status);
        $this->assertNotNull($this->invoice->paid_at);
    }

    public function test_billplz_webhook_callback_updates_invoice(): void
    {
        $payment = Payment::create([
            'kindergarten_id' => $this->kindergarten->id,
            'invoice_id' => $this->invoice->id,
            'amount' => 400.00,
            'method' => 'fpx',
            'status' => 'pending',
            'gateway_ref' => 'bill_test_12345',
        ]);

        $response = $this->postJson(route('api.webhooks.billplz'), [
            'id' => 'bill_test_12345',
            'paid' => 'true',
            'paid_at' => '2026-09-10 10:00:00',
            'amount' => 40000, // cents
        ]);

        $response->assertOk();

        $payment->refresh();
        $this->assertEquals('completed', $payment->status);

        $this->invoice->refresh();
        $this->assertEquals('paid', $this->invoice->status);
        $this->assertEquals(0.00, (float) $this->invoice->balance_due);
    }
}
