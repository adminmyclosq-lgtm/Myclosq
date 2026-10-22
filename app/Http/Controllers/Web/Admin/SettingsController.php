<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Admin & Staff Users Management.
     */
    public function users(Request $request)
    {
        $users = User::with(['roles', 'customerProfile'])
            ->whereHas('roles')
            ->orWhereIn('id', [1, 2])
            ->latest('id')
            ->paginate(20);

        $availableRoles = Role::where('code', '!=', 'CUSTOMER')->get();

        return view('admin.settings.users', compact('users', 'availableRoles'));
    }

    /**
     * Create / Invite a new Admin or Staff Member.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'email' => strtolower(trim($validated['email'])),
                'mobile' => $validated['mobile'] ?? null,
                'password_hash' => Hash::make($validated['password']),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $firstName = trim($validated['first_name']);
            $lastName = trim($validated['last_name'] ?? '');
            $displayName = trim("$firstName $lastName");

            $user->customerProfile()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'display_name' => $displayName ?: $firstName,
            ]);

            $user->roles()->sync([$validated['role_id']]);
        });

        return back()->with('success', "Staff user '{$validated['email']}' successfully created.");
    }

    /**
     * Update Staff Role & Status.
     */
    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($user->id === 1 && $validated['status'] === 'inactive') {
            return back()->with('error', 'Cannot deactivate primary Super Administrator.');
        }

        $user->roles()->sync([$validated['role_id']]);
        $user->update(['status' => $validated['status']]);

        return back()->with('success', "User '{$user->email}' updated successfully.");
    }

    /**
     * Roles & Permissions Matrix.
     */
    public function roles(Request $request)
    {
        $roles = Role::where('code', '!=', 'CUSTOMER')->with(['permissions', 'users'])->get();
        $permissions = Permission::all();

        return view('admin.settings.roles', compact('roles', 'permissions'));
    }

    /**
     * Sync interactive permissions matrix for roles.
     */
    public function syncRolesPermissions(Request $request)
    {
        $matrix = $request->input('matrix', []);
        $allPermissions = Permission::pluck('id')->toArray();

        DB::transaction(function () use ($matrix, $allPermissions) {
            $roles = Role::where('code', '!=', 'CUSTOMER')->get();
            foreach ($roles as $role) {
                if ($role->code === 'SUPER_ADMIN') {
                    // Super admin maintains all privileges
                    $role->permissions()->sync($allPermissions);
                } else {
                    $selected = isset($matrix[$role->id]) && is_array($matrix[$role->id])
                        ? array_map('intval', array_keys($matrix[$role->id]))
                        : [];
                    $role->permissions()->sync($selected);
                }
            }
        });

        return back()->with('success', 'Role permissions matrix saved successfully.');
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
            'cache_driver' => config('cache.default', 'file'),
            'queue_connection' => config('queue.default', 'sync'),
            'session_driver' => config('session.driver', 'file'),
            'memory_limit' => ini_get('memory_limit') ?: 'N/A',
            'max_upload' => ini_get('upload_max_filesize') ?: 'N/A',
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Web Server',
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
