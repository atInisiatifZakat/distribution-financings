<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inisiatif\Distribution\Financings\Repositories\DonationProgramRepository;

final class DonationProgramController
{
    public function index(Request $request, DonationProgramRepository $repository): JsonResource
    {
        return JsonResource::collection($repository->fetchAll($request));
    }
}
