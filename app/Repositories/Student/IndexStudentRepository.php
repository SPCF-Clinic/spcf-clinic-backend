<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Http\Resources\StudentResource;

class IndexStudentRepository extends BaseRepository
{
    public function execute($request)
    {
        $students = User::role('Student')
            ->when($request->student_id, function ($query, $student_id) {
                return $query->where('username', $student_id);
            })->get();

        return $this->success('Students retrieved successfully.', StudentResource::collection($students), 200   );
    }
}