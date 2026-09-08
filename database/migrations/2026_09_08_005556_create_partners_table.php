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
            $table->char('partner_type_code', 20);
            $table->string('name', 120);
            $table->string('trade_name', 150);
            $table->char('person_type', 7);
            $table->char('taxpayter_type', 15);
            $table->char('document_number', 14);
            $table->string('identity_number', 15);
            $table->string('identity_issuer', 20);
            $table->date('customer_since');
            $table->string('state_registration', 80);
            $table->string('municipal_registration', 30);
            $table->string('suframa_registration', 30);
            $table->char('marital_status', 10);
            $table->string('profession', 80);
            $table->char('gender', 6);
            $table->date('birth_date');
            $table->string('nationality', 40);
            $table->string('father_name', 120);
            $table->string('father_document', 15);
            $table->string('mother_name', 120);
            $table->string('mother_document', 15);
            $table->char('pix_type', 15);
            $table->text('pix_key');
            $table->text('notes');
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
