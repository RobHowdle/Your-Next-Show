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
        Schema::table('venues', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
        });

        Schema::table('other_services', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at']);
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at']);
        });

        Schema::table('other_services', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at']);
        });
    }
};