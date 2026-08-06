<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateFinancingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'donation_id' => ['required', 'uuid'],
            'donor_id' => [
                'required',
                'uuid',
            ],
            'distribution_id' => [
                'required',
                'uuid',
            ],
            'amount' => 'required',
            'donation_detail_funding_type_id' => [
                'nullable',
                'integer',
            ],
            'donation_detail_program_id' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
