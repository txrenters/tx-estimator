<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Support\ServiceCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /** @var list<array{0:string,1:string,2:string,3:string,4:string}> name, short, color, tone, catalog group (trade) */
    public const COMPANIES = [
        ['Crystal Creek Air', 'Air', '#287f9d', '#e9f4f8', 'HVAC'],
        ['Crystal Creek Pest Control', 'Pest Control', '#39835a', '#eaf4ed', 'Pest Control'],
        ['Crystal Creek Roofing', 'Roofing', '#9f6336', '#fbf0e4', 'Roofing'],
        ['Texas Home Maintenance Pros', 'Maintenance', '#62579b', '#efedf8', 'Maintenance'],
    ];

    public function run(): void
    {
        foreach (self::COMPANIES as [$name, $short, $color, $tone, $trade]) {
            Company::firstOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'short_name' => $short,
                'location' => 'Greater Houston, TX',
                'currency' => 'USD',
                'color' => $color,
                'tone' => $tone,
                'pricing_defaults' => config('estimating.pricing_defaults'),
                'service_types' => app(ServiceCatalog::class)->groups()[$trade] ?? null,
            ]);
        }
    }
}
