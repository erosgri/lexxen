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
            $table->dropColumn(['saldo', 'limite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_bancarias', function (Blueprint $table) {
            $table->decimal('saldo', 15, 2)->default(0);
            $table->decimal('limite', 15, 2)->default(0);
        });
    }
};
