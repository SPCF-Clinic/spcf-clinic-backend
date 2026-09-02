<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LoginRepository extends BaseRepository
{
    public function execute($request){
        $validated = $request->validated();

        if (!Auth::attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
        ])) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        $fullName = $user->hasName() ? $user->getFullNameAttribute() : $user->username;
        ActivityLog::create([
            'group' => 'AUTH',
            'action' => "{$fullName} logged in.",
            'performed_by' => $user->id,
        ]);

        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'full_name' => $fullName,
            'role' => $user->roles->pluck('name')->first(),
        ];

        return $this->success('User logged in successfully.', [
            'user' => $userData,
            'token' => $token,
        ], 200);
    }
}
