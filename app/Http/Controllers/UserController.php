<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\User\IndexUserRepository;

class UserController extends Controller
{
    protected $index;

    public function __construct(IndexUserRepository $index)
    {
        $this->index = $index;
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        return $this->index->execute();
    }
}
