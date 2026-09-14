<?php

namespace App\Support;

class FormFieldConflictResolver
{
    // private const MAX_ITERATIONS = 100;

    // public static function resolve(string $modelClass): void
    // {
    //     for ($iteration = 0; $iteration < self::MAX_ITERATIONS; $iteration++) {
    //         $changed = static::fixMultiVersionFormOrders($modelClass);
    //         $changed = static::fixMissingFormOrders($modelClass) || $changed;
    //         $changed = static::fixFirstDuplicateFormOrder($modelClass) || $changed;
    //         $changed = static::fixNullFormOrders($modelClass) || $changed;

    //         if (! $changed) {
    //             return;
    //         }
    //     }

    //     throw new \RuntimeException(
    //         "FormFieldConflictResolver could not resolve all form_order conflicts for {$modelClass} "
    //         . 'after ' . self::MAX_ITERATIONS . ' passes.'
    //     );
    // }

    // /**
    //  * Check 1 — for every field with more than one version carrying a
    //  * non-null form_order, keep it on the version with the highest
    //  * version_number and null it out everywhere else on that field.
    //  */
    // private static function fixMultiVersionFormOrders(string $modelClass): bool
    // {
    //     $changed = false;

    //     $modelClass::with('versions')->get()->each(function ($field) use (&$changed) {
    //         $versionsWithOrder = $field->versions->filter(fn ($version) => $version->form_order !== null);

    //         if ($versionsWithOrder->count() <= 1) {
    //             return;
    //         }

    //         $latest = $versionsWithOrder->sortByDesc('version_number')->first();

    //         $versionsWithOrder
    //             ->reject(fn ($version) => $version->id === $latest->id)
    //             ->each(function ($version) use (&$changed) {
    //                 $version->update(['form_order' => null]);
    //                 $changed = true;
    //             });
    //     });

    //     return $changed;
    // }

    // /**
    //  * Check 2 — let x = the total number of fields. If the highest
    //  * form_order exceeds x, there's a gap somewhere. Let a = the list of
    //  * missing values in 1..max, x = the highest value in a, n = the size
    //  * of a. Every form_order higher than x is decreased by n, closing the
    //  * gap.
    //  */
    // private static function fixMissingFormOrders(string $modelClass): bool
    // {
    //     $fields = $modelClass::with('latestVersion')->get();
    //     $totalFields = $fields->count();

    //     $orders = $fields
    //         ->map(fn ($field) => $field->latestVersion?->form_order)
    //         ->filter(fn ($value) => $value !== null)
    //         ->values();

    //     $maxOrder = $orders->max();

    //     if ($maxOrder === null || $maxOrder <= $totalFields) {
    //         return false;
    //     }

    //     $present = $orders->flip();
    //     $missing = collect(range(1, $maxOrder))->reject(fn ($i) => $present->has($i));

    //     if ($missing->isEmpty()) {
    //         return false;
    //     }

    //     $n = $missing->count();
    //     $highestMissing = $missing->max();

    //     $fields
    //         ->filter(fn ($field) => $field->latestVersion?->form_order !== null
    //             && $field->latestVersion->form_order > $highestMissing)
    //         ->each(fn ($field) => $field->latestVersion->update([
    //             'form_order' => $field->latestVersion->form_order - $n,
    //         ]));

    //     return true;
    // }

    // /**
    //  * Check 3 — starting from i = 1 and counting up, find the first
    //  * form_order value held by more than one field. Once found (call it
    //  * x): every field with a form_order higher than x shifts up by one
    //  * (making room at x + 1), and one of the fields tied at x (all but the
    //  * lowest id, which stays put) moves into that freed slot. Only the
    //  * first duplicate found is fixed per call — the outer resolve() loop
    //  * re-scans from scratch, which both re-finds any further duplicate and
    //  * re-runs checks 1-2 in case this shift disturbed them.
    //  */
    // private static function fixFirstDuplicateFormOrder(string $modelClass): bool
    // {
    //     $fields = $modelClass::with('latestVersion')->get();
    //     $totalFields = $fields->count();

    //     for ($i = 1; $i <= $totalFields; $i++) {
    //         $holders = $fields
    //             ->filter(fn ($field) => $field->latestVersion?->form_order === $i)
    //             ->sortBy(fn ($field) => $field->id)
    //             ->values();

    //         if ($holders->count() <= 1) {
    //             continue;
    //         }

    //         $extra = $holders[1];

    //         $fields
    //             ->filter(fn ($field) => $field->latestVersion?->form_order !== null
    //                 && $field->latestVersion->form_order > $i)
    //             ->sortByDesc(fn ($field) => $field->latestVersion->form_order)
    //             ->each(fn ($field) => $field->latestVersion->update([
    //                 'form_order' => $field->latestVersion->form_order + 1,
    //             ]));

    //         $extra->latestVersion->update(['form_order' => $i + 1]);

    //         return true;
    //     }

    //     return false;
    // }

    // /**
    //  * Check 4 — an active field (its latest version) with a null
    //  * form_order. Every field should always have one; this repairs it
    //  * based on what kind of field it is:
    //  *   - A parent field (its latestVersion has no required_with_field_id)
    //  *     is appended to the end: form_order = current max + 1.
    //  *   - An additional field (belongs to a parent via
    //  *     required_with_field_id) is placed right after its parent:
    //  *     form_order = parent's form_order + 1, and every field already at
    //  *     or past that position shifts forward by one to make room.
    //  *
    //  * Null-order parents are fixed before null-order additional fields in
    //  * the same pass, so an additional field whose parent was ALSO null can
    //  * still resolve correctly in one pass rather than needing another
    //  * trip through the outer loop.
    //  */
    // private static function fixNullFormOrders(string $modelClass): bool
    // {
    //     $changed = false;

    //     $fields = $modelClass::with('latestVersion.requiredWithField.latestVersion')->get();

    //     $nullOrderFields = $fields->filter(
    //         fn ($field) => $field->latestVersion !== null && $field->latestVersion->form_order === null
    //     );

    //     if ($nullOrderFields->isEmpty()) {
    //         return false;
    //     }

    //     $runningMax = $fields
    //         ->map(fn ($field) => $field->latestVersion?->form_order)
    //         ->filter(fn ($value) => $value !== null)
    //         ->max() ?? 0;

    //     [$nullParents, $nullAdditionalFields] = $nullOrderFields->partition(
    //         fn ($field) => $field->latestVersion->required_with_field_id === null
    //     );

    //     foreach ($nullParents as $field) {
    //         $runningMax++;
    //         $field->latestVersion->update(['form_order' => $runningMax]);
    //         $changed = true;
    //     }

    //     foreach ($nullAdditionalFields as $field) {
    //         $parentOrder = $field->latestVersion->requiredWithField?->latestVersion?->form_order;

    //         if ($parentOrder === null) {
    //             // The parent's own form_order is still unresolved (e.g. it
    //             // wasn't part of this pass's null-parent set) — skip for
    //             // now, the outer resolve() loop will retry next pass.
    //             continue;
    //         }

    //         $targetOrder = $parentOrder + 1;

    //         // The field's own form_order is still null at this point, so
    //         // it can never match this shift itself — safe to run before
    //         // assigning it below.
    //         FormOrderInserter::makeRoomAt(get_class($field->latestVersion), $targetOrder);

    //         $field->latestVersion->update(['form_order' => $targetOrder]);
    //         $changed = true;
    //     }

    //     return $changed;
    // }
}