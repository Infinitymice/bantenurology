<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            // Cek apakah pengguna adalah admin
            if (Auth::user()->is_admin) {
                return redirect()->intended('admin/dashboard'); // Redirect ke dashboard admin
            }

            // Redirect ke home jika pengguna bukan admin
            return redirect()->intended('home');
        }

        // Jika login gagal
        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    
    public function logout(Request $request)
    {
        // Logout pengguna
        Auth::logout();

        // Menghapus sesi dan token (jika menggunakan API token)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login setelah logout
        return redirect()->route('admin.auth.login');
    }
}
