<?php

namespace App\Http\Controllers;

use App\Repositories\Student\{
    IndexStudentRepository,
    ShowStudentRepository,
};
use App\Models\User;

class StudentController extends Controller
{
    protected $index, $show;

    public function __construct(
        IndexStudentRepository $index,
        ShowStudentRepository $show
    ) {
        $this->index = $index;
        $this->show = $show;
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        return $this->index->execute();
    }

    public function show(User $student)
    {
        $this->authorize('view', $student);
        return $this->show->execute($student);
    }
}