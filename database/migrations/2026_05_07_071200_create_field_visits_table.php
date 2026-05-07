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
            $table->foreignId('beneficiary_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('case_study_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('social_case_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('scheduled')->index();
            $table->string('type')->default('field')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->string('location')->nullable();
            $table->string('building_status')->nullable();
            $table->string('furniture_status')->nullable();
            $table->boolean('is_urgent')->default(false)->index();
            $table->text('purpose')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('next_action')->nullable();
            $table->timestamps();

            $table->index(['beneficiary_id', 'status']);
            $table->index(['case_study_id', 'status']);
            $table->index(['social_case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visits');
    }
};
