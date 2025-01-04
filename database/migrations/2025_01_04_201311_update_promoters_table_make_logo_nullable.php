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
        Schema::table('promoters', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->change();
            $table->longtext('my_venues')->nullable()->change();
            $table->json('genre')->nullable()->change();
            $table->json('band_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('promoters', function (Blueprint $table) {
            $table->string('logo_url')->nullable(false)->change();
            $table->longtext('my_venues')->nullable(false)->change();
            $table->json('genre')->nullable(false)->change();
            $table->json('band_type')->nullable(false)->change();
        });
    }
};