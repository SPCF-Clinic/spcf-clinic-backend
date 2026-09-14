<?php

namespace App\Support;

class FormFieldConflictChecker
{
    public static function checkForConflicts(string $versionModelClass)
    {
        $conflicts = [];

        $fields = $versionModelClass::with('latestVersion')->get();
        
        // Check for missing form_orders
        for ($i = 1; $i <= count($fields); $i++) {
            $fieldWithOrder = $fields->first(function ($field) use ($i) {
                return $field->latestVersion?->form_order === $i;
            });
            if (!$fieldWithOrder) {
                $conflicts[] = "No field found with form_order {$i}.";
            }
        }
        
        // Check for duplicate form_orders
        $formOrderCounts = [];
        foreach ($fields as $field) {
            $order = $field->latestVersion?->form_order;
            if ($order) {
                $formOrderCounts[$order] = ($formOrderCounts[$order] ?? 0) + 1;
            }
        }
        
        foreach ($formOrderCounts as $order => $count) {
            if ($count > 1) {
                $conflicts[] = "Multiple fields found with form_order {$order}.";
            }
        }

        return $conflicts;
    }
}