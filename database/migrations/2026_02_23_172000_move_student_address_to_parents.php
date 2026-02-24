<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add address to student_parents
        Schema::table('student_parents', function (Blueprint $table) {
            $table->text('address')->nullable()->after('mother_mobile');
        });

        // Remove address from students
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add address back to students
        Schema::table('students', function (Blueprint $table) {
            $table->text('address')->nullable()->after('school_name');
        });

        // Remove address from student_parents
        Schema::table('student_parents', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
