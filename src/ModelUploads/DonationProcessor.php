<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\ModelUploads;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use FromHome\ModelUpload\Exceptions\CannotProcessRecord;
use FromHome\ModelUpload\Processor\ModelUploadRecordProcessor;
use Inisiatif\Distribution\Financings\Models\Donation;

final class DonationProcessor implements ModelUploadRecordProcessor
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

        $donation = Donation::query()->updateOrCreate([
            'identification_number' => $record->getMetaData('identification_number')
        ],[
            'branch_id' => $record->getMetaData('branch_id'),
            'donor_id' => $record->getMetaData('donor_id'),
            'employee_id' => $record->getMetaData('employee_id'),
            'transaction_date' => $record->getMetaData('transaction_date'),
            'transaction_status' => $record->getMetaData('transaction_status'),
            'amount' => (float) $record->getMetaData('amount'),
            'total_amount' => (float) $record->getMetaData('total_amount'),
            'donation_type' => $record->getMetaData('donation_type')
        ]);

        $record->update([
            'model_id' => $donation->getKey(),
        ]);

        return $donation;
    }
}
