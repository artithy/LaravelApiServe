<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\Key;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('authorization');
        if (!$authHeader) {
            return response()->json([
                'message' => 'Token is not provided'
            ], 401);
        }
        $token = substr($authHeader, 7);
        $secretKey = env('SECRET_KEY');

        try {
            $decode = JWT::decode($token, new Key($secretKey, 'HS256'));
            if (!isset($decode->exp) || $decode->exp < time()) {
                return response([
                    'message' => 'Token expired'
                ], 401);
            }
            $request->attributes->set('auth_user', [
                'id' => $decode->id,
                'name' => $decode->name
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }
        return $next($request);
    }
}
