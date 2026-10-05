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
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 60);
            $table->string('symbol', 10);
            $table->string('category', 20);
            $table->foreignId('base_unit_id')->nullable()->constrained('measurement_units')->restrictOnDelete();
            $table->decimal('conversion_factor', 18, 8)->default(1);
            $table->unsignedTinyInteger('decimal_places')->default(0);
            $table->string('external_code', 10)->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_units');
    }
};
