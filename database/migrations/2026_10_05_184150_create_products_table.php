<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('sku', 40);
            $table->string('gtin', 14)->nullable();
            $table->string('name', 255);
            $table->string('description', 1024)->nullable();
            $table->text('notes')->nullable();

            $table->decimal('price', 18, 4)->default(0);
            $table->decimal('cost', 18, 4)->default(0);

            $table->decimal('net_weight', 12, 3)->nullable();
            $table->decimal('gross_weight', 12, 3)->nullable();
            $table->decimal('width', 12, 4)->nullable();
            $table->decimal('height', 12, 4)->nullable();
            $table->decimal('length', 12, 4)->nullable();
            $table->decimal('minimum_stock', 18, 4)->nullable();
            $table->decimal('maximum_stock', 18, 4)->nullable();

            $table->boolean('is_active')->default(true);
            $table->foreignId('measurement_unit_id')->constrained('measurement_units')->restrictOnDelete();
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->restrictOnDelete();
            $table->foreignId('product_brand_id')->nullable()->constrained('product_brands')->restrictOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX products_sku_unique ON products (lower(sku)) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX products_gtin_unique ON products (gtin) WHERE deleted_at IS NULL AND gtin IS NOT NULL');
        } else {
            DB::statement('ALTER TABLE products ADD COLUMN sku_active VARCHAR(40) GENERATED ALWAYS AS (IF(deleted_at IS NULL, sku, NULL)) VIRTUAL');
            DB::statement('ALTER TABLE products ADD COLUMN gtin_active VARCHAR(14) GENERATED ALWAYS AS (IF(deleted_at IS NULL, gtin, NULL)) VIRTUAL');
            DB::statement('CREATE UNIQUE INDEX products_sku_unique ON products (sku_active)');
            DB::statement('CREATE UNIQUE INDEX products_gtin_unique ON products (gtin_active)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
