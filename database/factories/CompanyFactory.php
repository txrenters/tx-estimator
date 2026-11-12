<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'short_name' => Str::limit($name, 12, ''),
            'location' => 'Greater Houston, TX',
            'currency' => 'USD',
            'color' => '#287f9d',
            'tone' => '#e9f4f8',
            'pricing_defaults' => config('estimating.pricing_defaults'),
        ];
    }
}
