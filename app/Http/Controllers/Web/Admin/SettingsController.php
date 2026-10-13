<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Admin & Staff Users Management.
     */
    public function users(Request $request)
    {
        $users = User::with('roles')
            ->whereHas('roles')
            ->orWhereIn('id', [1, 2])
            ->latest('id')
            ->paginate(20);

        $availableRoles = Role::all();

        return view('admin.settings.users', compact('users', 'availableRoles'));
    }

    /**
     * Roles & Permissions Matrix.
     */
    public function roles(Request $request)
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all();

        return view('admin.settings.roles', compact('roles', 'permissions'));
    }

    /**
     * System Settings & Configuration.
     */
    public function system(Request $request)
    {
        $systemInfo = [
            'app_name' => config('app.name', 'Guided Wellness'),
            'app_env' => config('app.env', 'production'),
            'app_debug' => config('app.debug', false),
            'app_url' => config('app.url'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone', 'Asia/Kolkata'),
            'currency' => 'INR (₹)',
            'db_connection' => config('database.default'),
            'mail_mailer' => config('mail.default'),
        ];

        return view('admin.settings.system', compact('systemInfo'));
    }

    /**
     * Clear application caches.
     */
    public function clearCache(Request $request)
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');

        return back()->with('success', 'Application caches cleared successfully.');
    }
}
