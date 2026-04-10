<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register($data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function login($data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function updateProfile($user, $data)
    {

        $user->username = $data['username'] ?? $user->username;
        $user->email = $data['email'] ?? $user->email;


        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return $user;
    }

    public function deleteAccount($user)
    {
        $user->tokens()->delete(); 
        return $user->delete();  
    }
}
