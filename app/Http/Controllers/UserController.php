<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\User\{
    IndexUserRepository,
    UpdateUserRepository,
};
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    protected $index, $update;

    public function __construct(IndexUserRepository $index, UpdateUserRepository $update)
    {
        $this->index = $index;
        $this->update = $update;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        return $this->index->execute($request);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        return $this->update->execute($request, $user);
    }
}
