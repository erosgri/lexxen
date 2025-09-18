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
        Schema::create('extratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carteira_id')->constrained('carteiras')->onDelete('cascade');
            $table->enum('tipo_operacao', ['transferencia_origem', 'transferencia_destino', 'saque', 'deposito', 'outros']);
            $table->decimal('valor', 15, 2);
            $table->decimal('saldo_apos_operacao', 15, 2);
            $table->string('conta_origem')->nullable(); // Nome ou identificador da conta origem
            $table->string('conta_destino')->nullable(); // Nome ou identificador da conta destino
            $table->text('descricao');
            $table->timestamp('data_operacao');
            $table->timestamps();
            
            $table->index(['carteira_id', 'data_operacao']);
            $table->index(['carteira_id', 'tipo_operacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extratos');
    }
};
