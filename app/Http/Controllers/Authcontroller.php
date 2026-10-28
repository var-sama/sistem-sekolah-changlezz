<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Authcontroller extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]); 

        $remember = $request->has('remember');

        if(Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $userRole = Auth::user()->role;
            
            if ($userRole === 'teacher') {
                return redirect()->route('teachers.index');
            } elseif ($userRole === 'student') {
                return redirect()->route('students.index');
            }


            return redirect('/');
        }

        return back()->withErrors([
            'email' => "Email or Password does not match"
        ]);
    }

    public function registerView()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {

        $validatedRequest = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);


        User::create([
            'name' => $validatedRequest['name'],
            'email' => $validatedRequest['email'],
            'password' => bcrypt($validatedRequest['password']),
            'role' => 'student',
            'email_verified_at' => now()
        ]);

        return redirect()->route('login-view');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login-view');
    }
}