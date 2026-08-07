<?php

namespace App\Http\Controllers;

use App\Repositories\Enum\FormFieldTypeEnumsRepository;

class EnumController extends Controller
{
    protected $formFieldTypes;

    public function __construct(
        FormFieldTypeEnumsRepository $formFieldTypes
    ) {
        $this->formFieldTypes = $formFieldTypes;
    }

    public function formFieldTypes()
    {
        return $this->formFieldTypes->execute();
    }
}