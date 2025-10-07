<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\Package\User\ModelRegistrar;
use Inisiatif\Distribution\Financings\Models\Donation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Inisiatif\ModelShared\Registrars\DonorModelRegistrar;
use Inisiatif\Package\Common\Abstracts\AbstractRepository;
use Inisiatif\Distribution\Financings\Scopes\DonationSearchScope;

final class DonationRepository extends AbstractRepository
{
    protected $model = Donation::class;

    public function fetchAll(Request $request): LengthAwarePaginator
    {
        $branch = $request->user()->getLoginable()->getAttribute('branch');

        $donationTable = ModelRegistrar::getDonationModel()->getTable();

        $branchTable = ModelRegistrar::getBranchModel()->getTable();

        $employeeTable = ModelRegistrar::getEmployeeModel()->getTable();

        $donorTable = app(DonorModelRegistrar::class)->getTableName();

        if ($branch && $branch->getAttribute('is_head_office') === false) {
            $builder = $this->getModel()->newQuery()
                ->select($donationTable.'.id', $branchTable.'.id AS branch_id', $employeeTable.'.id AS employee_id',
                    $donorTable.'.id AS donor_id', $donationTable.'.identification_number', $donationTable.'.type AS donation_type',
                    $branchTable.'.name AS branch_name', $donorTable.'.name AS donor_name', $employeeTable.'.name AS employee_name',
                    $donationTable.'.transaction_date', $donationTable.'.transaction_status', $donationTable.'.amount',
                    $donationTable.'.total_amount')
                ->join($branchTable, $donationTable.'.branch_id', '=', $branchTable.'.id')
                ->join($donorTable, $donationTable.'.donor_id', '=', $donorTable.'.id')
                ->join($employeeTable, $donationTable.'.employee_id', '=', $employeeTable.'.id')
                ->where($donationTable.'.branch_id', $request->user()->getLoginable()->getAttribute('branch_id'))
                ->where($donationTable.'.transaction_status', 'VERIFIED')
                ->orderBy($donationTable.'.transaction_date', 'desc')
                ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);
        } elseif ($branch && $branch->getAttribute('is_head_office') === true) {
            $builder = $this->getModel()->newQuery()
                ->select($donationTable.'.id', $branchTable.'.id AS branch_id', $employeeTable.'.id AS employee_id',
                    $donorTable.'.id AS donor_id', $donationTable.'.identification_number', $donationTable.'.type AS donation_type',
                    $branchTable.'.name AS branch_name', $donorTable.'.name AS donor_name', $employeeTable.'.name AS employee_name',
                    $donationTable.'.transaction_date', $donationTable.'.transaction_status', $donationTable.'.amount',
                    $donationTable.'.total_amount')
                ->join($branchTable, $donationTable.'.branch_id', '=', $branchTable.'.id')
                ->join($donorTable, $donationTable.'.donor_id', '=', $donorTable.'.id')
                ->join($employeeTable, $donationTable.'.employee_id', '=', $employeeTable.'.id')
                ->where($donationTable.'.transaction_status', 'VERIFIED')
                ->orderBy($donationTable.'.transaction_date', 'desc')
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
