<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return response()->json([
            'message' => 'Register endpoint placeholder'
        ]);
    }

    public function login(Request $request)
    {
        return response()->json([
            'message' => 'Login endpoint placeholder'
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'message' => 'Logout endpoint placeholder'
        ]);
    }
}