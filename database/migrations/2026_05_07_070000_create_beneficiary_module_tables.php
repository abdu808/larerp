<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_owner_id')->nullable()->constrained('beneficiaries')->nullOnDelete();
            $table->string('national_id')->nullable()->unique();
            $table->string('first_name');
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('gender')->nullable()->index();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('relationship_to_guardian')->nullable()->index();
            $table->string('marital_status')->nullable()->index();
            $table->string('education_level')->nullable();
            $table->string('employment_status')->nullable()->index();
            $table->string('health_status')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->string('status')->default('active')->index();
            $table->string('city')->nullable()->index();
            $table->string('district')->nullable()->index();
            $table->text('address')->nullable();
            $table->date('registered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('social_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('case_number')->unique();
            $table->string('type')->nullable()->index();
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('normal')->index();
            $table->date('opened_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->text('summary');
            $table->text('needs')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->decimal('monthly_expenses', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['beneficiary_id', 'status']);
        });

        Schema::create('social_case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('general')->index();
            $table->text('note');
            $table->timestamp('noted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('social_case_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_case_attachments');
        Schema::dropIfExists('social_case_notes');
        Schema::dropIfExists('social_cases');
        Schema::dropIfExists('beneficiaries');
    }
};
