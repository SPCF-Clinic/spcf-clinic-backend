<?php

namespace App\Support;

class FormOrderInserter
{
    public static function makeRoomAt(string $versionModelClass, int $targetFormOrder): void
    {
        $versionModelClass::where('form_order', '>=', $targetFormOrder)
            ->increment('form_order');
    }
}