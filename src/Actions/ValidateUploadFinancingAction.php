<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Inisiatif\Distribution\Financings\Repositories\DonationRepository;

final class ValidateUploadFinancingAction
{
    public function __construct(
        private readonly DonationRepository $donationRepository,
    ) {}

    public function handle(UploadedFile $file, bool $isHeadOffice, mixed $branchId): void
    {
        $errors = [];

        $uploadedAmounts = [];

        foreach ($this->readRows($file) as $index => $row) {
            $line = $index + 2;

            $identificationNumber = $this->asString($this->value($row, [
                'identification_number',
                'transaction_id',
            ]));

            $donationDetailId = $this->value($row, ['donation_detail_id']);

            $donorIdentificationNumber = $this->asString($this->value($row, [
                'donor_number',
                'donor_identification_number',
            ]));

            if ($identificationNumber === null && ($donationDetailId === null || $donationDetailId === '') && $donorIdentificationNumber === null) {
                continue;
            }

            if ($identificationNumber === null) {
                $errors['identification_number'][] = "Row {$line}: identification_number is required";
            }

            if ($donationDetailId === null || $donationDetailId === '') {
                $errors['donation_detail_id'][] = "Row {$line}: donation_detail_id is required";
            }

            if ($donorIdentificationNumber === null) {
                $errors['donor_identification_number'][] = "Row {$line}: donor_identification_number is required";
            }

            if ($identificationNumber === null || $donationDetailId === null || $donationDetailId === '' || $donorIdentificationNumber === null) {
                continue;
            }

            $donation = $this->donationRepository->findVerifiedDetail(
                $identificationNumber,
                $donationDetailId,
                $donorIdentificationNumber,
                $isHeadOffice,
                $branchId,
            );

            if ($donation !== null) {
                $amount = (float) $this->value($row, ['amount']);
                $donationId = (string) $donation->getKey();
                $uploadedAmounts[$donationId] = ($uploadedAmounts[$donationId] ?? 0) + $amount;

                if ($donation->isOverAmount($uploadedAmounts[$donationId])) {
                    $errors['amount'][] = "Row {$line}: total financing amount exceeds donation amount for identification_number {$identificationNumber}";
                }

                continue;
            }

            foreach ($this->donationRepository->getMissingVerifiedDetailErrors(
                $identificationNumber,
                $donationDetailId,
                $donorIdentificationNumber,
                $isHeadOffice,
                $branchId,
            ) as $field => $message) {
                $errors[$field][] = "Row {$line}: {$message}";
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRows(UploadedFile $file): array
    {
        $handle = \fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'Unable to read uploaded file',
            ]);
        }

        $header = \fgetcsv($handle, 0, ';');

        if ($header === false) {
            \fclose($handle);

            throw ValidationException::withMessages([
                'file' => 'CSV header is required',
            ]);
        }

        $header[0] = \preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]) ?? (string) $header[0];

        $headers = \array_map(static function (mixed $column): string {
            return \strtolower(\str_replace(' ', '_', \trim((string) $column)));
        }, $header);

        $rows = [];

        while (($data = \fgetcsv($handle, 0, ';')) !== false) {
            if ($this->isEmptyRow($data)) {
                continue;
            }

            $rows[] = \array_combine($headers, \array_pad($data, \count($headers), null)) ?: [];
        }

        \fclose($handle);

        return $rows;
    }

    /**
     * @param  list<string|null>  $data
     */
    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($value !== null && \trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<string>  $keys
     */
    private function value(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (\array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
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
