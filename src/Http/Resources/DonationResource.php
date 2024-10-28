<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DonationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getAttribute('id'),
            'employee_id' => $this->resource->getAttribute('employee_id'),
            'branch_id' => $this->resource->getAttribute('branch_id'),
            'donor_id' => $this->resource->getAttribute('donor_id'),
            'identification_number' => $this->resource->getAttribute('identification_number'),
            'transaction_date' => $this->resource->getAttribute('transaction_date'),
            'transaction_status' => $this->resource->getAttribute('transaction_status'),
            'amount' => $this->resource->getAttribute('amount'),
            'total_amount' => $this->resource->getAttribute('total_amount'),
            'branch' => new BranchResource(
                $this->whenLoaded('branch')
            ),
            'employee' => new BranchResource(
                $this->whenLoaded('employee')
            ),
            'donor' => new DonorResource(
                $this->whenLoaded('donor')
            ),
        ];
    }
}
