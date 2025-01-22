<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use FromHome\ModelUpload\ModelUpload;
use Illuminate\Http\Resources\Json\JsonResource;
use FromHome\ModelUpload\Actions\StoreModelUploadFile;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\Http\Requests\UploadFileRequest;
use Inisiatif\Distribution\Financings\ModelUploads\ImportFinancingModelUpload;

final class UploadFinancingController
{
    public function __construct()
    {
        ModelUpload::useModelRecordImporter(ImportFinancingModelUpload::class);
    }

    public function store(UploadFileRequest $request, StoreModelUploadFile $uploadFile): JsonResource
    {
        $request->validate([
            'distribution_id' => 'required|exists:distributions,id'
        ]);

        $uploadFile->handle(
            $request->user(),
            $request->file('file'),
            Financing::class,
            $request->except('file'),
        );

        return JsonResource::make([
            'status' => 'success',
            'message' => 'Financing was imported',
        ]);
    }
}
