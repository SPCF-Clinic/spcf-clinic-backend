<?php

namespace App\Support;

/**
 * Closes form_order gaps for a form-field model (PersonalInfoField,
 * MedicalHistoryField, ...).
 *
 * Reordering never creates gaps on its own, but a deletion does — a
 * deleted field's form_order value is never reclaimed, so the values in
 * use can end up exceeding the total number of fields. Multiple
 * deletions without a compaction in between can leave several such gaps
 * scattered at different positions.
 *
 * Run after every reorder AND after every deletion — deletion is the only
 * operation that can actually produce a gap, and running it there too
 * means gaps are always closed one at a time, right as they're created,
 * so they're never non-contiguous by the time this runs.
 */
class FormOrderCompactor
{
    /**
     * Let x = the total number of fields (of this model). form_order
     * values are never meant to exceed x — but a deletion, or a reorder
     * that moves a field a long distance, can leave one behind (the
     * vacated original position is never reclaimed on its own), so this
     * closes the gap(s):
     *
     *   1. max = the highest current form_order. If max <= x, there's
     *      nothing to do.
     *   2. Otherwise, walk i = 1, 2, ..., max and note which values of i
     *      have no field holding them — the missing form_order values.
     *      Let a = how many are missing, and gapTop = the highest one.
     *   3. Every non-ignored field whose form_order exceeds gapTop shifts
     *      down — by a if its form_order is below $targetFormOrder, or by
     *      a + b (b = count of $ignoreFieldIds) if it's above.
     *
     * The two-zone split matters whenever a field moves further than one
     * position: say field A moves from 5 to 27 in a 27-field form. Only
     * the one field that was already at/after 27 gets pushed forward (to
     * 28) — positions 6-26 are deliberately left untouched by the move
     * itself, since nothing needed to make room for A there. So after the
     * move: 5 is vacant, 6-26 are unchanged, A sits at 27 (ignored, must
     * not move again), and the pushed field sits at 28.
     *   - a = 1 (only position 5 is missing), b = 1 (A is the one ignored
     *     field), gapTop = 5.
     *   - Fields 6-26 (between gapTop and the target) each shift down by
     *     just a=1, sliding into 5-25 to close the vacated gap.
     *   - The pushed field at 28 (beyond the target) shifts down by
     *     a+b=2, landing at 26 — one extra slot to skip over A, which
     *     is sitting at 27 and staying there.
     *   Using a flat a+b for every field here (the earlier version of this
     *   method) would incorrectly drag 6-26 down too, colliding with the
     *   untouched fields below them; using a flat a would leave the pushed
     *   field colliding with A's new position. The split avoids both.
     *
     * $ignoreFieldIds and $targetFormOrder both come from the reorder
     * case — the field(s) just deliberately moved must be left exactly
     * where the reorder put them. Deletion has neither a target nor
     * anything to ignore (b=0), which collapses this back to the single
     * flat shift the simple gap-closing case always needed.
     */
    public static function compact(string $modelClass, array $ignoreFieldIds = [], ?int $targetFormOrder = null): void
    {
        $fields = $modelClass::with('latestVersion')->get();
        $x = $fields->count();

        $orders = $fields
            ->map(fn ($field) => $field->latestVersion?->form_order)
            ->filter(fn ($value) => $value !== null)
            ->values();

        $maxOrder = $orders->max();

        if ($maxOrder === null || $maxOrder <= $x) {
            return;
        }

        $present = $orders->flip();

        $missing = collect(range(1, $maxOrder))->reject(fn ($i) => $present->has($i));

        $a = $missing->count();

        if ($a === 0) {
            return;
        }

        $gapTop = $missing->max();
        $b = count($ignoreFieldIds);

        $fields
            ->reject(fn ($field) => in_array($field->id, $ignoreFieldIds, true))
            ->filter(fn ($field) => $field->latestVersion?->form_order !== null
                && $field->latestVersion->form_order > $gapTop)
            ->sortBy(fn ($field) => $field->latestVersion->form_order)
            ->each(function ($field) use ($a, $b, $targetFormOrder) {
                $current = $field->latestVersion->form_order;

                $shift = ($targetFormOrder !== null && $current > $targetFormOrder)
                    ? $a + $b
                    : $a;

                $field->latestVersion->update(['form_order' => $current - $shift]);
            });
    }
}