<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAllowance;
use App\Models\AllowanceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Staff::with(['allowances.allowanceType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Staff/Index', [
            'staff' => $staff,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        $allowanceTypes = AllowanceType::orderBy('name')->get();

        return Inertia::render('Staff/Create', [
            'allowanceTypes' => $allowanceTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:50',
            'ic_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'join_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'epf_number' => 'nullable|string|max:30',
            'socso_number' => 'nullable|string|max:30',
            'epf_category' => 'required|in:1,2',
            'address' => 'nullable|string',
        ]);

        $staff = Staff::create($validated);

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff member added successfully.');
    }

    public function show(Staff $staff): Response
    {
        $staff->load([
            'allowances.allowanceType',
            'leaveBalances.leaveType',
            'leaveRequests' => fn($q) => $q->orderBy('id', 'desc')->take(5),
            'payrollItems.payrollRun' => fn($q) => $q->orderBy('id', 'desc')->take(6),
        ]);

        $allowanceTypes = AllowanceType::all();

        return Inertia::render('Staff/Show', [
            'staff' => $staff,
            'allowanceTypes' => $allowanceTypes,
        ]);
    }

    public function edit(Staff $staff): Response
    {
        return Inertia::render('Staff/Edit', [
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:50',
            'ic_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'join_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,resigned,terminated',
            'basic_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'epf_number' => 'nullable|string|max:30',
            'socso_number' => 'nullable|string|max:30',
            'epf_category' => 'required|in:1,2',
            'address' => 'nullable|string',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff updated successfully.');
    }

    public function addAllowance(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ]);

        StaffAllowance::create([
            'staff_id' => $staff->id,
            'allowance_type_id' => $validated['allowance_type_id'],
            'amount' => $validated['amount'],
            'effective_from' => $validated['effective_from'],
        ]);

        return back()->with('success', 'Allowance added.');
    }
}
