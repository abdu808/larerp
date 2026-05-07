<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assistance_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('decision_type')->index();
            $table->string('status')->default('draft')->index();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->string('approved_service_type')->nullable()->index();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('decided_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['social_case_id', 'status']);
        });

        Schema::create('support_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->string('plan_type')->default('relief')->index();
            $table->text('goal');
            $table->string('status')->default('draft')->index();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('success_criteria')->nullable();
            $table->timestamps();

            $table->index(['social_case_id', 'status']);
        });

        Schema::create('service_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('support_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('committee_decision_id')->nullable()->constrained()->nullOnDelete();
            $table->string('delivery_type')->index();
            $table->string('status')->default('scheduled')->index();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('service_description');
            $table->date('delivered_at')->nullable();
            $table->foreignId('delivered_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['social_case_id', 'status']);
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('support_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_delivery_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('scheduled')->index();
            $table->date('followed_up_at')->nullable();
            $table->text('outcome');
            $table->string('improvement_level')->nullable()->index();
            $table->string('follow_up_decision')->index();
            $table->date('next_follow_up_at')->nullable();
            $table->foreignId('followed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['social_case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('service_deliveries');
        Schema::dropIfExists('support_plans');
        Schema::dropIfExists('committee_decisions');
    }
};
