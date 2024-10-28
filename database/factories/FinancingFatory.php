<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Inisiatif\Distribution\Financings\Models\Financing;

final class FinancingFactory extends Factory
{
    protected $model = Financing::class;

    public function definition(): array
    {
        return [
            'donation_id' => $this->faker->uuid(),
            'donation_number' => $this->faker->numberBetween(11111, 99999),
            'distribution_id' => $this->faker->uuid(),
            'distribution_name' => $this->faker->sentence(1),
            'distribution_at' => Carbon::now()->toString(),
            'distribution_program_id' => $this->faker->uuid(),
            'distribution_program_name' => $this->faker->sentence(1),
            'distribution_sector_id' => $this->faker->numberBetween(1, 999),
            'distribution_sector_name' => $this->faker->sentence(1),
            'amount' => 0,
        ];
    }
}
