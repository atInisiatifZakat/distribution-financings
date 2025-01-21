<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\ModelUploads;

use FromHome\ModelUpload\AbstractModelRecordImport;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use Illuminate\Support\Str;
use Inisiatif\Distribution\Financings\Models\Donation;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;

final class ImportDonationModelUpload extends AbstractModelRecordImport implements OnEachRow, WithHeadingRow, WithStartRow
{
    public function onRow(Row $row): void
    {
        ModelUploadRecord::create([
            'id' => \strtolower((string) Str::ulid()),
            'model_upload_file_id' => $this->uploadFile->getKey(),
            'payload' => $row->toArray(),
            'meta' => $this->meta,
            'model_type' => Donation::class,
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
