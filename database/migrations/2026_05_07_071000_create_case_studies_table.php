<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assistance_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('researcher_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('draft')->index();
            $table->date('started_at')->nullable();
            $table->timestamp('ready_for_supervisor_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('summary')->nullable();
            $table->text('family_situation')->nullable();
            $table->text('risk_factors')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('supervisor_notes')->nullable();
            $table->timestamps();

            $table->index(['social_case_id', 'status']);
            $table->index(['researcher_id', 'status']);
            $table->index(['supervisor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
