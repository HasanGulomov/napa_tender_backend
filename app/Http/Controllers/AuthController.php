<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->service->register($data);
        return response()->json(['message' => 'Ro‘yxatdan o‘tdingiz', 'data' => $result], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->service->login($data);

        if (!$result) {
            return response()->json(['message' => 'Email yoki parol xato!'], 401);
        }

        return response()->json(['message' => 'Xush kelibsiz!', 'data' => $result]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'username' => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $updatedUser = $this->service->updateProfile($user, $data);

        return response()->json([
            'message' => 'Ma’lumotlar yangilandi',
            'user'    => $updatedUser
        ]);
    }

    public function delete(Request $request)
    {
        $this->service->deleteAccount($request->user());
        return response()->json(['message' => 'Akkaunt o‘chirildi']);
    }
}
