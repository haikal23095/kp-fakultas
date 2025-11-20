<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profile user
     */
    public function index()
    {
        $user = Auth::user();

        // Load relasi berdasarkan role
        $user->load(['role', 'mahasiswa.prodi', 'dosen.prodi', 'pegawai.prodi']);

        // Determine the layout based on user role
        $roleId = $user->Id_Role;
        $layout = match ($roleId) {
            1 => 'admin',
            2 => 'dekan',
            3 => 'kajur',
            4 => 'kaprodi',
            5 => 'dosen',
            6 => 'mahasiswa',
            default => 'mahasiswa',
        };

        return view('profile.index', compact('user', 'layout'));
    }

    /**
     * Update profile user
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'Name_User' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:Users,email,' . $user->Id_User . ',Id_User',
        ]);

        $user->Name_User = $request->Name_User;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password berhasil diperbarui!');
    }
}
