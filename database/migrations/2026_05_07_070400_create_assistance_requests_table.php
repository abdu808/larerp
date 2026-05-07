<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('social_case_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_number')->unique();
            $table->string('request_type')->index();
            $table->string('status')->default('draft')->index();
            $table->string('urgency')->default('normal')->index();
            $table->string('source')->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->text('description');
            $table->boolean('consent_to_store_data')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'status']);
            $table->index(['beneficiary_id', 'status']);
            $table->index(['social_case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_requests');
    }
};
