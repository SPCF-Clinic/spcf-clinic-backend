<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;

class ShowAuthenticatedUserRepository extends BaseRepository
{
    public function execute(){
        $user = auth()->user();

        $userDetails = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->personalInfos ? $user->getFullNameAttribute() : null,
            'role' => $user->getRoleNames()->first(),
        ];

        return $this->success('Authenticated user retrieved successfully.', $userDetails, 200);
    }
}
