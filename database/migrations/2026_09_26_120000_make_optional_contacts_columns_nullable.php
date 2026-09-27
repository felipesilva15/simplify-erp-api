<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Os contatos passaram a ser gravados junto do parceiro, e a API os trata
     * como opcionais: StorePartnerRequest/UpdatePartnerRequest marcam esses
     * campos como nullable e a regra de negócio exige apenas um meio de contato
     * (e-mail, celular ou telefone). As colunas foram criadas como NOT NULL,
     * o que impedia gravar um contato que informa, por exemplo, só o e-mail.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('department', 80)->nullable()->change();
            $table->string('email', 180)->nullable()->change();
            $table->string('mobile', 11)->nullable()->change();
            $table->string('phone', 10)->nullable()->change();
            $table->text('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('department', 80)->nullable(false)->change();
            $table->string('email', 180)->nullable(false)->change();
            $table->string('mobile', 11)->nullable(false)->change();
            $table->string('phone', 10)->nullable(false)->change();
            $table->text('notes')->nullable(false)->change();
        });
    }
};
