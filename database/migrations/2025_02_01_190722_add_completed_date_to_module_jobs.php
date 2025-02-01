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
            $table->timestamp('completed_date')->nullable()->after('job_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_jobs', function (Blueprint $table) {
            $table->dropColumn('completed_date');
        });
    }
};