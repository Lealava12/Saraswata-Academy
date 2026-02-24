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
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('mpin');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('mpin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('mpin', 6)->after('mobile');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('mpin', 6)->after('mobile');
        });
    }
};
