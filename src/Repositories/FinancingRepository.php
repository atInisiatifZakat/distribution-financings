<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Package\Common\Abstracts\AbstractRepository;
use Inisiatif\Distribution\Financings\Filters\DateIntervalFilter;

final class FinancingRepository extends AbstractRepository
{
    protected $model = Financing::class;

    public function fetchAll(Request $request)
    {
        $builder = $this->getModel()->newQuery()->orderBy('created_at', 'desc');

        return $this->queryBuilder($builder, $request)
            ->paginate($request->integer('limit', 15))
            ->appends((array) $request->query());
    }

    public function fetchUsingDistribution(string $distributionId, Request $request)
    {
        $builder = $this->getModel()->newQuery()
            ->where('distribution_id', '=', $distributionId)
            ->with(['donation', 'donation.branch', 'donation.employee', 'donation.donor'])
            ->orderBy('created_at', 'desc');

        return $this->queryBuilder($builder, $request)
            ->paginate($request->integer('limit', 5))
            ->appends((array) $request->query());
    }

    public function queryBuilder(Builder $builder, Request $request): QueryBuilder
    {
        return QueryBuilder::for($builder, $request)->allowedFilters([
            AllowedFilter::exact('donation', 'donation_id'),
            AllowedFilter::exact('distribution', 'distribution_id'),
            AllowedFilter::exact('program', 'distribution_program_id'),
            AllowedFilter::exact('program_sector', 'distribution_sector_id'),
            AllowedFilter::custom('created_at', new DateIntervalFilter, 'created_at'),
            AllowedFilter::custom('distribution_at', new DateIntervalFilter, 'distribution_at'),
        ])->allowedIncludes([
            AllowedInclude::relationship('donation'),
            AllowedInclude::relationship('distribution'),
            AllowedInclude::relationship('program'),
            AllowedInclude::relationship('program_sector'),
        ]);
    }
}
