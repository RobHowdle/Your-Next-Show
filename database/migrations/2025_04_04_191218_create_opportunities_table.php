<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            // Polymorphic relationship for the creator (venue, promoter, etc)
            $table->morphs('serviceable');

            // Polymorphic relationship for what the opportunity relates to (event, etc)
            $table->nullableMorphs('related');

            // Basic fields
            $table->string('title');
            $table->string('additional_info')->nullable();
            $table->string('type'); // e.g., 'bands_wanted', 'job_opportunity', etc
            $table->string('position_type'); // headliner, support, opener, etc
            $table->string('status')->default('active');

            // Media
            $table->string('poster_url')->nullable();
            $table->boolean('use_related_poster')->default(false);

            // Dates
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('set_length')->nullable();
            $table->dateTime('application_deadline')->nullable();

            // Common metadata stored as JSON
            $table->json('genres')->nullable();
            $table->json('excluded_entities')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Create a table for applications/responses to opportunities
        Schema::create('opportunity_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->onDelete('cascade');
            $table->morphs('applicant');
            $table->string('status')->default('pending');
            $table->json('application_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_applications');
        Schema::dropIfExists('opportunity_details');
        Schema::dropIfExists('opportunities');
    }
};