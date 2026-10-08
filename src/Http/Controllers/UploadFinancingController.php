<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Controllers;

use Illuminate\Http\JsonResponse;
use FromHome\ModelUpload\ModelUpload;
use Illuminate\Validation\ValidationException;
use FromHome\ModelUpload\Models\ModelUploadFile;
use Illuminate\Http\Resources\Json\JsonResource;
use FromHome\ModelUpload\Actions\StoreModelUploadFile;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\Support\CsvDelimiter;
use Inisiatif\Distribution\Financings\Http\Requests\UploadFileRequest;
use Inisiatif\Distribution\Financings\Actions\ValidateUploadFinancingAction;
use Inisiatif\Distribution\Financings\ModelUploads\ImportFinancingModelUpload;
use Inisiatif\Distribution\Financings\Actions\ReplacePreviousFinancingUploadAction;

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

            $uploaded = $uploadFile->handle(
                $request->user(),
                $request->file('file'),
                Financing::class,
                array_merge($request->except('file'), [
                    'branch_id' => $branchId,
                    'is_head_office' => $isHeadOffice,
                    'csv_delimiter' => CsvDelimiter::detect((string) $request->file('file')->getRealPath()),
                ]),
            );

            $failureMessage = $this->failureMessage($uploaded);

            if ($failureMessage !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => $failureMessage,
                ], 422);
            }

            return JsonResource::make([
                'status' => 'success',
                'message' => 'Financing was imported',
            ])->response();
        } catch (ValidationException $exception) {
            $message = $exception->validator->errors()->first('distribution_amount')
                ?: ($exception->validator->errors()->first() ?: $exception->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $exception->errors(),
            ], 422);
        }
    }

    private function failureMessage(ModelUploadFile $uploaded): ?string
    {
        $recordCount = $uploaded->records()->count();

        if ($recordCount === 0) {
            return null;
        }

        $savedCount = $uploaded->records()->whereNotNull('model_id')->count();

        if ($savedCount > 0) {
            return null;
        }

        $errors = $uploaded->records()
            ->whereNotNull('error_message')
            ->pluck('error_message')
            ->filter()
            ->unique()
            ->values();

        foreach ($errors as $error) {
            if (\str_contains((string) $error, 'Amount must be the same as distribution amount')
                || \str_contains((string) $error, 'exceeds donation amount')) {
                return 'Nominal yang diupload melebihi nominal donasi';
            }
        }

        $message = (string) ($errors->first() ?: 'Data donasi gagal diupload');

        return \str_starts_with($message, 'Cannot process record : ')
            ? \substr($message, \strlen('Cannot process record : '))
            : $message;
    }
}
