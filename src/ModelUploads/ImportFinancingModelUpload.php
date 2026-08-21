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
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Inisiatif\Distribution\Financings\Models\Financing;

final class ImportFinancingModelUpload extends AbstractModelRecordImport implements OnEachRow, WithCustomCsvSettings, WithHeadingRow, WithStartRow
{
    public function onRow(Row $row): void
    {
        ModelUploadRecord::create([
            'id' => \strtolower((string) Str::ulid()),
            'model_upload_file_id' => $this->uploadFile->getKey(),
            'payload' => $this->normalizePayload($row->toArray()),
            'meta' => $this->meta,
            'model_type' => Financing::class,
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8',
        ];
    }

    private function normalizePayload(array $row): array
    {
        return array_merge($row, [
            'identification_number' => $this->asString($this->value($row, [
                'identification_number',
                'transaction_id',
            ])),
            'amount' => $this->value($row, ['amount']),
            'donation_detail_id' => $this->value($row, ['donation_detail_id']),
            'donor_identification_number' => $this->asString($this->value($row, [
                'donor_number',
                'donor_identification_number',
            ])),
            'funding_type' => $this->value($row, ['funding_type']),
            'program' => $this->value($row, ['program']),
        ]);
    }

    private function value(array $row, array $keys): mixed
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalized[\strtolower(\str_replace(' ', '_', (string) $key))] = $value;
        }

        foreach ($keys as $key) {
            $lookup = \strtolower(\str_replace(' ', '_', $key));

            if (\array_key_exists($lookup, $normalized) && $normalized[$lookup] !== null && $normalized[$lookup] !== '') {
                return $normalized[$lookup];
            }
        }

        return null;
    }

    private function asString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (\is_numeric($value) && ! \is_string($value)) {
            return \sprintf('%.0f', $value);
        }

        return \trim((string) $value);
    }
}
