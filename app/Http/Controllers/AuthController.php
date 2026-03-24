<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('login', $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login yoki parol xato'], 401);
        }

        return response()->json([
            'user'         => $user,
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type'   => 'Bearer'
        ]);
    }
}