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
        Schema::table('carteiras', function (Blueprint $table) {
            $table->enum('status', ['ATIVA', 'DESATIVA', 'AGUARDANDO_LIBERACAO'])->default('AGUARDANDO_LIBERACAO')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carteiras', function (Blueprint $table) {
            $table->enum('status', ['ATIVA', 'DESATIVA'])->default('ATIVA')->change();
        });
    }
};
