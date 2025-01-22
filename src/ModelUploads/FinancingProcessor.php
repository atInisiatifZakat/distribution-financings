<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\ModelUploads;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use FromHome\ModelUpload\Exceptions\CannotProcessRecord;
use FromHome\ModelUpload\Processor\ModelUploadRecordProcessor;
use Inisiatif\Distribution\Financings\Models\Distribution;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\Models\Financing;

final class FinancingProcessor implements ModelUploadRecordProcessor
{
    /**
     * @var bool
     */
    protected $usingTemplate = false;

    /**
     * @throws Throwable
     */
    public function process(ModelUploadRecord $record): Model
    {
        if ($record->getAttribute('model_id') !== null || $record->getAttribute('error_message') !== null) {
            throw CannotProcessRecord::make('Model record already processed');
        }

        if ($record->getPayloadData('is_duplicated')) {
            throw CannotProcessRecord::make('Beneficiary already exist');
        }

        /** @var Donation|null $donation */
        $donation = Donation::query()->where('identification_number', $record->getPayloadData('identification_number'))->first();

        if($donation === null){
            throw CannotProcessRecord::make('Donation not found');
        }

        /** @var Distribution|null $distribution */
        $distribution = Distribution::query()->where('id', $record->getMetaData('distribution_id'))->first();

        if($distribution === null){
            throw CannotProcessRecord::make('Program not found');
        }

        $isOverAmount = $distribution->isOverRequestAmount((float) $record->getPayloadData('amount'));

        if($isOverAmount){
            throw CannotProcessRecord::make('Amount must be the same as distribution amount');
        }

        $financing = Financing::query()->create([
            'donation_id' => $donation->getKey(),
            'donation_number' => $donation->getAttribute('identification_number'),
            'distribution_id' => $distribution->getKey(),
            'distribution_name' => $distribution->getAttribute('name'),
            'distribution_at' => $distribution->getAttribute('distribution_at'),
            'distribution_program_id' => $distribution->getAttribute('program_id'),
            'distribution_program_name' => $distribution->getAttribute('program')->getAttribute('name'),
            'distribution_sector_id' => $distribution->getAttribute('program_sector_id'),
            'distribution_sector_name' => $distribution->getAttribute('program_sector')->getAttribute('name'),
            'amount' => (float) $record->getPayloadData('amount'),
        ]);

        $record->update([
            'model_id' => $financing->getKey(),
        ]);

        return $financing;
    }
}
