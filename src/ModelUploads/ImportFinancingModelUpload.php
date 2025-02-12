<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\ModelUploads;

use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use FromHome\ModelUpload\AbstractModelRecordImport;
use Inisiatif\Distribution\Financings\Models\Donation;

final class ImportFinancingModelUpload extends AbstractModelRecordImport implements OnEachRow, WithHeadingRow, WithStartRow
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
