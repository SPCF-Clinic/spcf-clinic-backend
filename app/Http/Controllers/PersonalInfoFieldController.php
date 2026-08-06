<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\PersonalInfoField\{
    StorePersonalInfoFieldRequest,
    UpdatePersonalInfoFieldRequest,
};

use App\Repositories\PersonalInfoField\{
    StorePersonalInfoFieldRepository,
    UpdatePersonalInfoFieldRepository,
    IndexPersonalInfoFieldRepository,
    DeletePersonalInfoFieldRepository,
};

use App\Models\PersonalInfoField;

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $update, $delete;

    public function __construct(
        IndexPersonalInfoFieldRepository $index,
        StorePersonalInfoFieldRepository $store,
        UpdatePersonalInfoFieldRepository $update,
        DeletePersonalInfoFieldRepository $delete
    ) {
        // $this->authorizeResource(PersonalInfoField::class, 'field');

        $this->index = $index;
        $this->store = $store;
        $this->update = $update;
        $this->delete = $delete;
    }

    public function index()
    {
        $this->authorize('viewAny', PersonalInfoField::class);
        return $this->index->execute();
    }

    public function store(StorePersonalInfoFieldRequest $request)
    {
        $this->authorize('create', PersonalInfoField::class);
        return $this->store->execute($request);
    }

    public function update(UpdatePersonalInfoFieldRequest $request, PersonalInfoField $field)
    {
        $this->authorize('update', $field);
        return $this->update->execute($request, $field);
    }

    public function destroy(PersonalInfoField $field)
    {
        $this->authorize('delete', $field);
        return $this->delete->execute($field);
    }
}
