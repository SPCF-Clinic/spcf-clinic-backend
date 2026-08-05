<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\PersonalInfoField\{
    StorePersonalInfoFieldRequest,
};

use App\Repositories\PersonalInfoField\{
    StorePersonalInfoField,
};

class PersonalInfoFieldController extends Controller
{
    protected $index, $store, $show, $update, $delete;

    public function __construct(
        StorePersonalInfoField $store
    ) {
        $this->store = $store;
    }

    public function store(StorePersonalInfoFieldRequest $request)
    {
        return $this->store->execute($request);
    }
}
