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
        Schema::create('carteiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_bancaria_id')->constrained('contas_bancarias')->onDelete('cascade');
            $table->string('name')->default('Principal');
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('type', ['DEFAULT', 'WALLET'])->default('DEFAULT');
            $table->enum('status', ['ATIVA', 'DESATIVA'])->default('ATIVA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carteiras');
    }
};
