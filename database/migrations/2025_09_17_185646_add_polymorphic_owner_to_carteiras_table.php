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
            $table->morphs('owner');
            $table->dropForeign(['conta_bancaria_id']);
            $table->dropColumn('conta_bancaria_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carteiras', function (Blueprint $table) {
            $table->dropMorphs('owner');
            $table->foreignId('conta_bancaria_id')->constrained('contas_bancarias')->onDelete('cascade');
        });
    }
};
