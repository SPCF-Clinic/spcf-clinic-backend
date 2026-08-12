<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Models\ActivityLog;

class LogoutRepository extends BaseRepository
{
    public function execute(){
        $user = auth()->user();
        if ($user) {
            $fullName = $user->hasRole('Student') ? $user->getFullNameAttribute() : $user->username;
            ActivityLog::create([
                // 'group' => 'AUTH',
                'action' => "{$fullName} logged out.",
                'performed_by' => $user->id,
            ]);

            $user->tokens()->delete();
            return $this->success('User logged out successfully.', null, 200);
        }
        return $this->error('User not authenticated.', 401);
    }
}
