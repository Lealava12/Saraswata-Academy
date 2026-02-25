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
        DB::table('student_materials')
            ->where('status', 'Returned')
            ->update(['status' => 'Not Issued']);

        // 2) Change enum to Issued / Not Issued
        DB::statement("
            ALTER TABLE student_materials
            MODIFY status ENUM('Issued','Not Issued') NOT NULL DEFAULT 'Not Issued'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement("
            ALTER TABLE student_materials
            MODIFY status ENUM('Issued','Returned') NOT NULL DEFAULT 'Issued'
        ");

        // Convert "Not Issued" back to "Returned" (rollback mapping)
        DB::table('student_materials')
            ->where('status', 'Not Issued')
            ->update(['status' => 'Returned']);
    }
};
