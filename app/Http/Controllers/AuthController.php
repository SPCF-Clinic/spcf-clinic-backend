<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\{
    RegisterRequest,
    LoginRequest,
};
use App\Models\{
    User,
    ActivityLog,
};
use Illuminate\Support\Facades\{
    DB,
    Hash,
    Auth,
};

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
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

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
        ])) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        $fullName = $user->hasRole('Student') ? $user->getFullNameAttribute() : $user->username;
        ActivityLog::create([
            'group' => 'AUTH',
            'action' => "{$fullName} logged in.",
            'performed_by' => $user->id,
        ]);

        return $this->success('User logged in successfully.', [
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $fullName = $user->hasRole('Student') ? $user->getFullNameAttribute() : $user->username;
            ActivityLog::create([
                'group' => 'AUTH',
                'action' => "{$fullName} logged out.",
                'performed_by' => $user->id,
            ]);

            $user->tokens()->delete();
            return $this->success('User logged out successfully.', null, 200);
        }
        return $this->error('User not authenticated.', 401);
    }
}