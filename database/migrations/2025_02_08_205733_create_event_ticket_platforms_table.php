<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_ticket_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('platform_name'); // eventbrite, ticketmaster, etc
            $table->string('platform_event_id');
            $table->string('platform_event_url')->nullable();
            $table->json('platform_event_data')->nullable(); // Store additional data if needed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ticket_platforms');
    }
};
