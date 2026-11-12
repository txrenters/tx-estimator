<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->string('share_token', 40)->nullable()->unique()->after('approved_at');
            $table->timestamp('share_expires_at')->nullable()->after('share_token');
            $table->timestamp('shared_at')->nullable()->after('share_expires_at');
            $table->unsignedInteger('share_views')->default(0)->after('shared_at');
            $table->timestamp('share_viewed_at')->nullable()->after('share_views');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropUnique(['share_token']);
            $table->dropColumn(['share_token', 'share_expires_at', 'shared_at', 'share_views', 'share_viewed_at']);
        });
    }
};
