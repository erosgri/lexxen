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
        Schema::table('contas_bancarias', function (Blueprint $table) {
            // Alterar o ENUM para incluir os novos valores
            $table->enum('tipo_conta', [
                'corrente', 'poupanca', 'salario',
                'corrente_pf', 'poupanca_pf',
                'empresarial_pj'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_bancarias', function (Blueprint $table) {
            // Reverter para o ENUM original
            $table->enum('tipo_conta', ['corrente', 'poupanca', 'salario'])->change();
        });
    }
};
