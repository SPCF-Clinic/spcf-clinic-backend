<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\{
    IndexStudentRequest,
    IndexStudentCheckInsRequest,
    UpdateStudentRequest
};
use App\Repositories\Student\{
    IndexStudentRepository,
    ShowStudentRepository,
    IndexStudentCheckInsRepository,
    UpdateStudentRepository
};
use App\Models\User;

class StudentController extends Controller
{
    protected $index, $show, $indexCheckIns, $update;

    public function __construct(
        IndexStudentRepository $index,
        ShowStudentRepository $show,
        IndexStudentCheckInsRepository $indexCheckIns,
        UpdateStudentRepository $update
    ) {
        $this->index = $index;
        $this->show = $show;
        $this->indexCheckIns = $indexCheckIns;
        $this->update = $update;
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

    public function update(UpdateStudentRequest $request, User $student)
    {
        $this->authorize('update', $student);
        return $this->update->execute($request, $student);
    }
}