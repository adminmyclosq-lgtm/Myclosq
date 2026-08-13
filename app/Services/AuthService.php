<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        $customerRole = Role::where('code','CUSTOMER')->first();
        if ($customerRole) {
            $user->roles()->syncWithoutDetaching([$customerRole->id]);
        }

        $user->customerProfile()->create([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'display_name' => $data['display_name'] ?? ($data['first_name'] ?? $user->email),
            'whatsapp_opt_in' => (bool) ($data['whatsapp_opt_in'] ?? false),
            'whatsapp_opt_in_at' => !empty($data['whatsapp_opt_in']) ? now() : null,
            'marketing_opt_in' => (bool) ($data['marketing_opt_in'] ?? false),
        ]);

        $token = $user->createToken('gutreset-web')->plainTextToken;

        return compact('user','token');
    }

    public function login(string $identifier, string $password): array
    {
        $user = User::where('email',$identifier)
            ->orWhere('mobile',$identifier)
            ->first();

        if (!$user || !Hash::check($password, $user->password_hash) || $user->status !== 'active') {
            throw ValidationException::withMessages(['identifier' => 'Invalid credentials.']);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('gutreset-web')->plainTextToken;

        return compact('user','token');
    }
}
