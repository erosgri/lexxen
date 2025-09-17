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
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained('contas_bancarias')->onDelete('cascade');
            $table->enum('tipo', ['DEPOSITO', 'SAQUE', 'TRANSFERENCIA_ENVIADA', 'TRANSFERENCIA_RECEBIDA']);
            $table->decimal('valor', 15, 2);
            $table->string('descricao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes');
    }
};
