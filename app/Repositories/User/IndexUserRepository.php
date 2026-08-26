<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    UserPersonalInfo,
};

class IndexUserRepository extends BaseRepository
{
    public function execute(){
        $users = User::all()->map(function ($user) {
            $personalInfo = $user->personalInfos()->get();

            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->hasName() ? $user->getFullNameAttribute() : null,
                'contact_number' => $user->hasPersonalInfoValue(9) ? $user->getPersonalInfoValue(9) : null,
                'access' => $user->getRoleNames()->first(),
            ];
        });

        return $this->success('Users retrieved successfully.', $users, 200);
    }
}
