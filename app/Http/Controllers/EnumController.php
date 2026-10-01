<?php

namespace App\Http\Controllers;

use App\Repositories\Enum\{
    FormFieldTypeEnumsRepository,
    CheckInEnumsRepository
};

class EnumController extends Controller
{
    protected $formFieldTypes, $checkInEnums;

    public function __construct(
        FormFieldTypeEnumsRepository $formFieldTypes,
        CheckInEnumsRepository $checkInEnums
    ) {
        $this->formFieldTypes = $formFieldTypes;
        $this->checkInEnums = $checkInEnums;
    }

    public function formFieldTypes()
    {
        return $this->formFieldTypes->execute();
    }

    public function checkInEnums()
    {
        return $this->checkInEnums->execute();
    }
}