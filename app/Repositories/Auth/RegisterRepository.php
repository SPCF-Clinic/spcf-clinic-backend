<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    ActivityLog,
};
use Illuminate\Support\Facades\{
    DB,
    Hash,
};

class RegisterRepository extends BaseRepository
{
    public function execute($request){
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $validated['student_id'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->assignRole('Student');

            $this->storeFieldAnswers($user, $validated['personal_info'] ?? [], 'personalInfos', 'personal_info_field_id');
            $this->storeFieldAnswers($user, $validated['medical_history'] ?? [], 'medicalHistories', 'medical_history_field_id');

            $fullName = $user->getFullNameAttribute();
            ActivityLog::create([
                'group' => 'AUTH',
                'action' => "New student registered: {$fullName}",
                'performed_by' => $user->id,
            ]);

            DB::commit();

            return $this->success('User registered successfully.', $user, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Registration failed.', 500, $e->getMessage());
        }
    }

    /**
     * Persist one EAV row per answered field. Unanswered optional fields
     * (null) are skipped rather than stored, since the `value` column isn't
     * nullable. Multi-select (Checkbox) answers arrive as arrays and are
     * JSON-encoded for storage.
     */
    private function storeFieldAnswers(User $user, array $answers, string $relation, string $foreignKey): void
    {
        foreach ($answers as $fieldId => $value) {
            if ($value === null) {
                continue;
            }

            $user->{$relation}()->create([
                $foreignKey => $fieldId,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ]);
        }
    }
}
