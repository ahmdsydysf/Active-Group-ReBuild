<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasOrderingScopes
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_active'), true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy($this->qualifyColumn('sort_order'))->orderByDesc($this->qualifyColumn('id'));
    }
}
