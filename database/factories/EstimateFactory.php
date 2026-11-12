<?php

namespace Database\Factories;

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estimate>
 */
class EstimateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => fn (array $attrs) => User::factory()->estimator(Company::query()->whereKey($attrs['company_id'])->firstOrFail())->create()->id,
            'title' => fake()->words(3, true),
            'status' => EstimateStatus::Draft,
            'service_type' => 'HVAC repair',
            'client' => fake()->name(),
            'address' => fake()->streetAddress(),
            'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 100, 'materials' => 0, 'fixed' => null]],
            'pricing' => config('estimating.pricing_defaults'),
            'total' => 130,
            'subtotal' => 130,
            'cost' => 100,
            'margin' => 23.0769,
        ];
    }
}
