<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendancesTableForMultipleSubjects extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop the old subject_id foreign key and column
            $table->dropForeign(['subject_id']); // Drop foreign key if exists
            $table->dropColumn('subject_id');
            
            // Add new subject_ids JSON column
            $table->json('subject_ids')->nullable()->after('class_id');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('subject_ids');
            $table->unsignedBigInteger('subject_id')->nullable();
            // Add foreign key back if needed
            // $table->foreign('subject_id')->references('id')->on('subjects');
        });
    }
}