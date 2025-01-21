<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\ModelUploads;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\Models\Donation;
use FromHome\ModelUpload\Exceptions\CannotProcessRecord;
use FromHome\ModelUpload\Processor\ModelUploadRecordProcessor;

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
            'identification_number' => $record->getPayloadData('identification_number'),
        ], [
            'branch_id' => $record->getPayloadData('branch_id'),
            'donor_id' => $record->getPayloadData('donor_id'),
            'employee_id' => $record->getPayloadData('employee_id'),
            'transaction_date' => $record->getPayloadData('transaction_date'),
            'transaction_status' => $record->getPayloadData('transaction_status'),
            'amount' => (float) $record->getPayloadData('amount'),
            'total_amount' => (float) $record->getPayloadData('total_amount'),
            'donation_type' => $record->getPayloadData('donation_type'),
        ]);

        $record->update([
            'model_id' => $donation->getKey(),
        ]);

        return $donation;
    }
}
