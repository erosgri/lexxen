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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carteira_origem_id')->constrained('carteiras')->onDelete('cascade');
            $table->foreignId('carteira_destino_id')->constrained('carteiras')->onDelete('cascade');
            $table->decimal('valor', 15, 2);
            $table->text('descricao');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('idempotency_key')->unique();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['carteira_origem_id', 'status']);
            $table->index(['carteira_destino_id', 'status']);
            $table->index('idempotency_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
