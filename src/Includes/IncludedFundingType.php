<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Includes;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Includes\IncludeInterface;

final class IncludedFundingType implements IncludeInterface
{
    public function __invoke(Builder $query, string $relation): void
    {
        $query->with(['funding' => function ($query) {
            $query->select('id', 'name');
        }]);
    }
}
