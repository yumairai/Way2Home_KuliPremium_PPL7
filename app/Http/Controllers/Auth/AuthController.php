<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

        Customer::create(['user_id' => $user->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message'  => 'Register Berhasil',
            'redirect' => route('customer-layouts.dashboard')
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Logika Pengalihan Berdasarkan Role
            if ($user->role === 'admin') {
                return response()->json([
                    'message'  => 'Login Admin Berhasil',
                    'redirect' => route('admin.dashboard')
                ], 200);
            }

            // Default ke Dashboard Customer
            return response()->json([
                'message'  => 'Login Berhasil',
                'redirect' => route('customer-layouts.dashboard') // Ke Dashboard Customer
            ], 200);
        }

        return response()->json(['message' => 'Email atau Password Salah'], 401);
    }

    public function logout(Request $request)
    {
        $role = Auth::user()->role;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role === 'admin') {
            return redirect()->route('login')->with('message', 'Anda telah logout');
        }

        return redirect('/')->with('message', 'Anda telah logout');
    }

    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('customer-layouts.dashboard');
        }
        return view('customer-layouts.dashboard');
    }
}
