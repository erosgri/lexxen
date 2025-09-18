<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Modifica a coluna para adicionar o novo valor ao ENUM
            DB::statement("ALTER TABLE users CHANGE COLUMN status_aprovacao status_aprovacao ENUM('aguardando', 'aprovado', 'reprovado', 'bloqueado') NOT NULL DEFAULT 'aguardando'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverte a coluna para o estado anterior, sem 'bloqueado'
            DB::statement("ALTER TABLE users CHANGE COLUMN status_aprovacao status_aprovacao ENUM('aguardando', 'aprovado', 'reprovado') NOT NULL DEFAULT 'aguardando'");
        });
    }
};
