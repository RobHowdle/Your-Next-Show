<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('promoters')->whereNotNull('genre')->get()->each(function ($promoter) {
            $genres = json_decode($promoter->genre, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $genres = explode(',', $promoter->genre);
                DB::table('promoters')->where('id', $promoter->id)->update([
                    'genre' => json_encode($genres),
                ]);
            }
        });

        Schema::table('promoters', function (Blueprint $table) {
            $table->json('genre')->nullable()->change();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promoters', function (Blueprint $table) {
            $table->longText('genre')->nullable()->change();
        });
    }
};