<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('social_case_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assistance_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('document_type')->index();
            $table->string('sensitivity_level')->default('internal')->index();
            $table->string('verification_status')->default('uploaded')->index();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('used_in_decision_at')->nullable()->index();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'document_type']);
            $table->index(['beneficiary_id', 'document_type']);
            $table->index(['assistance_request_id', 'verification_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_documents');
    }
};
