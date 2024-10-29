<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class DonorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getAttribute('id'),
            'name' => $this->resource->getAttribute('name'),
            'identification_number' => $this->resource->getAttribute('identification_number'),
        ];
    }
}
