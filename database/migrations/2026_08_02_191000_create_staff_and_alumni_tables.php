<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('employee_number', 30)->nullable()->unique();
            $table->string('name')->index();
            $table->string('gender', 1);
            $table->string('position')->index();
            $table->string('employment_status')->nullable();
            $table->string('education')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable()->index();
            $table->string('photo_path')->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nis', 30)->nullable()->index();
            $table->string('name')->index();
            $table->year('graduation_year')->index();
            $table->string('further_education')->nullable();
            $table->string('occupation')->nullable();
            $table->text('achievement')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('publication_consent')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('staff');
    }
};
