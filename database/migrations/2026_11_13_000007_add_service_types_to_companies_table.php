<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Services this company offers; null means every service type.
            $table->json('service_types')->nullable()->after('pricing_defaults');
        });

        // Give the four original companies their trade.
        $trades = [
            'crystal-creek-air' => ['HVAC repair', 'HVAC replacement'],
            'crystal-creek-pest-control' => ['Pest plan', 'Pest treatment'],
            'crystal-creek-roofing' => ['Roofing'],
            'texas-home-maintenance-pros' => ['Siding', 'Make-ready', 'Cleaning', 'Plumbing', 'General maintenance'],
        ];

        foreach ($trades as $slug => $services) {
            DB::table('companies')->where('slug', $slug)->update(['service_types' => json_encode($services)]);
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('service_types');
        });
    }
};
