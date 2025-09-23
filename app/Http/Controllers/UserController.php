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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',

            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
            ]);
        }

        $tokenString = bin2hex(random_bytes(32));

        $token = Token::create([
            'token' => $tokenString,
            'user_id' => $user->id,
            'is_active' => 1,
        ]);

        if (!$token) {
            return response()->json([
                'message' => 'token generation failed',
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token->token,
        ]);
    }

    public function dashboard(Request $request)
    {
        $token = $request->attributes->get('token');
        $user = User::find($token->user_id);

        return response()->json([
            'message' => 'Welcome to dashboard',
            'user' => $user,
        ]);
    }
}
