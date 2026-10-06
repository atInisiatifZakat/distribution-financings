<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use FromHome\ModelUpload\ModelUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use FromHome\ModelUpload\Actions\StoreModelUploadFile;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\Http\Requests\UploadFileRequest;
use Inisiatif\Distribution\Financings\Actions\ReplacePreviousFinancingUploadAction;
use Inisiatif\Distribution\Financings\Actions\ValidateUploadFinancingAction;
use Inisiatif\Distribution\Financings\ModelUploads\ImportFinancingModelUpload;

final class UploadFinancingController
{
    public function __construct()
    {
        ModelUpload::useModelRecordImporter(ImportFinancingModelUpload::class);
    }

    public function store(
        UploadFileRequest $request,
        StoreModelUploadFile $uploadFile,
        ValidateUploadFinancingAction $validate,
        ReplacePreviousFinancingUploadAction $replacePreviousUpload,
    ): JsonResponse {
        try {
            $loginable = $request->user()->getLoginable();

            $branch = $loginable?->getAttribute('branch');

            $isHeadOffice = $branch?->getAttribute('is_head_office') === true;

            $branchId = $loginable?->getAttribute('branch_id');

            $validate->handle(
                $request->file('file'),
                $isHeadOffice,
                $branchId,
                $request->input('distribution_id'),
            );

            $replacePreviousUpload->handle((string) $request->input('distribution_id'));

            $uploadFile->handle(
                $request->user(),
                $request->file('file'),
                Financing::class,
                array_merge($request->except('file'), [
                    'branch_id' => $branchId,
                    'is_head_office' => $isHeadOffice,
                ]),
            );

            return JsonResource::make([
                'status' => 'success',
                'message' => 'Financing was imported',
            ])->response();
        } catch (ValidationException $exception) {
            $message = $exception->validator->errors()->first() ?: $exception->getMessage();

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $exception->errors(),
            ], 422);
        }
    }
}
