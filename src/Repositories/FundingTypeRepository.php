<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Inisiatif\ModelShared\ModelShared;

final class FundingTypeRepository
{
    public function fetchAll(Request $request): Collection
    {
        return QueryBuilder::for(ModelShared::getFundingTypeModel()->newQuery()->orderBy('name'), $request)
            ->allowedFilters([
                AllowedFilter::partial('name', 'name'),
                AllowedFilter::exact('id', 'id'),
            ])
            ->get();
    }
}
