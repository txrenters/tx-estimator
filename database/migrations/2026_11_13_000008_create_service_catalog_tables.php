<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->default('wrench');
            $table->string('color', 7)->default('#17664e');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_group_id')->constrained()->restrictOnDelete();
            $table->string('name')->unique();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // The original desk's trades and services.
        $catalog = [
            ['HVAC', 'wind', '#287f9d', ['HVAC repair', 'HVAC replacement']],
            ['Pest Control', 'bug', '#39835a', ['Pest plan', 'Pest treatment']],
            ['Roofing', 'house', '#9f6336', ['Roofing']],
            ['Maintenance', 'wrench', '#62579b', ['Siding', 'Make-ready', 'Cleaning', 'Plumbing', 'General maintenance']],
        ];

        $now = now();
        $sort = 0;
        foreach ($catalog as $g => [$name, $icon, $color, $services]) {
            $groupId = DB::table('service_groups')->insertGetId([
                'name' => $name, 'icon' => $icon, 'color' => $color, 'sort' => $g, 'created_at' => $now, 'updated_at' => $now,
            ]);
            foreach ($services as $service) {
                DB::table('service_types')->insert([
                    'service_group_id' => $groupId, 'name' => $service, 'sort' => $sort++, 'is_active' => true,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('service_groups');
    }
};
