<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user()?->getRoleNames() ?? [],
                'permissions' => $request->user()?->getAllPermissions()->pluck('name') ?? [],
                'kindergarten' => $request->user()?->activeKindergarten() ?? $request->user()?->kindergarten,
                'available_kindergartens' => ($request->user()?->hasRole('super_admin') || $request->user()?->role === 'super_admin')
                    ? \App\Models\Kindergarten::select('id', 'name', 'city', 'state', 'invoice_prefix')->get()
                    : ($request->user()?->kindergarten ? [$request->user()->kindergarten] : []),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
