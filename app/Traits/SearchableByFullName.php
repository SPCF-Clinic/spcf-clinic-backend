<?php

namespace App\Traits;

use App\Support\NameSearch;
use Illuminate\Database\Eloquent\Builder;

trait SearchableByFullName
{
    /**
     * Scope a query to users whose EAV full name matches $search,
     * in any common name-part order.
     */
    public function scopeFullNameLike(Builder $query, string $search): Builder
    {
        return $query->whereIn(
            $query->getModel()->getQualifiedKeyName(),
            NameSearch::matchingUserIdsSubquery($search)
        );
    }
}