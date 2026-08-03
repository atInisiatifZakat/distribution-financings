<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Inisiatif\Distribution\Financings\Models\DonationProgram;
use Inisiatif\Distribution\Models\Distribution\Distribution;
use Inisiatif\ModelShared\Models\FundingType;

final class CreateFinancingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'donation_id' => 'required',
            'distribution_id' => [
                'required',
                Rule::exists(Distribution::class, 'id'),
            ],
            'amount' => 'required',
            'funding_id' => [
                'required',
                Rule::exists(FundingType::class, 'id'),
            ],
            'program_id' => [
                'required',
                Rule::exists(DonationProgram::class, 'id'),
            ],
        ];
    }
}
