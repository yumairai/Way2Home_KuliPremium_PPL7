<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // tampilkan form profile
    public function edit()
    {
        $user = Auth::user();

        return view('customer.profile', compact('user'));
    }

    // update data profile
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($request->only([
            'name', 'email', 'phone_number', 'address'
        ]));

        return back()->with('success', 'Profile berhasil diupdate');
    }
}