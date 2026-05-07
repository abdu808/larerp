<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_study_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('income_score')->default(0);
            $table->unsignedTinyInteger('vulnerability_score')->default(0);
            $table->unsignedTinyInteger('housing_score')->default(0);
            $table->unsignedTinyInteger('health_score')->default(0);
            $table->unsignedTinyInteger('education_score')->default(0);
            $table->unsignedTinyInteger('debts_score')->default(0);
            $table->unsignedTinyInteger('support_sources_score')->default(0);
            $table->unsignedTinyInteger('total_score')->default(0)->index();
            $table->string('level')->default('low')->index();
            $table->text('notes')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();

            $table->unique('case_study_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs_assessments');
    }
};
