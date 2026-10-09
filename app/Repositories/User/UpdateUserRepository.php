<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Http\Resources\StudentResource;
use App\Models\User;

class UpdateUserRepository extends BaseRepository
{
    public function execute($request, $user)
    {
        $data = $request->validated();

        if (isset($data['status'])) {
            if (!auth()->user()->hasRole('Super Admin')) {
                return $this->error('You are not authorized to update the status of a user.', 403);
            }
            if ($data['status'] === 'ARCHIVE') {
                $user->delete();
            } elseif ($data['status'] === 'UNARCHIVE') {
                $user->restore();
            }
        }

        if (isset($data['password'])) {
            if ($user->trashed()) {
                return $this->error('Cannot update password for archived user.', 400);
            }
            $user->password = $data['password'];
        }

        $user->save();

        if ($user->hasRole('Student')) {
            $user = new StudentResource($user);
        } else {
            $user = collect([$user])->map(function ($u) {
                return [
                    'id' => $u->id,
                    'username' => $u->username,
                    'status' => $u->trashed() ? 'ARCHIVED' : 'ACTIVE',
                    'roles' => $u->getRoleNames()->first(),
                ];
            });
        }

        return $this->success('User updated successfully.', $user, 200);
    }
}
