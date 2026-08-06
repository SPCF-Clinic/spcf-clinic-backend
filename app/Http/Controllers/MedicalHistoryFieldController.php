<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\MedicalHistoryField\{
    StoreMedicalHistoryFieldRepository,
};

use App\Http\Requests\MedicalHistoryField\{
    StoreMedicalHistoryFieldRequest,
};

use App\Models\MedicalHistoryField;

class MedicalHistoryFieldController extends Controller
{
    protected $store;

    public function __construct(
        StoreMedicalHistoryFieldRepository $store
    ) {
        $this->store = $store;
    }

    public function store(StoreMedicalHistoryFieldRequest $request)
    {
        $this->authorize('create', MedicalHistoryField::class);
        return $this->store->execute($request);
    }
}
