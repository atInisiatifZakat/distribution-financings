<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Actions;

use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\DataTransfers\CreateFinancingData;

final class CreateFinancingAction
{
    public function handle(CreateFinancingData $data): void
    {
        Financing::query()->create($data->toArray());
    }
}
