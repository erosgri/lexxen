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
        Schema::table('transfers', function (Blueprint $table) {
            // Campos para transferências externas
            $table->string('agencia_destino')->nullable()->after('carteira_destino_id');
            $table->string('conta_destino')->nullable()->after('agencia_destino');
            $table->string('tipo')->default('entre_carteiras')->after('conta_destino');
            
            // Tornar carteira_destino_id nullable para transferências externas
            $table->foreignId('carteira_destino_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['agencia_destino', 'conta_destino', 'tipo']);
            $table->foreignId('carteira_destino_id')->nullable(false)->change();
        });
    }
};
