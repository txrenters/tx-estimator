<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([CompanySeeder::class, ServiceTemplateSeeder::class]);

        if ($email = config('estimating.superadmin.email')) {
            User::firstOrCreate(['email' => $email], [
                'name' => 'Superadmin',
                'password' => config('estimating.superadmin.password') ?: str()->random(32),
                'role' => Role::Superadmin,
                'email_verified_at' => now(),
            ]);
        }

        if (app()->environment('local')) {
            $air = Company::where('slug', 'crystal-creek-air')->firstOrFail();

            User::firstOrCreate(['email' => 'admin@example.com'], [
                'name' => 'Demo Admin', 'password' => 'password', 'role' => Role::Superadmin, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'manager@example.com'], [
                'name' => 'Demo Manager', 'password' => 'password', 'role' => Role::Manager, 'company_id' => $air->id, 'email_verified_at' => now(),
            ]);
            User::firstOrCreate(['email' => 'estimator@example.com'], [
                'name' => 'Demo Estimator', 'password' => 'password', 'role' => Role::Estimator, 'company_id' => $air->id, 'email_verified_at' => now(),
            ]);
        }
    }
}
