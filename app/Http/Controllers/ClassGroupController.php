<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassGroupController extends Controller
{
    public function index(): Response
    {
        $classes = ClassGroup::withCount('students')
            ->orderBy('academic_year', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2035',
            'capacity' => 'nullable|integer|min:1|max:100',
        ]);

        ClassGroup::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function update(Request $request, ClassGroup $classGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'academic_year' => 'required|integer|min:2020|max:2035',
            'capacity' => 'nullable|integer|min:1|max:100',
        ]);

        $classGroup->update($validated);

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassGroup $classGroup): RedirectResponse
    {
        if ($classGroup->students()->count() > 0) {
            return back()->with('error', 'Cannot delete class with enrolled students.');
        }

        $classGroup->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
