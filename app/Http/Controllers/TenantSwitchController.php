<?php

namespace App\Http\Controllers;

use App\Models\Kindergarten;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSwitchController extends Controller
{
    /**
     * Switch active kindergarten tenant session for authorized users.
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kindergarten_id' => 'required|exists:kindergartens,id',
        ]);

        $user = Auth::user();

        // Super admins can switch to any kindergarten; regular staff can only access their assigned branch
        if (!$user->hasRole('super_admin') && $user->role !== 'super_admin') {
            if ($user->kindergarten_id != $validated['kindergarten_id']) {
                abort(403, 'You do not have permission to switch to this kindergarten branch.');
            }
        }

        session(['active_kindergarten_id' => (int) $validated['kindergarten_id']]);

        $target = Kindergarten::find($validated['kindergarten_id']);

        return back()->with('success', "Switched branch context to {$target->name}.");
    }
}
