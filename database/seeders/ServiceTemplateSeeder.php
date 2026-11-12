<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ServiceTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $companies = collect(CompanySeeder::COMPANIES)
            ->map(fn (array $c) => Company::where('slug', Str::slug($c[0]))->firstOrFail());

        foreach (require __DIR__.'/data/templates.php' as $template) {
            $company = $companies[$template['company']];

            ServiceTemplate::firstOrCreate(
                ['company_id' => $company->id, 'key' => $template['key']],
                [
                    'title' => $template['title'],
                    'service_type' => $template['service'],
                    'scope' => $template['scope'],
                    'terms' => $template['terms'] ?: null,
                    'lines' => $template['lines'],
                ],
            );
        }
    }
}
