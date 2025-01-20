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
            $table->enum('deposit_required', ['yes', 'no'])->nullable()->after('in_house_gear');
            $table->decimal('deposit_amount')->nullable()->after('deposit_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('deposit_required');
            $table->dropColumn('deposit_amount');
        });
    }
};