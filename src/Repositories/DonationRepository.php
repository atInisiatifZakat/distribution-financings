<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\Distribution\Financings\Models\Donation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Inisiatif\Package\Common\Abstracts\AbstractRepository;
use Inisiatif\Distribution\Financings\Scopes\DonationSearchScope;

final class DonationRepository extends AbstractRepository
{
    protected $model = Donation::class;

    public function fetchAll(Request $request): LengthAwarePaginator
    {
        $branch = $request->user()->getLoginable()->getAttribute('branch');

        if ($branch && $branch->getAttribute('is_head_office') === false) {
            $builder = $this->getModel()->newQuery()
                ->select('donations.id', 'branches.id AS branch_id', 'employees.id AS employee_id',
                    'donors.id AS donor_id', 'donations.identification_number', 'donations.donation_type',
                    'branches.name AS branch_name', 'donors.name AS donor_name', 'employees.name AS employee_name',
                    'donations.transaction_date', 'donations.transaction_status', 'donations.amount',
                    'donations.total_amount')
                ->join('branches', 'donations.branch_id', '=', 'branches.id')
                ->join('donors', 'donations.donor_id', '=', 'donors.id')
                ->join('employees', 'donations.employee_id', '=', 'employees.id')
                ->where('donations.branch_id', $request->user()->getLoginable()->getAttribute('branch_id'))
                ->where('transaction_status', 'VERIFIED')
                ->orderBy('transaction_date', 'desc')
                ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);
        } elseif ($branch && $branch->getAttribute('is_head_office') === true) {
            $builder = $this->getModel()->newQuery()
                ->select('donations.id', 'branches.id AS branch_id', 'employees.id AS employee_id',
                    'donors.id AS donor_id', 'donations.identification_number', 'donations.donation_type',
                    'branches.name AS branch_name', 'donors.name AS donor_name', 'employees.name AS employee_name',
                    'donations.transaction_date', 'donations.transaction_status', 'donations.amount',
                    'donations.total_amount')
                ->join('branches', 'donations.branch_id', '=', 'branches.id')
                ->join('donors', 'donations.donor_id', '=', 'donors.id')
                ->join('employees', 'donations.employee_id', '=', 'employees.id')
                ->where('transaction_status', 'VERIFIED')
                ->orderBy('transaction_date', 'desc')
                ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);
        }

        return $this->queryBuilder($builder, $request)
            ->paginate($request->integer('limit', 5))
            ->appends((array) $request->query());
    }

    public function queryBuilder(Builder $builder, Request $request): QueryBuilder
    {
        return QueryBuilder::for($builder, $request)->allowedFilters([
            AllowedFilter::exact('branch', 'branch_id'),
            AllowedFilter::exact('employee', 'employee_id'),
            AllowedFilter::exact('status', 'transaction_status'),
            AllowedFilter::exact('donation_type', 'donation_type'),
        ])->allowedIncludes([
            AllowedInclude::relationship('branch'),
            AllowedInclude::relationship('employee'),
            AllowedInclude::relationship('donor'),
        ]);
    }
}
