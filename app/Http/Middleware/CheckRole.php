<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->Id_Role;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect ke dashboard sesuai role user jika tidak memiliki akses
        switch ($userRole) {
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
}