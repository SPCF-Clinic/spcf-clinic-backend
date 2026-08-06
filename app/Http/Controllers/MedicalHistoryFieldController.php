<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\MedicalHistoryField\{
    StoreMedicalHistoryFieldRepository,
    UpdateMedicalHistoryFieldRepository,
    IndexMedicalHistoryFieldRepository,
    DeleteMedicalHistoryFieldRepository,
    ReorderFormRepository,
};

use App\Http\Requests\MedicalHistoryField\{
    StoreMedicalHistoryFieldRequest,
    UpdateMedicalHistoryFieldRequest,
    ReorderFormRequest,
};

use App\Models\MedicalHistoryField;

class MedicalHistoryFieldController extends Controller
{
    protected $index, $store, $update, $delete, $reorder;

    public function __construct(
        IndexMedicalHistoryFieldRepository $index,
        StoreMedicalHistoryFieldRepository $store,
        UpdateMedicalHistoryFieldRepository $update,
        DeleteMedicalHistoryFieldRepository $delete,
        ReorderFormRepository $reorder
    ) {
        $this->index = $index;
        $this->store = $store;
        $this->update = $update;
        $this->delete = $delete;
        $this->reorder = $reorder;
    }

    public function index()
    {
        $this->authorize('viewAny', MedicalHistoryField::class);
        return $this->index->execute();
    }

    public function store(StoreMedicalHistoryFieldRequest $request)
    {
        $this->authorize('create', MedicalHistoryField::class);
        return $this->store->execute($request);
    }

    public function update(UpdateMedicalHistoryFieldRequest $request, MedicalHistoryField $field)
    {
        $this->authorize('update', $field);
        return $this->update->execute($request, $field);
    }

    public function destroy(MedicalHistoryField $field)
    {
        $this->authorize('delete', $field);
        return $this->delete->execute($field);
    }

    public function reorderForm(ReorderFormRequest $request)
    {
        $this->authorize('reorderForm', MedicalHistoryField::class);
        return $this->reorder->execute($request);
    }
}
