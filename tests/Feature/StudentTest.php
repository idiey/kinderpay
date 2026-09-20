<?php

namespace Tests\Feature;

use App\Models\ClassGroup;
use App\Models\Kindergarten;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kindergarten $kindergarten;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->kindergarten = Kindergarten::create([
            'name' => 'Tadika Bintang',
        ]);

        $this->admin = User::factory()->create([
            'kindergarten_id' => $this->kindergarten->id,
            'role' => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_students_directory(): void
    {
        Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Nur Aina',
            'date_of_birth' => '2021-04-10',
            'gender' => 'female',
            'enrollment_date' => '2026-01-05',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('students.index'));
        $response->assertOk();
    }

    public function test_admin_can_enroll_student_with_guardian(): void
    {
        $class = ClassGroup::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => '5 Cerdik',
            'academic_year' => 2026,
            'capacity' => 25,
        ]);

        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Mohd Danish',
            'ic_number' => '210515-10-8888',
            'date_of_birth' => '2021-05-15',
            'gender' => 'male',
            'class_group_id' => $class->id,
            'enrollment_date' => '2026-01-02',
            'allergies' => 'None',
            'guardian_name' => 'Faridah binti Osman',
            'guardian_relationship' => 'mother',
            'guardian_phone' => '0129876543',
            'guardian_email' => 'faridah@test.com',
            'guardian_address' => 'No 12, Jalan Indah, Nilai',
        ]);

        $response->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Mohd Danish',
            'class_group_id' => $class->id,
        ]);

        $this->assertDatabaseHas('guardians', [
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Faridah binti Osman',
            'phone' => '0129876543',
        ]);
    }

    public function test_admin_can_update_student_particulars(): void
    {
        $student = Student::create([
            'kindergarten_id' => $this->kindergarten->id,
            'name' => 'Siti Khadijah',
            'date_of_birth' => '2021-08-20',
            'gender' => 'female',
            'enrollment_date' => '2026-01-05',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put(route('students.update', $student->id), [
            'name' => 'Siti Khadijah Updated',
            'date_of_birth' => '2021-08-20',
            'gender' => 'female',
            'enrollment_date' => '2026-01-05',
            'status' => 'graduated',
        ]);

        $response->assertRedirect(route('students.show', $student->id));
        $this->assertEquals('Siti Khadijah Updated', $student->fresh()->name);
        $this->assertEquals('graduated', $student->fresh()->status);
    }
}
