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
            $table->string('preferred_contact')->nullable();
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->string('preferred_contact')->nullable();
        });

        Schema::table('other_services', function (Blueprint $table) {
            $table->string('preferred_contact')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('preferred_contact');
        });
        Schema::table('promoters', function (Blueprint $table) {
            $table->dropColumn('preferred_contact');
        });
        Schema::table('other_services', function (Blueprint $table) {
            $table->dropColumn('preferred_contact');
        });
    }
};
