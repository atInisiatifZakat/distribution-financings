<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Actions;

use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\Models\Financing;

final class DeleteFinancingAction
{
    public function handle(Financing $financing, Donation $donation): void
    {
        $donation->calculateAmountRemaining('delete');

        $financing->delete();
    }
}
