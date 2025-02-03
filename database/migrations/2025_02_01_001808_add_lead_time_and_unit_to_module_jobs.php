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
        Schema::table('module_jobs', function (Blueprint $table) {
            $table->integer('lead_time')->nullable();
            $table->enum('lead_time_unit', ['hours', 'days', 'weeks', 'months'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('module_jobs', function (Blueprint $table) {
            $table->dropColumn('lead_time');
            $table->dropColumn('lead_time_unit');
        });
    }
};