<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_study_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('scheduled')->index();
            $table->string('type')->default('field')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->string('location')->nullable();
            $table->text('purpose')->nullable();
            $table->text('findings')->nullable();
            $table->text('next_action')->nullable();
            $table->timestamps();

            $table->index(['case_study_id', 'status']);
            $table->index(['social_case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visits');
    }
};
