<?php

namespace App\Repositories\Student;

use App\Repositories\BaseRepository;
use App\Http\Resources\StudentResource;
use App\Models\User;

class UpdateStudentRepository extends BaseRepository
{
    public function execute($request, $student)
    {
        $data = $request->validated();

        if (isset($data['status'])) {
            if (!auth()->user()->hasRole('Super Admin')) {
                return $this->error('You are not authorized to update the status of a student.', 403);
            }
            if ($data['status'] === 'ARCHIVE') {
                $student->delete();
            } elseif ($data['status'] === 'UNARCHIVE') {
                $student->restore();
            }
        }

        if (isset($data['password'])) {
            if ($student->trashed()) {
                return $this->error('Cannot update password for archived student.', 400);
            }
            $student->password = $data['password'];
        }

        $student->save();

        return $this->success('Student updated successfully.', new StudentResource($student), 200);
    }
}
