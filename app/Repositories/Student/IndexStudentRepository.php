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
        $sortBy = $request->input('sort_by') ?: 'id';
        $sortOrder = $request->input('sort_order') ?: 'asc';
        $students = User::role('Student')
            ->when($request->student_id, function ($query, $student_id) {
                return $query->where('username', $student_id);
            })
            ->withTrashed()
            ->paginate($perPage);

        $paginationData = $this->pagePaginationData($students);
        $students = StudentResource::collection($students);

        $students->collection = $students->collection
            ->sortBy(function (StudentResource $student) use ($request, $sortBy) {
                $studentData = $student->toArray($request);

                if ($sortBy === 'student_id') {
                    return $studentData['username'];
                }

                if ($sortBy === 'year_level') {
                    return ($studentData['year_level'] ?? '') . ($studentData['grade_level'] ?? '');
                }

                return $studentData[$sortBy] ?? null;
            }, SORT_NATURAL | SORT_FLAG_CASE, $sortOrder === 'desc')
            ->values();

        return $this->success('Students retrieved successfully.', [
            'students' => $students,
            'pagination' => $paginationData
        ], 200);
    }
}