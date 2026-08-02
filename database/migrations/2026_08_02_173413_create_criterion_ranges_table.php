<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('criterion_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_criterion_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('label');
            $table->string('color', 20)->nullable();
            $table->unique(['achievement_criterion_id', 'min_score', 'max_score'], 'criterion_range_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterion_ranges');
    }
};
