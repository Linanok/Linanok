<?php

namespace App\Filament\Resources\LinkResource\Filters\QueryBuilder\Constraints\HasPasswordConstraint\Operators;

use Illuminate\Database\Eloquent\Builder;

class IsTrueOperator extends \Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator
{
    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        if ($this->isInverse()) {
            return $query->whereNull('password');
        }

        return $query->whereNotNull('password');
    }
}
