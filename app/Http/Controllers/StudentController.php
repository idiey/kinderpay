<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Student::with(['classGroup', 'guardians']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_group_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString();
        $classes = ClassGroup::orderBy('name')->get();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'status']),
        ]);
    }

    public function create(): Response
    {
        $classes = ClassGroup::orderBy('name')->get();

        return Inertia::render('Students/Create', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'class_group_id' => 'nullable|exists:class_groups,id',
            'enrollment_date' => 'required|date',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            // Guardian details
            'guardian_name' => 'required|string|max:255',
            'guardian_relationship' => 'required|in:father,mother,guardian,other',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $student = Student::create([
                'name' => $validated['name'],
                'ic_number' => $validated['ic_number'] ?? null,
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'class_group_id' => $validated['class_group_id'] ?? null,
                'enrollment_date' => $validated['enrollment_date'],
                'allergies' => $validated['allergies'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
                'status' => 'active',
            ]);

            // Find or create guardian
            $guardian = Guardian::firstOrCreate(
                [
                    'phone' => $validated['guardian_phone'],
                ],
                [
                    'name' => $validated['guardian_name'],
                    'relationship' => $validated['guardian_relationship'],
                    'email' => $validated['guardian_email'] ?? null,
                    'address' => $validated['guardian_address'] ?? null,
                    'is_primary' => true,
                ]
            );

            $student->guardians()->attach($guardian->id, [
                'relationship' => $validated['guardian_relationship'],
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Student enrolled successfully.');
    }

    public function show(Student $student): Response
    {
        $student->load([
            'classGroup',
            'guardians',
            'studentFees.feeTemplate',
            'invoices' => function ($q) {
                $q->orderBy('billing_month', 'desc')->take(12);
            },
        ]);

        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }

    public function edit(Student $student): Response
    {
        $student->load(['classGroup', 'guardians']);
        $classes = ClassGroup::orderBy('name')->get();

        return Inertia::render('Students/Edit', [
            'student' => $student,
            'classes' => $classes,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'class_group_id' => 'nullable|exists:class_groups,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,withdrawn,graduated',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student->id)->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student removed successfully.');
    }
}
