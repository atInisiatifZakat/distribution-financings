<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Includes;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Includes\IncludeInterface;

final class IncludedProgram implements IncludeInterface
{
    public function __invoke(Builder $query, string $relation): void
    {
        $query->with(['program' => function ($query): void {
            $query->select('id', 'name');
        }]);
    }
}
