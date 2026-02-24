<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_material_id')->constrained('study_materials')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->date('issue_date');
            $table->enum('status', ['Issued', 'Returned'])->default('Issued');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_materials');
    }
};
