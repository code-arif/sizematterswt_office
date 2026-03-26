<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('owner_name')->after('admin_id')->nullable();
            $table->string('owner_address')->after('owner_name')->nullable();
            $table->string('owner_phone')->after('owner_address')->nullable();
            $table->string('owner_avatar')->after('owner_phone')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_address', 'owner_phone', 'owner_avatar']);
        });
    }
};
