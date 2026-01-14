<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // Show the Register Form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Process the Registration
    public function register(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed', // expects 'password_confirmation' field
            'phone_number' => 'required',
        ]);

        // 2. Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => 'AGENT',
            'is_verified' => false, // <--- Force them to be an Agent
        ]);

        // 3. Auto Login & Redirect to Dashboard
        Auth::login($user);

        event(new Registered($user));
        
        return redirect()->route('verification.notice');
    }
}