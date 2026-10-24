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
        Schema::table('measurement_units', function (Blueprint $table) {
            $table->dropUnique('measurement_units_code_unique');
            $table->unique(['code', 'deleted_at'], 'measurement_units_code_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurement_units', function (Blueprint $table) {
            $table->dropUnique('measurement_units_code_deleted_at_unique');
            $table->unique('code', 'measurement_units_code_unique');
        });
    }
};