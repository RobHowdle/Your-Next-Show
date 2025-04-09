<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('minor_profile_views', function (Blueprint $table) {
            $table->id();
            $table->morphs('serviceable');
            // Add profile type column
            $table->string('profile_type'); // 'service', 'venue', or 'promoter'
            $table->string('ip_address');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('user_agent')->nullable();
            $table->string('referrer_url')->nullable();
            $table->json('geo_location')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index('profile_type');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minor_profile_views');
    }
};