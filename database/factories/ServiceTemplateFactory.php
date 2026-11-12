<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ServiceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceTemplate>
 */
class ServiceTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => fake()->words(3, true),
            'service_type' => 'HVAC repair',
            'scope' => fake()->sentence(),
            'terms' => '',
            'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 100, 'materials' => 0, 'fixed' => null]],
        ];
    }
}
