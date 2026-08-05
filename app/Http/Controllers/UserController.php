<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\User\{
    RegisterRequest,
    LoginRequest,
};
use App\Models\{
    User,
};
use Illuminate\Support\Facades\{
    DB,
    Hash,
    Auth,
};

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        if (User::where('username', $validated['student_id'])->exists()) {
            return $this->error('Username already exists.', 400);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $validated['student_id'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->assignRole('USER');

            $validated['student_info']['student_id'] = $validated['student_id'];
            $user->studentInfo()->create($validated['student_info']);

            DB::commit();

            return $this->success('User registered successfully.', $user, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Registration failed.', 500, $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
        ])) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success('User logged in successfully.', [
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            return $this->success('User logged out successfully.', null, 200);
        }
        return $this->error('User not authenticated.', 401);
    }
}
