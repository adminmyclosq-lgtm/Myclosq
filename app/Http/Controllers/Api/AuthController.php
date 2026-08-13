<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function register(RegisterRequest $request)
    {
        return response()->json($this->auth->register($request->validated()),201);
    }

    public function login(LoginRequest $request)
    {
        return response()->json($this->auth->login($request->string('identifier')->toString(),$request->string('password')->toString()));
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles','customerProfile','addresses'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
