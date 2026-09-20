<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\FeeTemplate;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentFeeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Student::with(['classGroup', 'studentFees.feeTemplate'])
            ->where('status', 'active');

        if ($request->filled('class_id')) {
            $query->where('class_group_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $students = $query->orderBy('name')->paginate(15)->withQueryString();
        $classes = ClassGroup::orderBy('name')->get();
        $templates = FeeTemplate::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Fees/Assign', [
            'students' => $students,
            'classes' => $classes,
            'templates' => $templates,
            'filters' => $request->only(['class_id', 'search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'class_group_id' => 'nullable|exists:class_groups,id',
            'fee_template_id' => 'required|exists:fee_templates,id',
            'custom_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        // If bulk assigning to class
        if (!empty($validated['class_group_id'])) {
            $students = Student::where('class_group_id', $validated['class_group_id'])
                ->where('status', 'active')
                ->get();

            foreach ($students as $student) {
                StudentFee::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'fee_template_id' => $validated['fee_template_id'],
                    ],
                    [
                        'custom_amount' => $validated['custom_amount'] ?? null,
                        'discount_amount' => $validated['discount_amount'] ?? 0,
                        'discount_reason' => $validated['discount_reason'] ?? null,
                        'effective_from' => $validated['effective_from'],
                        'effective_to' => $validated['effective_to'] ?? null,
                    ]
                );
            }

            return back()->with('success', "Assigned fee template to {$students->count()} students in class.");
        }

        if (empty($validated['student_id'])) {
            return back()->with('error', 'Please select either a student or a class.');
        }

        StudentFee::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'fee_template_id' => $validated['fee_template_id'],
            ],
            [
                'custom_amount' => $validated['custom_amount'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'effective_from' => $validated['effective_from'],
                'effective_to' => $validated['effective_to'] ?? null,
            ]
        );

        return back()->with('success', 'Fee assigned to student successfully.');
    }

    public function destroy(StudentFee $studentFee): RedirectResponse
    {
        $studentFee->delete();
        return back()->with('success', 'Assigned fee removed.');
    }
}
