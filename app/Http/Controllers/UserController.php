<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\JWT;
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

        $secretKey = env('SECRET_KEY');
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $jwt,
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
        $secretKey = env('SECRET_KEY');
        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        if (!$jwt) {
            return response()->json([
                'message' => 'token generation failed',
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $jwt,
        ]);
    }

    public function dashboard(Request $request)
    {
        $token = $request->attributes->get('auth_user');
        $user = User::find($token['id']);

        return response()->json([
            'message' => 'Welcome to dashboard',
            'user' => $user,
        ]);
    }
}
