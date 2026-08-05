<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\PersonalInfoField\{
    StorePersonalInfoFieldRequest,
    UpdatePersonalInfoFieldRequest,
};

use App\Repositories\PersonalInfoField\{
    StorePersonalInfoField,
    UpdatePersonalInfoField,
};

use App\Models\PersonalInfoField;

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $show, $update, $delete;

    public function __construct(
        StorePersonalInfoField $store,
        UpdatePersonalInfoField $update
    ) {
        $this->store = $store;
        $this->update = $update;
    }

    public function store(StorePersonalInfoFieldRequest $request)
    {
        // $this->authorize('create', PersonalInfoField::class);
        return $this->store->execute($request);
    }

    public function update(UpdatePersonalInfoFieldRequest $request, PersonalInfoField $field)
    {
        // $this->authorize('update', $field);
        return $this->update->execute($request, $field);
    }
}
