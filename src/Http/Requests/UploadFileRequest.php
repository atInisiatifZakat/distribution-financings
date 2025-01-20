<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UploadFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file',
        ];
    }
}
