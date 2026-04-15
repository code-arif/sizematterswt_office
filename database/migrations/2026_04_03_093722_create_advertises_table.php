<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('advertiser')->nullable(); // e.g. 'admin', 'user'

            // Linked entity — farm, ranch, or event (nullable — ad can be standalone)
            $table->nullableMorphs('advertiseable');

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image'); // banner image
            $table->string('cta_label')->default('View Details'); // button label

            // Trigger location — where on the map to show this ad
            $table->decimal('trigger_latitude', 10, 7);
            $table->decimal('trigger_longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(1000);

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->unsignedBigInteger('impression_count')->default(0); // quick counter
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertises');
    }
};
