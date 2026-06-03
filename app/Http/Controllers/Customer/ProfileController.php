<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected SupabaseStorageService $supabase;

    public function __construct(SupabaseStorageService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function edit()
    {
        return view('customer-layouts.profile', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string',
            'address'      => 'nullable|string',
            'avatar'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone_number', 'address']);

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama kalau ada
            if ($user->avatar) {
                $this->supabase->deletePublic($user->avatar);
            }

            // Upload ke public-assets/avatars/
            $url = $this->supabase->uploadPublic($request->file('avatar'), 'avatars');
            $data['avatar'] = $url; // simpan full public URL
        }

        $user->update($data);

        return back()->with('success', 'Profile berhasil diupdate');
    }

    public function updateAddressData(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama'    => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'required|string',
        ]);

        $user->update([
            'name'         => $request->nama,
            'phone_number' => $request->telepon,
            'address'      => $request->alamat,
        ]);

        $user->customer()->update([
            'no_hp' => $request->telepon,
        ]);

        return response()->json(['status' => 'success']);
    }
}