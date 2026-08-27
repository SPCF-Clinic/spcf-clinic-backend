<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    UserPersonalInfo,
};

class IndexUserRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
            'page' => 'sometimes|nullable|integer|min:1',
            'search' => 'sometimes|nullable|string|max:255',
        ]);

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->fullNameLike($search)
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $paginated = $query->paginate($request->input('per_page', 20));

        $users = $paginated->map(function ($user) {
            $personalInfo = $user->personalInfos()->get();
            $email = $user->hasPersonalInfoValue(null, 'email') ? $user->getPersonalInfoValueByName('email') : null;

            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->hasName() ? $user->getFullNameAttribute() : null,
                'contact_number' => $user->hasPersonalInfoValue(9) ? $user->getPersonalInfoValue(9) : null,
                'email' => $email,
                'access' => $user->getRoleNames()->first(),
            ];
        });

        return $this->success('Users retrieved successfully.', [
            'users' => $users,
            'pagination' => $this->pagePaginationData($paginated)
        ], 200);
    }
}
