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
        // Atualizar ENUM para apenas 3 tipos simples
        DB::statement("ALTER TABLE contas_bancarias MODIFY COLUMN tipo_conta ENUM('corrente', 'poupanca', 'empresarial') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para ENUM anterior
        DB::statement("ALTER TABLE contas_bancarias MODIFY COLUMN tipo_conta ENUM('corrente', 'poupanca', 'salario', 'corrente_pf', 'poupanca_pf', 'corrente_pj', 'poupanca_pj', 'empresarial_pj', 'empresarial_pf') NOT NULL");
    }
};
