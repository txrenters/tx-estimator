<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name');
            $table->string('location')->default('Greater Houston, TX');
            $table->string('currency', 3)->default('USD');
            $table->string('color', 7)->default('#17664e');
            $table->string('tone', 7)->default('#eaf4ee');
            $table->string('logo_path')->nullable();
            $table->text('default_scope')->nullable();
            $table->text('default_terms')->nullable();
            $table->json('pricing_defaults');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
