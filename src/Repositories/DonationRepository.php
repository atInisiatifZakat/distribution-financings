<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Repositories;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Inisiatif\ModelShared\ModelShared;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\Package\User\ModelRegistrar;
use Inisiatif\Distribution\Financings\Models\Donation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Inisiatif\Distribution\Financings\Filters\DateIntervalFilter;
use Inisiatif\Package\Common\Abstracts\AbstractRepository;
use Inisiatif\Distribution\Financings\Models\DonationDetail;
use Inisiatif\Distribution\Financings\Includes\IncludedProgram;
use Inisiatif\Distribution\Financings\Scopes\DonationSearchScope;
use Inisiatif\Distribution\Financings\Includes\IncludedFundingType;

final class DonationRepository extends AbstractRepository
{
    protected $model = Donation::class;

    public function fetchAll(Request $request): LengthAwarePaginator
    {
        $branch = $request->user()->getLoginable()->getAttribute('branch');

        $donationTable = ModelShared::getDonationModel()->getTable();

        $donationDetailTable = ModelShared::getDonationDetailModel()->getTable();

        $fundingTypeTable = ModelShared::getFundingTypeModel()->getTable();

        $donationProgramTable = ModelShared::getProgramModel()->getTable();

        $branchTable = ModelRegistrar::getBranchModel()->getTable();

        $employeeTable = ModelRegistrar::getEmployeeModel()->getTable();

        $donorTable = ModelShared::getDonorModel()->getTable();

        if ($branch && $branch->getAttribute('is_head_office') === false) {
            $builder = $this->getModel()->newQuery()->select(
                $donationTable.'.id',
                $branchTable.'.id AS branch_id',
                $employeeTable.'.id AS employee_id',
                $donorTable.'.id AS donor_id',
                $donationTable.'.identification_number',
                $donationTable.'.type AS donation_type',
                $branchTable.'.name AS branch_name',
                $donorTable.'.name AS donor_name',
                $donorTable.'.identification_number AS donor_identification_number',
                $employeeTable.'.name AS employee_name',
                $donationTable.'.transaction_date',
                $donationTable.'.transaction_status',
                $donationTable.'.amount',
                $donationTable.'.total_amount',
                $donationDetailTable.'.id AS donation_detail_id',
                $donationDetailTable.'.funding_type_id',
                $donationDetailTable.'.program_id', )
                ->join($branchTable, $donationTable.'.branch_id', '=', $branchTable.'.id')
                ->join($donorTable, $donationTable.'.donor_id', '=', $donorTable.'.id')
                ->join($employeeTable, $donationTable.'.employee_id', '=', $employeeTable.'.id')
                ->join($donationDetailTable, $donationTable.'.id', '=', $donationDetailTable.'.donation_id')
                ->leftJoin($fundingTypeTable.' as funding', $donationDetailTable.'.funding_type_id', '=', 'funding.id')
                ->leftJoin($donationProgramTable.' as program', $donationDetailTable.'.program_id', '=', 'program.id')
                ->where($donationTable.'.branch_id', $request->user()->getLoginable()->getAttribute('branch_id'))
                ->where($donationTable.'.transaction_status', 'VERIFIED')
                ->groupBy($branchTable.'.id')
                ->groupBy($employeeTable.'.id')
                ->groupBy($donorTable.'.id')
                ->groupBy($donationTable.'.id')
                ->groupBy($donationDetailTable.'.id')
                ->orderBy($donationTable.'.transaction_date', 'desc')
                ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);
        } elseif ($branch && $branch->getAttribute('is_head_office') === true) {
            $builder = $this->getModel()->newQuery()->select(
                $donationTable.'.id',
                $branchTable.'.id AS branch_id',
                $employeeTable.'.id AS employee_id',
                $donorTable.'.id AS donor_id',
                $donationTable.'.identification_number',
                $donationTable.'.type AS donation_type',
                $branchTable.'.name AS branch_name',
                $donorTable.'.name AS donor_name',
                $donorTable.'.identification_number AS donor_identification_number',
                $employeeTable.'.name AS employee_name',
                $donationTable.'.transaction_date',
                $donationTable.'.transaction_status',
                $donationTable.'.amount',
                $donationTable.'.total_amount',
                $donationDetailTable.'.id AS donation_detail_id',
                $donationDetailTable.'.funding_type_id',
                $donationDetailTable.'.program_id', )
                ->join($branchTable, $donationTable.'.branch_id', '=', $branchTable.'.id')
                ->join($donorTable, $donationTable.'.donor_id', '=', $donorTable.'.id')
                ->join($employeeTable, $donationTable.'.employee_id', '=', $employeeTable.'.id')
                ->join($donationDetailTable, $donationTable.'.id', '=', $donationDetailTable.'.donation_id')
                ->leftJoin($fundingTypeTable.' as funding', $donationDetailTable.'.funding_type_id', '=', 'funding.id')
                ->leftJoin($donationProgramTable.' as program', $donationDetailTable.'.program_id', '=', 'program.id')
                ->where($donationTable.'.transaction_status', 'VERIFIED')
                ->groupBy($branchTable.'.id')
                ->groupBy($employeeTable.'.id')
                ->groupBy($donorTable.'.id')
                ->groupBy($donationTable.'.id')
                ->groupBy($donationDetailTable.'.id')
                ->orderBy($donationTable.'.transaction_date', 'desc')
                ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);
        }

        $query = $this->queryBuilder($builder, $request, $donationDetailTable);

        return $query
            ->paginate($request->integer('limit', 5))
            ->appends((array) $request->query());
    }

    public function queryBuilder(Builder $builder, Request $request, ?string $donationDetailTable = null, ?string $donorTable = null): QueryBuilder
    {
        $donationDetailTable ??= (new DonationDetail)->getTable();

        $donorTable ??= ModelShared::getDonorModel()->getTable();

        return QueryBuilder::for($builder, $request)->allowedFilters([
            AllowedFilter::exact('branch', 'branch_id'),
            AllowedFilter::exact('employee', 'employee_id'),
            AllowedFilter::exact('status', 'transaction_status'),
            AllowedFilter::exact('donation_type', 'donation_type'),
            AllowedFilter::exact('donor_name', $donorTable.'name'),
            AllowedFilter::exact('funding_type', $donationDetailTable.'.funding_type_id'),
            AllowedFilter::exact('program', $donationDetailTable.'.program_id'),
            AllowedFilter::custom('transaction_date', new DateIntervalFilter()),
        ])->allowedIncludes([
            AllowedInclude::relationship('branch'),
            AllowedInclude::relationship('employee'),
            AllowedInclude::relationship('donor'),
            AllowedInclude::relationship('details'),
            AllowedInclude::custom('funding_type', new IncludedFundingType),
            AllowedInclude::custom('program', new IncludedProgram),
        ]);
    }
}
