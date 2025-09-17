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
            // Muda a coluna para incluir o novo status e define o novo padrão
            $table->enum('status', ['ATIVA', 'BLOQUEADA', 'AGUARDANDO_APROVACAO'])->default('AGUARDANDO_APROVACAO')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_bancarias', function (Blueprint $table) {
            // Reverte para o estado anterior
            $table->enum('status', ['ATIVA', 'BLOQUEADA'])->default('ATIVA')->change();
        });
    }
};
