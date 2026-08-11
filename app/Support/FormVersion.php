<?php

namespace App\Support;

/**
 * A fingerprint of the *entire current state* of a form-field model
 * (PersonalInfoField or MedicalHistoryField): which fields exist, each
 * one's version, and each one's form_order.
 *
 * Unlike a single field's own version_number, this changes on ANY mutation
 * to the form — adding a field, editing one, deleting one, or reordering —
 * so it's what callers should compare against to detect "the form changed
 * since I loaded it," as opposed to "this specific field changed."
 *
 * Purely derived (never stored), so it can never drift out of sync with
 * the actual data — recomputing it is one cheap query.
 */
class FormVersion
{
    public static function compute(string $modelClass): string
    {
        $signature = $modelClass::with('latestVersion')
            ->get()
            ->map(function ($field) {
                $version = $field->latestVersion;

                return $field->id . ':'
                    . ($version?->version_number ?? '0') . ':'
                    . ($version?->form_order ?? 'null');
            })
            ->sort()
            ->implode('|');

        return md5($signature);
    }
}