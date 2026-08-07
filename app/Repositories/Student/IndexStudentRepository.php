<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Http\Resources\StudentResource;

class IndexStudentRepository extends BaseRepository
{
    public function execute()
    {
        $students = User::role('USER')
            ->orderBy('username')
            ->get();

        return $this->success(
            'Students retrieved successfully.',
            StudentResource::collection($students),
            200
        );
    }
}