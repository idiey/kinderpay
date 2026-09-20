<?php

namespace App\Http\Controllers;

use App\Models\FeeTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeeTemplateController extends Controller
{
    public function index(): Response
    {
        $templates = FeeTemplate::withCount('studentFees')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('Fees/Templates', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:recurring,one_time',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,yearly,once',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        FeeTemplate::create($validated);

        return redirect()->route('fee-templates.index')->with('success', 'Fee template created successfully.');
    }

    public function update(Request $request, FeeTemplate $feeTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:recurring,one_time',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:monthly,quarterly,yearly,once',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $feeTemplate->update($validated);

        return redirect()->route('fee-templates.index')->with('success', 'Fee template updated successfully.');
    }

    public function destroy(FeeTemplate $feeTemplate): RedirectResponse
    {
        if ($feeTemplate->studentFees()->count() > 0) {
            $feeTemplate->update(['is_active' => false]);
            return redirect()->route('fee-templates.index')->with('success', 'Fee template marked as inactive (in use by students).');
        }

        $feeTemplate->delete();
        return redirect()->route('fee-templates.index')->with('success', 'Fee template deleted successfully.');
    }
}
