<?php

namespace App\Support;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Closure;

class NameSearch
{
    public const FIRST_NAME_FIELD_ID = 1;
    public const LAST_NAME_FIELD_ID = 2;
    public const MIDDLE_NAME_FIELD_ID = 3;

    /**
     * Closure usable as a whereIn(...) subquery to match user ids whose
     * EAV first/middle/last name fields contain $search, in any common
     * name order (first-last, last-first, first-middle-last).
     */
    public static function matchingUserIdsSubquery(string $search): Closure
    {
        return function (QueryBuilder $sub) use ($search) {
            $sub->select('pi_first.user_id')
                ->from('user_personal_infos as pi_first')
                ->join('user_personal_infos as pi_last', function ($join) {
                    $join->on('pi_last.user_id', '=', 'pi_first.user_id')
                         ->where('pi_last.personal_info_field_id', self::LAST_NAME_FIELD_ID);
                })
                ->leftJoin('user_personal_infos as pi_middle', function ($join) {
                    $join->on('pi_middle.user_id', '=', 'pi_first.user_id')
                         ->where('pi_middle.personal_info_field_id', self::MIDDLE_NAME_FIELD_ID);
                })
                ->where('pi_first.personal_info_field_id', self::FIRST_NAME_FIELD_ID)
                ->where(function ($q) use ($search) {
                    $q->whereRaw("CONCAT_WS(' ', pi_first.value, pi_last.value) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CONCAT_WS(' ', pi_last.value, pi_first.value) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CONCAT_WS(' ', pi_first.value, pi_middle.value, pi_last.value) LIKE ?", ["%{$search}%"]);
                });
        };
    }
}