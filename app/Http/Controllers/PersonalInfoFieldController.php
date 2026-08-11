<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\PersonalInfoField\{
    StorePersonalInfoFieldRequest,
    UpdatePersonalInfoFieldRequest,
    DeletePersonalInfoFieldRequest,
    ReorderFormRequest,
};

use App\Repositories\PersonalInfoField\{
    StorePersonalInfoFieldRepository,
    UpdatePersonalInfoFieldRepository,
    IndexPersonalInfoFieldRepository,
    DeletePersonalInfoFieldRepository,
    ReorderFormRepository,
};

use App\Models\PersonalInfoField;

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $update, $delete, $reorder;

    public function __construct(
        IndexPersonalInfoFieldRepository $index,
        StorePersonalInfoFieldRepository $store,
        UpdatePersonalInfoFieldRepository $update,
        DeletePersonalInfoFieldRepository $delete,
        ReorderFormRepository $reorder
    ) {
        // $this->authorizeResource(PersonalInfoField::class, 'field');

        $this->index = $index;
        $this->store = $store;
        $this->update = $update;
        $this->delete = $delete;
        $this->reorder = $reorder;
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

    public function destroy(DeletePersonalInfoFieldRequest $request, PersonalInfoField $field)
    {
        $this->authorize('delete', $field);
        return $this->delete->execute($request, $field);
    }

    public function reorderForm(ReorderFormRequest $request)
    {
        $this->authorize('reorderForm', PersonalInfoField::class);
        return $this->reorder->execute($request);
    }
}