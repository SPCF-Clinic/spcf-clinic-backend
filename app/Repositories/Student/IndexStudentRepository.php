<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Http\Resources\StudentResource;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexStudentRepository extends BaseRepository
{
    public function execute($request)
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sort_by') ?: 'id';
        $sortOrder = $request->input('sort_order') ?: 'asc';
        $students = User::role('Student')
            ->when($request->student_id, function ($query, $student_id) {
                return $query->where('username', $student_id);
            })
            ->withTrashed()
            ->get();

        $students = $students->map(
            fn ($student) => new StudentResource($student)
        );

        $students = $students
            ->sortBy(
                function (StudentResource $student) use ($request, $sortBy) {
                    $studentData = $student->toArray($request);

                    if ($sortBy === 'student_id') {
                        return $studentData['username'];
                    }

                    if ($sortBy === 'year_level') {
                        return ($studentData['year_level'] ?? '')
                            . ($studentData['grade_level'] ?? '');
                    }

                    return $studentData[$sortBy] ?? null;
                },
                SORT_NATURAL | SORT_FLAG_CASE,
                $sortOrder === 'desc'
            )
            ->values();

        $paginator = new LengthAwarePaginator(
            $students->forPage($page, $perPage)->values(),
            $students->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $paginationData = $this->pagePaginationData($paginator);

        return $this->success('Students retrieved successfully.', [
            'students' => StudentResource::collection($paginator),
            'pagination' => $paginationData,
        ], 200);
    }
}