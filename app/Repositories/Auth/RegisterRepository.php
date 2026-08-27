<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    PersonalInfoField,
    MedicalHistoryField,
    ActivityLog,
};
use App\Support\FormVersion;
use Illuminate\Support\Facades\{
    DB,
    Hash,
};
use App\Events\RegisterEvent;

class RegisterRepository extends BaseRepository
{
    public function execute($request){
        $validated = $request->validated();

        if ($validated['personal_info_form_version'] !== FormVersion::compute(PersonalInfoField::class)
            || $validated['medical_history_form_version'] !== FormVersion::compute(MedicalHistoryField::class)) {
            return $this->error(
                'This form has changed since you loaded it. Please refresh the page and try again.',
                409
            );
        }

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
                // 'group' => 'AUTH',
                'action' => "New student registered: {$fullName}",
                'performed_by' => $user->id,
            ]);

            DB::commit();

            broadcast(new RegisterEvent($user->id));

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