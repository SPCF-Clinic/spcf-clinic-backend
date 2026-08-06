<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\PersonalInfoField\{
    StorePersonalInfoFieldRequest,
    UpdatePersonalInfoFieldRequest,
    SwitchFormOrderRequest,
};

use App\Repositories\PersonalInfoField\{
    StorePersonalInfoFieldRepository,
    UpdatePersonalInfoFieldRepository,
    IndexPersonalInfoFieldRepository,
    DeletePersonalInfoFieldRepository,
    SwitchFormOrderRepository,
};

use App\Models\PersonalInfoField;

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $update, $delete, $switch;

    public function __construct(
        IndexPersonalInfoFieldRepository $index,
        StorePersonalInfoFieldRepository $store,
        UpdatePersonalInfoFieldRepository $update,
        DeletePersonalInfoFieldRepository $delete,
        SwitchFormOrderRepository $switch
    ) {
        // $this->authorizeResource(PersonalInfoField::class, 'field');

        $this->index = $index;
        $this->store = $store;
        $this->update = $update;
        $this->delete = $delete;
        $this->switch = $switch;
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

    public function switchFormOrder(SwitchFormOrderRequest $request)
    {
        $this->authorize('switchFormOrder', PersonalInfoField::class);
        return $this->switch->execute($request);
    }
}
