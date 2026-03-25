<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller 
{
    // REGISTER - Username, Email va Password bilan ro'yxatdan o'tish
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username', // Username band bo'lmasligi kerak
            'email'    => 'required|string|email|unique:users,email', // Email band bo'lmasligi kerak
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        return response()->json([
            'message' => 'Muvaffaqiyatli ro‘yxatdan o‘tdingiz',
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'user'    => $user
        ], 201);
    }

    // LOGIN - Username, Email va Password hammasi mos kelishi shart
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Bazadan username va email bo'yicha qidiramiz
        $user = User::where('username', $request->username)
                    ->where('email', $request->email)
                    ->first();

        // Tekshiruv: Foydalanuvchi bormi va parol to'g'rimi?
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username, email yoki parol xato!'], 401);
        }

        return response()->json([
            'message' => 'Xush kelibsiz!',
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'user'    => $user
        ]);
    }
}