<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_objective_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['learning_objective_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_scopes');
    }
};
