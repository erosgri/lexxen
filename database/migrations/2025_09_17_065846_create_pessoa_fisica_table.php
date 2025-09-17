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
        Schema::create('pessoa_fisica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nome_completo');
            $table->string('cpf', 11)->unique();
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable();
            $table->string('telefone', 15)->nullable();
            $table->string('celular', 15)->nullable();
            $table->string('endereco');
            $table->string('numero', 10);
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('cep', 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa_fisica');
    }
};
