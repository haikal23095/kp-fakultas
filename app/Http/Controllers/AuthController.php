<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Check if password is hashed or plain text
            $passwordMatches = false;

            if (str_starts_with($user->password, '$2y$')) {
                // Password is hashed, use normal check
                $passwordMatches = Hash::check($request->password, $user->password);
            } else {
                // Password is plain text, compare directly
                $passwordMatches = ($request->password === $user->password);
            }

            if ($passwordMatches) {
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();

                // Redirect berdasarkan role
                return $this->redirectToRoleDashboard($user->Id_Role);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Menampilkan dashboard setelah login
     */
    public function dashboard()
    {
        $user = Auth::user();
        return $this->redirectToRoleDashboard($user->Id_Role);
    }

    /**
     * Redirect ke dashboard berdasarkan role
     */
    private function redirectToRoleDashboard($roleId)
    {
        switch ($roleId) {
            case 1:
                return redirect()->route('dashboard.admin');
            case 2:
                return redirect()->route('dashboard.dekan');
            case 3:
                return redirect()->route('dashboard.kajur');
            case 4:
                return redirect()->route('dashboard.kaprodi');
            case 5:
                return redirect()->route('dashboard.dosen');
            case 6:
                return redirect()->route('dashboard.mahasiswa');
            default:
                return redirect()->route('dashboard.default');
        }
    }

    // Dashboard untuk setiap role
    public function dashboardAdmin()
    {
        return view('dashboard.admin');
    }

    public function dashboardDekan()
    {
        return view('dashboard.dekan');
    }

    public function dashboardKajur()
    {
        return view('dashboard.kajur');
    }

    public function dashboardKaprodi()
    {
        return view('dashboard.kaprodi');
    }

    public function dashboardDosen()
    {
        return view('dashboard.dosen');
    }

    public function dashboardMahasiswa()
    {
        return view('dashboard.mahasiswa');
    }

    public function dashboardDefault()
    {
        return view('dashboard.default');
    }
}
