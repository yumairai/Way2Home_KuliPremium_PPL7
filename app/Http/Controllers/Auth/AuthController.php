<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => 'required|string|min:8|confirmed',
            'phone_number' => 'required',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'customer',
            'phone_number' => $request->phone_number,
        ]);

        // relasi customer
        Customer::create([
            'user_id' => $user->id
        ]);

        // login otomatis
        Auth::login($user);
        $request->session()->regenerate();

        // 🔥 kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message'  => 'Register berhasil, cek email untuk verifikasi.',
            'redirect' => route('verification.notice') // ⬅️ BUKAN dashboard lagi
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'message' => 'Email atau Password Salah'
            ], 401);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // 🔥 CEK VERIFIKASI EMAIL DULU
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message'  => 'Email belum diverifikasi',
                'redirect' => route('verification.notice')
            ], 200);
        }

        // 🔥 kalau SUDAH verifikasi → lanjut role
        $dashboardRoute = $this->dashboardRouteForRole($user?->role);
        $dashboardUrl   = route($dashboardRoute);

        if ($user->role === 'admin') {
            return response()->json([
                'message'  => 'Login Admin Berhasil',
                'redirect' => $dashboardUrl
            ], 200);
        }

        if ($user->role === 'mandor') {
            return response()->json([
                'message'  => 'Login Mandor Berhasil',
                'redirect' => $dashboardUrl
            ], 200);
        }

        return response()->json([
            'message'  => 'Login Berhasil',
            'redirect' => $dashboardUrl
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda telah logout',
                'redirect' => route('home'),
            ]);
        }

        return redirect()->route('home')->with('message', 'Anda telah logout');
    }

    public function index()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRouteForRole(Auth::user()->role));
        }
        return view('customer-layouts.dashboard');
    }

    private function dashboardRouteForRole(?string $role): string
    {
        return match ($role) {
            'admin' => 'admin.dashboard',
            'mandor' => 'mandor.dashboard',
            default => 'customer-layouts.dashboard',
        };
    }
}