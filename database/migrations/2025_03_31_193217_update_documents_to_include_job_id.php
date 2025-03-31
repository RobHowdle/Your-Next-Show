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
        // Add job_id to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->nullable()->after('serviceable_id');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
        });

        // Modify module_jobs table
        Schema::table('module_jobs', function (Blueprint $table) {
            $table->dropColumn('scope_url');
            $table->unsignedBigInteger('document_id')->after('id')->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');
        });

        // Drop jobs_documents table
        Schema::dropIfExists('jobs_documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate jobs_documents table
        Schema::create('jobs_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->string('file_path');
            $table->timestamps();
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });

        // Revert module_documents changes
        Schema::table('module_documents', function (Blueprint $table) {
            $table->string('scope_url')->nullable();
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
        });

        // Remove job_id from documents
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
    }
};