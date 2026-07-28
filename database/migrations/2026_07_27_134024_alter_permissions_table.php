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
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_id');
            $table->dropColumn(['resource']);
            $table->foreignId('resource_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('label', 80)->nullable();
        });

        DB::table('permissions')->update([
            'label' => ''
        ]);

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('label', 80)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('resource', 60)->nullable();
            $table->dropConstrainedForeignId('resource_id');
            $table->dropColumn(['label']);
        });

         DB::table('permissions')->update([
            'resource' => ''
        ]);

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('resource', 60)->nullable(false)->change();
        });
    }
};
