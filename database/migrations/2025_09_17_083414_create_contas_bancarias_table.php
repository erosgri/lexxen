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
        Schema::create('contas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->string('agencia', 10);
            $table->enum('tipo_conta', ['corrente', 'poupanca', 'salario']);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->decimal('limite', 15, 2)->default(0);
            $table->enum('status', ['ATIVA', 'BLOQUEADA'])->default('ATIVA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas_bancarias');
    }
};
