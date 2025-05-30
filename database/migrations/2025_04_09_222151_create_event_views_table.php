<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('visitor_id');
            $table->string('ip_address')->nullable();
            $table->string('referrer_url')->nullable();
            $table->string('referrer_type')->nullable();
            $table->timestamps();

            // Create a unique constraint to prevent duplicate views
            $table->unique(['event_id', 'visitor_id', 'ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_views');
    }
};