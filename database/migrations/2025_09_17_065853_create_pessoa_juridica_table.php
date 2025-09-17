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
        Schema::create('pessoa_juridica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj', 14)->unique();
            $table->string('inscricao_estadual', 20)->nullable();
            $table->string('inscricao_municipal', 20)->nullable();
            $table->string('telefone', 15)->nullable();
            $table->string('celular', 15)->nullable();
            $table->string('endereco');
            $table->string('numero', 10);
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('cep', 8);
            $table->string('representante_legal');
            $table->string('cpf_representante', 11);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa_juridica');
    }
};
