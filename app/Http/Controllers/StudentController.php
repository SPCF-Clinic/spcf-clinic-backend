<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\{
    IndexStudentRequest,
    IndexStudentCheckInsRequest
};
use App\Repositories\Student\{
    IndexStudentRepository,
    ShowStudentRepository,
    IndexStudentCheckInsRepository
};
use App\Models\User;

class StudentController extends Controller
{
    protected $index, $show, $indexCheckIns;

    public function __construct(
        IndexStudentRepository $index,
        ShowStudentRepository $show,
        IndexStudentCheckInsRepository $indexCheckIns

    ) {
        $this->index = $index;
        $this->show = $show;
        $this->indexCheckIns = $indexCheckIns;
    }

    public function index(IndexStudentRequest $request)
    {
        $this->authorize('viewAny', User::class);
        return $this->index->execute($request);
    }

    public function show(User $student)
    {
        $this->authorize('view', $student);
        return $this->show->execute($student);
    }

    public function indexCheckIns(IndexStudentCheckInsRequest $request, User $student)
    {
        $this->authorize('viewCheckIns', $student);
        return $this->indexCheckIns->execute($request, $student);
    }
}