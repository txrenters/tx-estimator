<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('key')->nullable();
            $table->string('title');
            $table->string('service_type');
            $table->text('scope')->nullable();
            $table->text('terms')->nullable();
            $table->json('lines');
            $table->timestamps();

            $table->index(['company_id', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_templates');
    }
};
