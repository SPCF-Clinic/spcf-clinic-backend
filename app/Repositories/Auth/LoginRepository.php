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

        $fullName = $user->hasRole('Student') ? $user->getFullNameAttribute() : $user->username;
        ActivityLog::create([
            'group' => 'AUTH',
            'action' => "{$fullName} logged in.",
            'performed_by' => $user->id,
        ]);

        return $this->success('User logged in successfully.', [
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}
