<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    User,
    UserPersonalInfo,
};

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::all()->map(function ($user) {
            $personalInfo = $user->personalInfos()->get();
            $lastName = $personalInfo->where('personal_info_field_id', 2)->first()?->value;
            $firstName = $personalInfo->where('personal_info_field_id', 1)->first()?->value;
            $middleName = $personalInfo->where('personal_info_field_id', 3)->first()?->value;

            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName,
                'contact_number' => $personalInfo->where('personal_info_field_id', 9)->first()?->value,
                'access' => $user->getRoleNames()->first(),
            ];
        });

        return $this->success('Users retrieved successfully.', $users, 200);
    }
}
