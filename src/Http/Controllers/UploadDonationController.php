<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;
use Inisiatif\Distribution\Financings\Http\Requests\UploadFileRequest;
use Inisiatif\Distribution\Financings\Jobs\ImportDonationJob;

final class UploadDonationController
{
    public function store(UploadFileRequest $request): JsonResource
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $file->storeAs(null, $file->getClientOriginalName(), 'temps');

        ImportDonationJob::dispatch(
            $file->getClientOriginalName(),
        );

        return JsonResource::make([
            'status' => 'success',
            'message' => 'Donation was imported',
        ]);
    }
}
