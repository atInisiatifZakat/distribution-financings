<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Inisiatif\Package\User\Factories\BranchFactory;
use Inisiatif\Package\User\Factories\EmployeeFactory;
use Inisiatif\Distribution\Financings\Models\Donation;
use Inisiatif\ModelShared\Database\Factories\DonorFactory;

final class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'employee_id' => EmployeeFactory::new()->create()->getKey(),
            'branch_id' => BranchFactory::new()->create()->getKey(),
            'donor_id' => DonorFactory::new()->create()->getKey(),
            'identification_number' => $this->faker->numberBetween(11111, 99999),
            'transaction_date' => Carbon::now()->toString(),
            'transaction_status' => $this->faker->randomElement(['VERIFIED', 'CANCEL', 'PENDING']),
            'amount' => $this->faker->numberBetween(111111, 9999999),
            'total_amount' => $this->faker->numberBetween(111111, 9999999),
        ];
    }
}
