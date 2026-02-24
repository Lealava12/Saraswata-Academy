<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 12)->unique(); // SA250001
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('board_id')->constrained('boards');
            $table->foreignId('class_id')->constrained('classes');
            $table->string('roll_no', 20)->nullable();
            $table->string('mobile', 15);
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->date('joining_date');
            $table->string('school_name')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
