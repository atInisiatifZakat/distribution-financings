<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class FinancingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getAttribute('id'),
            'donation_id' => $this->resource->getAttribute('donation_id'),
            'donation_number' => $this->resource->getAttribute('donation_number'),
            'distribution_id' => $this->resource->getAttribute('distribution_id'),
            'distribution_name' => $this->resource->getAttribute('distribution_name'),
            'distribution_at' => $this->resource->getAttribute('distribution_at'),
            'distribution_program_id' => $this->resource->getAttribute('distribution_program_id'),
            'distribution_program_name' => $this->resource->getAttribute('distribution_program_name'),
            'distribution_sector_id' => $this->resource->getAttribute('distribution_sector_id'),
            'distribution_sector_name' => $this->resource->getAttribute('distribution_sector_name'),
            'amount' => $this->resource->getAttribute('amount'),
            'donation' => new DonationResource(
                $this->whenLoaded('donation')
            ),
        ];
    }
}
