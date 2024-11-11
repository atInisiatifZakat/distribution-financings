<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

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

final class FinancingController
{
    public function index(string $distributionId, FinancingRepository $repository): JsonResource
    {
        return FinancingResource::collection($repository->fetchUsingDistribution($distributionId));
    }

    public function store(CreateFinancingRequest $request, CreateFinancingAction $action)
    {
        /** @var Distribution|null $distribution */
        $distribution = Distribution::query()->find($request->input('distribution_id'))->loadMissing(['program', 'program_sector']);

        /** @var Donation|null $donation */
        $donation = Donation::query()->find($request->input('donation_id'));

        if ($distribution === null || $donation === null) {
            throw ValidationException::withMessages(($distribution === null) ? [
                'distribution_id' => 'Distribution doesn`t exists',
            ] : [
                'donation_id' => 'Distribution doesn`t exists',
            ]);
        }

        if ($distribution->isOverRequestAmount($request->integer('amount'))) {
            throw ValidationException::withMessages([
                'amount' => 'Amount must be the same as distribution amount',
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
