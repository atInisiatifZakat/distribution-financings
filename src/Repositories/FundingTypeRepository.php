<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\Distribution\Financings\Models\FundingType;
use Inisiatif\Package\Common\Abstracts\AbstractRepository;

final class FundingTypeRepository extends AbstractRepository
{
    protected $model = FundingType::class;

    public function fetchAll(Request $request): Collection
    {
        $builder = $this->getModel()->newQuery()->orderBy('name');

        return $this->queryBuilder($builder, $request)->get();
    }

    public function queryBuilder(Builder $builder, Request $request): QueryBuilder
    {
        return QueryBuilder::for($builder, $request)->allowedFilters([
            AllowedFilter::partial('name', 'name'),
            AllowedFilter::exact('id', 'id'),
        ]);
    }
}
