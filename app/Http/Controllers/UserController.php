<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'message' => 'User already exist',
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'User registration failed',
            ], 500);
        }

        $tokenString = bin2hex(random_bytes(32));
        $token = Token::create([
            'token' => $tokenString,
            'user_id' => $user->id,
            'is_active' => 1,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token->token,
        ], 201);
    }
}
