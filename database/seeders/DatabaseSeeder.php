<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use App\Models\FeeTemplate;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Kindergarten;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // Seed demo accounts and operational data
        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@kinderpay.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );
        $superAdmin->assignRole('super_admin');

            // 2. Demo Kindergarten
            $kindergarten = Kindergarten::firstOrCreate(
                ['name' => 'Tadika Ceria Demo'],
                [
                    'registration_no' => 'SSM-2026-TDK0019',
                    'address' => 'No 45, Jalan Putra Permai 3, Bandar Putra',
                    'city' => 'Nilai',
                    'state' => 'Negeri Sembilan',
                    'postcode' => '71800',
                    'phone' => '06-8501234',
                    'email' => 'info@ceriademo.edu.my',
                    'invoice_prefix' => 'TCD',
                    'invoice_day' => 1,
                    'payment_gateway' => 'billplz',
                    'timezone' => 'Asia/Kuala_Lumpur',
                ]
            );

            // 3. Admin User
            $admin = User::firstOrCreate(
                ['email' => 'demo@kinderpay.test'],
                [
                    'name' => 'Cikgu Noraini (Principal)',
                    'password' => Hash::make('password'),
                    'kindergarten_id' => $kindergarten->id,
                    'role' => 'admin',
                ]
            );
            $admin->assignRole('admin');

            // 4. Parent User
            $parentUser = User::firstOrCreate(
                ['email' => 'parent@kinderpay.test'],
                [
                    'name' => 'Puan Roslina binti Mansor',
                    'password' => Hash::make('password'),
                    'kindergarten_id' => $kindergarten->id,
                    'role' => 'parent',
                ]
            );
            $parentUser->assignRole('parent');

            // 5. Classes
            $class1 = ClassGroup::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => '4 Tahun Cerdas'],
                ['academic_year' => 2026, 'capacity' => 20]
            );
            $class2 = ClassGroup::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => '5 Tahun Pintar'],
                ['academic_year' => 2026, 'capacity' => 25]
            );
            $class3 = ClassGroup::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => '6 Tahun Bijak'],
                ['academic_year' => 2026, 'capacity' => 25]
            );

            // 6. Fee Templates
            $feeTuition = FeeTemplate::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Monthly Tuition (Yuran Bulanan)'],
                ['type' => 'recurring', 'amount' => 350.00, 'frequency' => 'monthly', 'is_active' => true]
            );
            $feeMeal = FeeTemplate::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Nutritious Meal Plan (Makan & Minum)'],
                ['type' => 'recurring', 'amount' => 80.00, 'frequency' => 'monthly', 'is_active' => true]
            );
            $feeTransport = FeeTemplate::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Transportation Van (Van Sekolah)'],
                ['type' => 'recurring', 'amount' => 120.00, 'frequency' => 'monthly', 'is_active' => true]
            );

            // 7. Students & Guardians
            // Student 1 (Parent User's Child)
            $student1 = Student::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Muhammad Rayyan bin Rosli'],
                [
                    'class_group_id' => $class2->id,
                    'ic_number' => '210612-05-1123',
                    'date_of_birth' => '2021-06-12',
                    'gender' => 'male',
                    'enrollment_date' => '2026-01-02',
                    'status' => 'active',
                    'allergies' => 'Mild peanut allergy',
                ]
            );

            $guardian1 = Guardian::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'phone' => '0123456789'],
                [
                    'user_id' => $parentUser->id,
                    'name' => 'Puan Roslina binti Mansor',
                    'email' => 'parent@kinderpay.test',
                    'relationship' => 'mother',
                    'address' => 'No 18, Jalan Melati 2, Nilai Impian',
                    'is_primary' => true,
                ]
            );
            $student1->guardians()->syncWithoutDetaching([$guardian1->id => ['relationship' => 'mother']]);

            // Assign fees to Student 1
            StudentFee::firstOrCreate(
                ['student_id' => $student1->id, 'fee_template_id' => $feeTuition->id],
                ['effective_from' => '2026-01-01', 'discount_amount' => 0]
            );
            StudentFee::firstOrCreate(
                ['student_id' => $student1->id, 'fee_template_id' => $feeMeal->id],
                ['effective_from' => '2026-01-01', 'discount_amount' => 0]
            );

            // Student 2
            $student2 = Student::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Nur Aisyah binti Khairul'],
                [
                    'class_group_id' => $class3->id,
                    'ic_number' => '200420-10-8764',
                    'date_of_birth' => '2020-04-20',
                    'gender' => 'female',
                    'enrollment_date' => '2026-01-02',
                    'status' => 'active',
                ]
            );
            $guardian2 = Guardian::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'phone' => '0139876543'],
                [
                    'name' => 'Encik Khairul Anuar',
                    'email' => 'khairul@gmail.com',
                    'relationship' => 'father',
                    'address' => 'No 7, Lorong Desa Cemerlang, Nilai',
                    'is_primary' => true,
                ]
            );
            $student2->guardians()->syncWithoutDetaching([$guardian2->id => ['relationship' => 'father']]);

            StudentFee::firstOrCreate(
                ['student_id' => $student2->id, 'fee_template_id' => $feeTuition->id],
                ['effective_from' => '2026-01-01', 'discount_amount' => 30.00, 'discount_reason' => 'Early Bird']
            );

            // Student 3
            $student3 = Student::firstOrCreate(
                ['kindergarten_id' => $kindergarten->id, 'name' => 'Danish Mikael bin Zamri'],
                [
                    'class_group_id' => $class1->id,
                    'ic_number' => '220815-14-5541',
                    'date_of_birth' => '2022-08-15',
                    'gender' => 'male',
                    'enrollment_date' => '2026-01-02',
                    'status' => 'active',
                ]
            );
            StudentFee::firstOrCreate(
                ['student_id' => $student3->id, 'fee_template_id' => $feeTuition->id],
                ['effective_from' => '2026-01-01']
            );
            StudentFee::firstOrCreate(
                ['student_id' => $student3->id, 'fee_template_id' => $feeTransport->id],
                ['effective_from' => '2026-01-01']
            );

            // 8. Sample Invoices for current month
            $currentMonth = Carbon::now()->startOfMonth()->toDateString();
            $dueDate = Carbon::now()->startOfMonth()->addDays(14)->toDateString();

            // Invoice 1: Student 1 (Partially Paid)
            $inv1 = Invoice::firstOrCreate(
                ['invoice_number' => 'TCD-202609-0001'],
                [
                    'kindergarten_id' => $kindergarten->id,
                    'student_id' => $student1->id,
                    'billing_month' => $currentMonth,
                    'subtotal' => 430.00,
                    'discount_total' => 0.00,
                    'total_amount' => 430.00,
                    'paid_amount' => 200.00,
                    'balance_due' => 230.00,
                    'status' => 'partially_paid',
                    'due_date' => $dueDate,
                    'sent_at' => now(),
                ]
            );
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv1->id, 'description' => 'Monthly Tuition (Yuran Bulanan)'],
                ['quantity' => 1, 'unit_price' => 350.00, 'discount' => 0.00, 'total' => 350.00]
            );
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv1->id, 'description' => 'Nutritious Meal Plan (Makan & Minum)'],
                ['quantity' => 1, 'unit_price' => 80.00, 'discount' => 0.00, 'total' => 80.00]
            );

            // Payment for Invoice 1
            Payment::firstOrCreate(
                ['invoice_id' => $inv1->id, 'amount' => 200.00],
                [
                    'kindergarten_id' => $kindergarten->id,
                    'method' => 'fpx',
                    'status' => 'completed',
                    'receipt_number' => 'RCP-202609-0001',
                    'paid_at' => now()->subDays(2),
                    'notes' => 'FPX Online Payment via Maybank',
                ]
            );

            // Invoice 2: Student 2 (Fully Paid)
            $inv2 = Invoice::firstOrCreate(
                ['invoice_number' => 'TCD-202609-0002'],
                [
                    'kindergarten_id' => $kindergarten->id,
                    'student_id' => $student2->id,
                    'billing_month' => $currentMonth,
                    'subtotal' => 350.00,
                    'discount_total' => 30.00,
                    'total_amount' => 320.00,
                    'paid_amount' => 320.00,
                    'balance_due' => 0.00,
                    'status' => 'paid',
                    'due_date' => $dueDate,
                    'sent_at' => now(),
                    'paid_at' => now()->subDay(),
                ]
            );
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv2->id, 'description' => 'Monthly Tuition (Early Bird Discount)'],
                ['quantity' => 1, 'unit_price' => 350.00, 'discount' => 30.00, 'total' => 320.00]
            );
            Payment::firstOrCreate(
                ['invoice_id' => $inv2->id, 'amount' => 320.00],
                [
                    'kindergarten_id' => $kindergarten->id,
                    'method' => 'bank_transfer',
                    'status' => 'completed',
                    'receipt_number' => 'RCP-202609-0002',
                    'reference_number' => 'MBB2026091294',
                    'paid_at' => now()->subDay(),
                    'recorded_by' => $admin->id,
                    'notes' => 'Transferred via CDM machine',
                ]
            );

            // Invoice 3: Student 3 (Sent, Pending payment)
            $inv3 = Invoice::firstOrCreate(
                ['invoice_number' => 'TCD-202609-0003'],
                [
                    'kindergarten_id' => $kindergarten->id,
                    'student_id' => $student3->id,
                    'billing_month' => $currentMonth,
                    'subtotal' => 470.00,
                    'discount_total' => 0.00,
                    'total_amount' => 470.00,
                    'paid_amount' => 0.00,
                    'balance_due' => 470.00,
                    'status' => 'sent',
                    'due_date' => $dueDate,
                    'sent_at' => now(),
                ]
            );
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv3->id, 'description' => 'Monthly Tuition'],
                ['quantity' => 1, 'unit_price' => 350.00, 'discount' => 0.00, 'total' => 350.00]
            );
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv3->id, 'description' => 'Transportation Van (Van Sekolah)'],
                ['quantity' => 1, 'unit_price' => 120.00, 'discount' => 0.00, 'total' => 120.00]
            );

            // ==========================================
            // 11. Seed Branch 2: Tadika Ceria Cawangan Bangi (Multi-Tenant Branch)
            // ==========================================
            $bangiBranch = Kindergarten::firstOrCreate(
                ['name' => 'Tadika Ceria Cawangan Bangi'],
                [
                    'registration_no' => 'SSM-2026-TDK0020',
                    'address' => 'No 12, Seksyen 8, Bandar Baru Bangi',
                    'city' => 'Bangi',
                    'state' => 'Selangor',
                    'postcode' => '43650',
                    'phone' => '03-89215678',
                    'email' => 'bangi@ceriademo.edu.my',
                    'invoice_prefix' => 'TCB',
                    'invoice_day' => 1,
                    'payment_gateway' => 'billplz',
                    'timezone' => 'Asia/Kuala_Lumpur',
                ]
            );

            $bangiClass1 = ClassGroup::firstOrCreate(
                ['kindergarten_id' => $bangiBranch->id, 'name' => '5 Tahun Al-Farabi'],
                ['academic_year' => 2026, 'capacity' => 20]
            );

            $bangiStudent = Student::firstOrCreate(
                ['kindergarten_id' => $bangiBranch->id, 'name' => 'Nur Aina Safiya binti Azman'],
                [
                    'class_group_id' => $bangiClass1->id,
                    'ic_number' => '210815-10-3456',
                    'date_of_birth' => '2021-08-15',
                    'gender' => 'female',
                    'enrollment_date' => '2026-01-05',
                    'status' => 'active',
                ]
            );

            $bangiFee = FeeTemplate::firstOrCreate(
                ['kindergarten_id' => $bangiBranch->id, 'name' => 'Monthly Tuition (Yuran Bulanan)'],
                ['type' => 'recurring', 'amount' => 380.00, 'frequency' => 'monthly', 'is_active' => true]
            );
    }
}
