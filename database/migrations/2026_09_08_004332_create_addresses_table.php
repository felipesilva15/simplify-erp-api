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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('addressable_type');
            $table->unsignedBigInteger('addressable_id');
            $table->string('type', 30);
            $table->boolean('is_primary')->default(false);
            $table->foreignId('country_id')->constrained();

            // Nacional
            $table->foreignId('state_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->string('district', 80)->nullable();
            $table->string('street', 120)->nullable();
            $table->string('number', 16)->nullable();

            // Estrangeiro
            $table->string('state_name', 80)->nullable();
            $table->string('city_name', 90)->nullable();
            $table->string('address_line_1', 160)->nullable();
            $table->string('address_line_2', 160)->nullable();

            $table->string('postal_code', 20)->nullable();
            $table->string('complement', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['addressable_type', 'addressable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
