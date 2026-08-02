<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', fn (Blueprint $table) => $table->string('document_path')->nullable()->after('photo_path'));
        Schema::table('students', fn (Blueprint $table) => $table->string('document_path')->nullable()->after('photo_path'));
    }

    public function down(): void
    {
        Schema::table('teachers', fn (Blueprint $table) => $table->dropColumn('document_path'));
        Schema::table('students', fn (Blueprint $table) => $table->dropColumn('document_path'));
    }
};
