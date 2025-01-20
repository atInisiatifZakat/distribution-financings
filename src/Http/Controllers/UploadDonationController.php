<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use FromHome\ModelUpload\Actions\StoreModelUploadFile;
use FromHome\ModelUpload\ModelUpload;
use Illuminate\Http\Resources\Json\JsonResource;
use Inisiatif\Distribution\Financings\Http\Requests\UploadFileRequest;
use Inisiatif\Distribution\Financings\Jobs\ImportDonationJob;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\Distribution\Financings\ModelUpload\ImportDonationModelUpload;

final class UploadDonationController
{
    public function __construct()
    {
        ModelUpload::useModelRecordImporter(ImportDonationModelUpload::class);
    }

    public function store(UploadFileRequest $request, StoreModelUploadFile $uploadFile): JsonResource
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $uploadFile->handle(
            $request->user(),
            $request->file('file'),
            Donation::class,
            $request->except('file'),
        );

        ImportDonationJob::dispatch(
            $file->getClientOriginalName(),
        );

        return JsonResource::make([
            'status' => 'success',
            'message' => 'Donation was imported',
        ]);
    }
}
