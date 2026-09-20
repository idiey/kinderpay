<?php

namespace App\Http\Controllers;

use App\Models\Kindergarten;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $kindergarten = Auth::user()->kindergarten ?? new Kindergarten();

        return Inertia::render('Settings/Index', [
            'kindergarten' => $kindergarten,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $kindergarten = Auth::user()->kindergarten;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'postcode' => 'nullable|string|max:10',
            'invoice_prefix' => 'required|string|max:10',
            'payment_gateway' => 'required|string|max:50',
            'gateway_api_key' => 'nullable|string',
            'gateway_collection_id' => 'nullable|string|max:100',
        ]);

        if ($kindergarten) {
            $kindergarten->update($validated);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
