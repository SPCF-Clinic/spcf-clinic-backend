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
    IndexPersonalInfoFieldRepository
};

use App\Models\PersonalInfoField;

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $show, $update, $delete;

    public function __construct(
        IndexPersonalInfoFieldRepository $index,
        StorePersonalInfoFieldRepository $store,
        UpdatePersonalInfoFieldRepository $update
    ) {
        $this->authorizeResource(PersonalInfoField::class, 'field');

        $this->index = $index;
        $this->store = $store;
        $this->update = $update;
    }

    public function index()
    {
        return $this->index->execute();
    }

    public function store(StorePersonalInfoFieldRequest $request)
    {
        return $this->store->execute($request);
    }

    public function update(UpdatePersonalInfoFieldRequest $request, PersonalInfoField $field)
    {
        return $this->update->execute($request, $field);
    }
}
