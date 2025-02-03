<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // First make column nullable to handle existing data
            $table->string('google_access_token')->nullable()->change();

            // Then change to text
            $table->text('google_access_token')->nullable()->change();
            $table->text('google_refresh_token')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_access_token', 255)->nullable()->change();
            $table->string('google_refresh_token', 255)->nullable()->change();
        });
    }
};