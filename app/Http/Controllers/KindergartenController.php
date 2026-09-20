<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\FeeTemplate;
use App\Models\Kindergarten;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KindergartenController extends Controller
{
    /**
     * Display listing of all kindergartens.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->role === 'super_admin';

        $query = Kindergarten::withCount(['students', 'staff', 'classGroups', 'invoices']);

        if (!$isSuperAdmin && $user->kindergarten_id) {
            $query->where('id', $user->kindergarten_id);
        }

        $kindergartens = $query->orderBy('id', 'asc')->get();

        $activeKindergartenId = $user->activeKindergartenId();

        return Inertia::render('Kindergartens/Index', [
            'kindergartens' => $kindergartens,
            'activeKindergartenId' => $activeKindergartenId,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Store a newly created kindergarten branch.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super_admin') && $user->role !== 'super_admin') {
            abort(403, 'Only Super Administrators can create new kindergarten branches.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'postcode' => 'nullable|string|max:10',
            'invoice_prefix' => 'required|string|max:10|unique:kindergartens,invoice_prefix',
            'invoice_day' => 'nullable|integer|min:1|max:31',
            'payment_gateway' => 'required|string|in:billplz,manual,toyyibpay',
        ]);

        $validated['invoice_day'] = $validated['invoice_day'] ?? 1;
        $validated['timezone'] = 'Asia/Kuala_Lumpur';

        $kindergarten = Kindergarten::create($validated);

        // Seed basic default class groups for the new branch
        ClassGroup::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => '4 Tahun (Junior)',
            'academic_year' => (int) date('Y'),
            'capacity' => 20,
        ]);
        ClassGroup::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => '5 Tahun (Standard)',
            'academic_year' => (int) date('Y'),
            'capacity' => 25,
        ]);
        ClassGroup::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => '6 Tahun (Senior)',
            'academic_year' => (int) date('Y'),
            'capacity' => 25,
        ]);

        // Seed standard fee templates for new branch
        FeeTemplate::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => 'Monthly Tuition (Yuran Bulanan)',
            'type' => 'recurring',
            'amount' => 350.00,
            'frequency' => 'monthly',
            'is_active' => true,
        ]);
        FeeTemplate::create([
            'kindergarten_id' => $kindergarten->id,
            'name' => 'Meal Plan (Makan & Minum)',
            'type' => 'recurring',
            'amount' => 80.00,
            'frequency' => 'monthly',
            'is_active' => true,
        ]);

        // Automatically switch active context to the newly created branch
        session(['active_kindergarten_id' => $kindergarten->id]);

        return redirect()->route('kindergartens.index')
            ->with('success', "Branch '{$kindergarten->name}' registered successfully and set as active.");
    }

    /**
     * Update specified kindergarten branch.
     */
    public function update(Request $request, Kindergarten $kindergarten): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super_admin') && $user->role !== 'super_admin' && $user->kindergarten_id !== $kindergarten->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'postcode' => 'nullable|string|max:10',
            'invoice_prefix' => 'required|string|max:10|unique:kindergartens,invoice_prefix,' . $kindergarten->id,
            'invoice_day' => 'nullable|integer|min:1|max:31',
            'payment_gateway' => 'required|string',
            'gateway_api_key' => 'nullable|string',
            'gateway_collection_id' => 'nullable|string|max:100',
        ]);

        $kindergarten->update($validated);

        return back()->with('success', "Branch '{$kindergarten->name}' updated successfully.");
    }
}
