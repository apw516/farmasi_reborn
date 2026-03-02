<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    public function Index()
    {
        return view('Auth.login');
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // if (auth()->user()->status == 1) {
                return redirect()->intended('dashboard');
            // } else {
            //     Auth::logout();
            //     $request->session()->invalidate();
            //     $request->session()->regenerateToken();
            //     return redirect('/')->with('loginError', 'User belum diaktivasi !');
            // }
        }
        return back()->with('loginError', 'Login gagal !');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
