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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('partner_type_code', 20);
            $table->string('name', 120);
            $table->string('trade_name', 150);
            $table->string('person_type', 30);
            $table->string('taxpayer_type', 30);
            $table->string('document_number', 20);
            $table->string('identity_number', 15)->nullable();
            $table->string('identity_issuer', 20)->nullable();
            $table->date('partner_since')->nullable();
            $table->string('state_registration', 14)->nullable();
            $table->string('municipal_registration', 15)->nullable();
            $table->string('suframa_registration', 9)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->char('cbo', 6)->nullable();
            $table->string('gender', 12)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('father_name', 120)->nullable();
            $table->string('father_document', 15)->nullable();
            $table->string('mother_name', 120)->nullable();
            $table->string('mother_document', 15)->nullable();
            $table->string('pix_type', 30)->nullable();
            $table->text('pix_key')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_number']);
            $table->index(['partner_type_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
