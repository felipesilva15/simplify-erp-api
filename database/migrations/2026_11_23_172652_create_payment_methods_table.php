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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('condition', 80);
            $table->string('applicable_type', 30);
            $table->string('payment_type', 2);
            $table->string('tax_type', 30)->nullable();
            $table->decimal('tax_aliquot', 7, 4)->nullable();
            $table->decimal('tax_fixed_value', 15, 2)->nullable();
            $table->decimal('penalty_aliquot', 7, 4)->nullable();
            $table->decimal('interest_aliquot', 7, 4)->nullable();
            $table->integer('payment_deadline');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
