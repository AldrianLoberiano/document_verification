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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('verification_code', 64)->unique();
            $table->string('title');
            $table->string('document_type');
            $table->string('document_number')->nullable()->index();
            $table->string('recipient_name')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->string('file_original_name');
            $table->string('file_mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('file_path');
            $table->string('file_checksum', 64);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
