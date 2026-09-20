<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(LoginRequest $request)
    {
        $result=$this->auth->login($request->string('identifier')->toString(),$request->string('password')->toString());
        Auth::login($result['user'], false);
        $request->session()->regenerate();

        if (session()->has('pending_reset_qr')) {
            $qrCode = session()->pull('pending_reset_qr');
            return redirect()->route('reset.activate', ['qrCode' => $qrCode]);
        }

        $isAdmin = $result['user']->roles()->whereIn('code', [
            'SUPER_ADMIN','CONTENT_ADMIN','PRODUCT_ADMIN','ORDER_ADMIN','FULFILMENT_ADMIN',
            'CUSTOMER_SUPPORT','MARKETING_ADMIN','RESET_ADMIN','ANALYST'
        ])->exists();

        if ($isAdmin) {
            return redirect()->intended(route('admin.dashboard'))->with('success','Welcome back.');
        }

        return redirect()->intended(route('home'))->with('success','Welcome back.');
    }

    public function register(RegisterRequest $request)
    {
        $result=$this->auth->register($request->validated());
        Auth::login($result['user'], false);
        $request->session()->regenerate();
        return redirect()->route('account')->with('success','Your account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
