<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'userBGH' && $password === 'passBGH') {
            // Assuming you want to return a token or some indication of successful login
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }
}
