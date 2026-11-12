<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            // The property owner's typed-name authorization from the share link.
            $table->string('authorized_name')->nullable()->after('share_viewed_at');
            $table->timestamp('authorized_at')->nullable()->after('authorized_name');
            $table->string('authorized_ip', 45)->nullable()->after('authorized_at');
        });

        Schema::table('estimate_activities', function (Blueprint $table) {
            // Customer actions have no user account; their typed name is kept instead.
            $table->foreignId('user_id')->nullable()->change();
            $table->string('actor_name')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('estimate_activities', function (Blueprint $table) {
            $table->dropColumn('actor_name');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn(['authorized_name', 'authorized_at', 'authorized_ip']);
        });
    }
};
