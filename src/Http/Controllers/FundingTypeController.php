<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inisiatif\Distribution\Financings\Repositories\FundingTypeRepository;

final class FundingTypeController
{
    public function index(Request $request, FundingTypeRepository $repository): JsonResource
    {
        return JsonResource::collection($repository->fetchAll($request));
    }
}
