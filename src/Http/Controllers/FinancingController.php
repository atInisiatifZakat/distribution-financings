<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\Models\Distribution;
use Inisiatif\Distribution\Financings\Actions\CreateFinancingAction;
use Inisiatif\Distribution\Financings\Actions\DeleteFinancingAction;
use Inisiatif\Distribution\Financings\Http\Resources\FinancingResource;
use Inisiatif\Distribution\Financings\Repositories\FinancingRepository;
use Inisiatif\Distribution\Financings\DataTransfers\CreateFinancingData;
use Inisiatif\Distribution\Financings\Http\Requests\CreateFinancingRequest;
use Inisiatif\ModelShared\ModelShared;

final class FinancingController
{
    public function index(string $distributionId, Request $request, FinancingRepository $repository): JsonResource
    {
        return FinancingResource::collection($repository->fetchUsingDistribution($distributionId, $request));
    }

    public function store(CreateFinancingRequest $request, CreateFinancingAction $action)
    {
        /** @var Distribution|null $distribution */
        $distribution = Distribution::query()->find($request->input('distribution_id'));

        /** @var Donation|null $donation */
        $donation = Donation::query()->find($request->input('donation_id'));

        $donor = ModelShared::getDonorModel()::query()->find($request->input('donor_id'));

        if ($distribution === null || $donation === null || $donor === null) {
            throw ValidationException::withMessages(match (true) {
                $distribution === null => ['distribution_id' => 'Distribution doesn`t exists'],
                $donation === null => ['donation_id' => 'Donation doesn`t exists'],
                default => ['donor_id' => 'Donor doesn`t exists'],
            });
        }

        $distribution->loadMissing(['program', 'program_sector']);

        if ($distribution->isOverRequestAmount($request->integer('amount'))) {
            throw ValidationException::withMessages([
                'amount' => 'Over amount requested',
            ]);
        }

        $action->handle(
            new CreateFinancingData(array_merge($request->input(), [
                'donation_number' => $donation->getAttribute('identification_number'),
                'distribution_name' => $distribution->getAttribute('name'),
                'distribution_at' => $distribution->getAttribute('distribution_at'),
                'distribution_program_id' => $distribution->getAttribute('program_id'),
                'distribution_sector_id' => $distribution->getAttribute('program_sector_id'),
                'distribution_program_name' => $distribution->getAttribute('program')->getAttribute('name'),
                'distribution_sector_name' => $distribution->getAttribute('program_sector')->getAttribute('name'),
                'donor_id' => $donor->getAttribute('id'),
                'donor_identification_number' => $donor->getAttribute('identification_number'),
                'donation_detail_funding_type_id' => $request->input('donation_detail_funding_type_id'),
                'donation_detail_program_id' => $request->input('donation_detail_program_id'),
            ]))
        );

        return JsonResource::make([
            'status' => 'success',
            'message' => 'Donasi berhasil dipilih',
        ]);
    }
 
    public function delete(string $financingId, DeleteFinancingAction $action): JsonResource
    {
        $financing = Financing::query()->find($financingId);

        $donation = Donation::query()->find($financing->getAttribute('donation_id'));

        if ($financing === null || $donation === null) {
            throw ValidationException::withMessages(($financing === null) ? [
                'financing_id' => 'Financing doesn`t exists',
            ] : [
                'donation_id' => 'Donation doesn`t exists',
            ]);
        }

        $action->handle($financing, $donation);

        return JsonResource::make([
            'status' => 'success',
            'message' => 'Hapus donasi dari data financial berhasil',
        ]);

    }
}
