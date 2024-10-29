<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\DataTransfers;

use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

final class CreateFinancingData extends DataTransferObject
{
    #[MapTo('amount')]
    #[MapFrom('amount')]
    public int|float $amount;

    #[MapTo('donation_id')]
    #[MapFrom('donation_id')]
    public string $donationId;

    #[MapTo('donation_number')]
    #[MapFrom('donation_number')]
    public string $donationName;

    #[MapTo('distribution_id')]
    #[MapFrom('distribution_id')]
    public string $distributionId;

    #[MapTo('distribution_name')]
    #[MapFrom('distribution_name')]
    public string $distributionName;

    #[MapTo('distribution_at')]
    #[MapFrom('distribution_at')]
    public ?string $distributionAt = null;

    #[MapTo('distribution_program_id')]
    #[MapFrom('distribution_program_id')]
    public string $distributionProgramId;

    #[MapTo('distribution_program_name')]
    #[MapFrom('distribution_program_name')]
    public string $distributionProgramName;

    #[MapTo('distribution_sector_id')]
    #[MapFrom('distribution_sector_id')]
    public string $distributionSectorId;

    #[MapTo('distribution_sector_name')]
    #[MapFrom('distribution_sector_name')]
    public string $distributionSectorName;
}
