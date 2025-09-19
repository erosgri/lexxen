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
        // Adicionar os novos valores 'empresarial_pj' e 'empresarial_pf' ao ENUM existente
        DB::statement("ALTER TABLE contas_bancarias MODIFY COLUMN tipo_conta ENUM('corrente', 'poupanca', 'salario', 'corrente_pf', 'poupanca_pf', 'corrente_pj', 'poupanca_pj', 'empresarial_pj', 'empresarial_pf') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover os valores 'empresarial_pj' e 'empresarial_pf' do ENUM
        DB::statement("ALTER TABLE contas_bancarias MODIFY COLUMN tipo_conta ENUM('corrente', 'poupanca', 'salario', 'corrente_pf', 'poupanca_pf', 'corrente_pj', 'poupanca_pj') NOT NULL");
    }
};
