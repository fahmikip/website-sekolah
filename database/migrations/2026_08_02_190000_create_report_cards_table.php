<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft')->index();
            $table->text('homeroom_note')->nullable();
            $table->string('promotion_decision')->nullable();
            $table->unsignedSmallInteger('sick_days')->default(0);
            $table->unsignedSmallInteger('excused_days')->default(0);
            $table->unsignedSmallInteger('unexcused_days')->default(0);
            $table->string('verification_token', 64)->unique();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->unsignedInteger('print_count')->default(0);
            $table->timestamp('last_printed_at')->nullable();
            $table->unique(['student_id', 'academic_year_id', 'semester_id']);
            $table->timestamps();
        });

        Schema::create('report_card_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->decimal('final_score', 5, 2);
            $table->string('predicate', 5)->nullable();
            $table->text('description')->nullable();
            $table->boolean('description_approved')->default(false);
            $table->unique(['report_card_id', 'subject_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_scores');
        Schema::dropIfExists('report_cards');
    }
};
