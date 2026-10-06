<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Actions;

use FromHome\ModelUpload\Models\ModelUploadFile;
use FromHome\ModelUpload\Models\ModelUploadRecord;
use Inisiatif\Distribution\Financings\Models\Financing;

final class ReplacePreviousFinancingUploadAction
{
    public function handle(string $distributionId): void
    {
        $financingIds = $this->financingIds($distributionId);

        if ($financingIds !== []) {
            Financing::query()
                ->where('distribution_id', $distributionId)
                ->whereIn('id', $financingIds)
                ->delete();
        }

        $fileIds = ModelUploadRecord::query()
            ->where('meta->distribution_id', $distributionId)
            ->pluck('model_upload_file_id')
            ->unique()
            ->filter()
            ->values();

        if ($fileIds->isNotEmpty()) {
            ModelUploadFile::query()->whereIn('id', $fileIds)->delete();
        }
    }

    public function amountForDistribution(string $distributionId): float
    {
        $financingIds = $this->financingIds($distributionId);

        if ($financingIds === []) {
            return 0.0;
        }

        return (float) Financing::query()
            ->where('distribution_id', $distributionId)
            ->whereIn('id', $financingIds)
            ->sum('amount');
    }

    /**
     * @return array<string, float>
     */
    public function amountsByDonationId(string $distributionId): array
    {
        $financingIds = $this->financingIds($distributionId);

        if ($financingIds === []) {
            return [];
        }

        return Financing::query()
            ->where('distribution_id', $distributionId)
            ->whereIn('id', $financingIds)
            ->get(['donation_id', 'amount'])
            ->groupBy(fn (Financing $financing): string => (string) $financing->getAttribute('donation_id'))
            ->map(fn ($rows): float => (float) $rows->sum('amount'))
            ->all();
    }

    /**
     * @return list<string>
     */
    private function financingIds(string $distributionId): array
    {
        return ModelUploadRecord::query()
            ->where('model_type', Financing::class)
            ->whereNotNull('model_id')
            ->where('meta->distribution_id', $distributionId)
            ->pluck('model_id')
            ->map(static fn (mixed $id): string => (string) $id)
            ->unique()
            ->values()
            ->all();
    }
}
