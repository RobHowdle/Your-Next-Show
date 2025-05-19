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
        Schema::table('venues', function (Blueprint $table) {
            $table->json('packages')->nullable()->after('description');
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->json('packages')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('packages');
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->dropColumn('packages');
        });
    }
};
