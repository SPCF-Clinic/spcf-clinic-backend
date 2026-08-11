<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Http\Resources\StudentResource;

class IndexStudentRepository extends BaseRepository
{
    public function execute($request)
    {
        $perPage = $request->input('per_page', 20);
        $students = User::role('Student')
            ->when($request->student_id, function ($query, $student_id) {
                return $query->where('username', $student_id);
            })->paginate($perPage);

        $paginationData = $this->pagePaginationData($students);

        return $this->success('Students retrieved successfully.', [
            'students' => StudentResource::collection($students),
            'pagination' => $paginationData
        ], 200);
    }
}