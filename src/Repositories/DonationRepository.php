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
use Inisiatif\Package\Common\Abstracts\AbstractRepository;
use Inisiatif\Distribution\Financings\Models\DonationDetail;
use Inisiatif\Distribution\Financings\Includes\IncludedProgram;
use Inisiatif\Distribution\Financings\Filters\DateIntervalFilter;
use Inisiatif\Distribution\Financings\Scopes\DonationSearchScope;
use Inisiatif\Distribution\Financings\Includes\IncludedFundingType;

final class DonationRepository extends AbstractRepository
{
    protected $model = Donation::class;

    public function fetchAll(Request $request): LengthAwarePaginator
    {
        $branch = $request->user()->getLoginable()->getAttribute('branch');

        $branchId = $request->user()->getLoginable()->getAttribute('branch_id');

        $donationDetailTable = ModelShared::getDonationDetailModel()->getTable();

        if ($branch && $branch->getAttribute('is_head_office') === false) {
            $builder = $this->newVerifiedDonationQuery(false, $branchId);
        } else {
            $builder = $this->newVerifiedDonationQuery(true, null);
        }

        $builder = $builder
            ->orderBy(ModelShared::getDonationModel()->getTable().'.transaction_date', 'desc')
            ->withGlobalScope(DonationSearchScope::class, new DonationSearchScope);

        $query = $this->queryBuilder($builder, $request, $donationDetailTable);

        return $query
            ->paginate($request->integer('limit', 5))
            ->appends((array) $request->query());
    }

    public function findVerifiedDetail(
        string $identificationNumber,
        int|string $donationDetailId,
        string $donorIdentificationNumber,
        bool $isHeadOffice = true,
        mixed $branchId = null,
    ): ?Donation {
        $donationTable = ModelShared::getDonationModel()->getTable();

        $donationDetailTable = ModelShared::getDonationDetailModel()->getTable();

        $donorTable = ModelShared::getDonorModel()->getTable();

        /** @var Donation|null $donation */
        $donation = $this->newVerifiedDonationQuery($isHeadOffice, $branchId)
            ->where($donationTable.'.identification_number', $identificationNumber)
            ->where($donationDetailTable.'.id', $donationDetailId)
            ->where($donorTable.'.identification_number', $donorIdentificationNumber)
            ->first();

        return $donation;
    }

    /**
     * @return array<string, string>
     */
    public function getMissingVerifiedDetailErrors(
        string $identificationNumber,
        int|string $donationDetailId,
        string $donorIdentificationNumber,
        bool $isHeadOffice = true,
        mixed $branchId = null,
    ): array {
        $donationTable = ModelShared::getDonationModel()->getTable();

        $donationDetailTable = ModelShared::getDonationDetailModel()->getTable();

        $donorTable = ModelShared::getDonorModel()->getTable();

        $errors = [];

        $identificationExists = $this->newVerifiedDonationQuery($isHeadOffice, $branchId)
            ->where($donationTable.'.identification_number', $identificationNumber)
            ->exists();

        if ($identificationExists === false) {
            $errors['identification_number'] = "identification_number {$identificationNumber} does not exist";
        }

        $donationDetailExists = $this->newVerifiedDonationQuery($isHeadOffice, $branchId)
            ->where($donationDetailTable.'.id', $donationDetailId)
            ->exists();

        if ($donationDetailExists === false) {
            $errors['donation_detail_id'] = "donation_detail_id {$donationDetailId} does not exist";
        }

        $donorExists = $this->newVerifiedDonationQuery($isHeadOffice, $branchId)
            ->where($donorTable.'.identification_number', $donorIdentificationNumber)
            ->exists();

        if ($donorExists === false) {
            $errors['donor_identification_number'] = "donor_identification_number {$donorIdentificationNumber} does not exist";
        }

        if ($errors === []) {
            $errors['file'] = 'identification_number, donation_detail_id, and donor_identification_number do not match';
        }

        return $errors;
    }

    public function queryBuilder(Builder $builder, Request $request, ?string $donationDetailTable = null, ?string $donorTable = null): QueryBuilder
    {
        $donationDetailTable ??= (new DonationDetail)->getTable();

        $donorTable ??= ModelShared::getDonorModel()->getTable();

        return QueryBuilder::for($builder, $request)->allowedFilters([
            AllowedFilter::exact('branch', 'branch_id'),
            AllowedFilter::exact('employee', 'employee_id'),
            AllowedFilter::exact('donor', 'donor_id'),
            AllowedFilter::exact('status', 'transaction_status'),
            AllowedFilter::exact('donation_type', 'donation_type'),
            AllowedFilter::exact('donor_name', $donorTable.'name'),
            AllowedFilter::exact('funding_type', $donationDetailTable.'.funding_type_id'),
            AllowedFilter::exact('program', $donationDetailTable.'.program_id'),
            AllowedFilter::custom('transaction_date', new DateIntervalFilter),
        ])->allowedIncludes([
            AllowedInclude::relationship('branch'),
            AllowedInclude::relationship('employee'),
            AllowedInclude::relationship('donor'),
            AllowedInclude::relationship('details'),
            AllowedInclude::custom('funding_type', new IncludedFundingType),
            AllowedInclude::custom('program', new IncludedProgram),
        ]);
    }

    private function newVerifiedDonationQuery(bool $isHeadOffice, mixed $branchId): Builder
    {
        $donationTable = ModelShared::getDonationModel()->getTable();

        $donationDetailTable = ModelShared::getDonationDetailModel()->getTable();

        $fundingTypeTable = ModelShared::getFundingTypeModel()->getTable();

        $donationProgramTable = ModelShared::getProgramModel()->getTable();

        $branchTable = ModelRegistrar::getBranchModel()->getTable();

        $employeeTable = ModelRegistrar::getEmployeeModel()->getTable();

        $donorTable = ModelShared::getDonorModel()->getTable();

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
            $donationDetailTable.'.program_id',
        )
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
            ->groupBy($donationDetailTable.'.id');

        if ($isHeadOffice === false) {
            $builder->where($donationTable.'.branch_id', $branchId);
        }

        return $builder;
    }
}
