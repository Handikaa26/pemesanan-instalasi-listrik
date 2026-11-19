<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:user,email',
            'password' => 'required|string|min:6',
            'nomor_telp' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        // Generate Custom PK id_user: USR00001
        $lastUser = User::where('id_user', 'LIKE', 'USR%')->orderBy('id_user', 'desc')->first();
        $nextNum = 1;
        if ($lastUser) {
            $nextNum = intval(substr($lastUser->id_user, 3)) + 1;
        }
        $idUser = 'USR' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $idUser) {
            $user = User::create([
                'id_user' => $idUser,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nomor_telp' => $request->nomor_telp,
                'tanggal_lahir' => $request->tanggal_lahir,
                'foto_profil' => 'default.png',
                'id_role' => 2, // 2: Konsumen
                'is_active' => 1,
            ]);

            Alamat::create([
                'alamat' => $request->alamat,
                'penerima' => $request->username,
                'telepon_penerima' => $request->nomor_telp,
                'id_user' => $idUser,
            ]);

            Auth::login($user);
        });

        return redirect()->route('home')->with('success', 'Pendaftaran akun berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar.');
    }
}

