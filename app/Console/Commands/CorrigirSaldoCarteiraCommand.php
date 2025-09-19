<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carteira;

class CorrigirSaldoCarteiraCommand extends Command
{
    protected $signature = 'carteira:corrigir-saldo {carteira_id}';
    protected $description = 'Corrige o saldo de uma carteira baseado nas transações';

    public function handle()
    {
        $carteiraId = $this->argument('carteira_id');
        $carteira = Carteira::find($carteiraId);
        
        if (!$carteira) {
            $this->error("Carteira {$carteiraId} não encontrada!");
            return;
        }
        
        $this->info("Carteira: {$carteira->name}");
        $this->info("Saldo atual: R$ " . number_format($carteira->balance, 2, ',', '.'));
        
        $creditos = $carteira->transacoes()->where('tipo', 'credit')->sum('valor');
        $debitos = $carteira->transacoes()->where('tipo', 'debit')->sum('valor');
        $saldoCorreto = $creditos - $debitos;
        
        $this->info("Créditos: R$ " . number_format($creditos, 2, ',', '.'));
        $this->info("Débitos: R$ " . number_format($debitos, 2, ',', '.'));
        $this->info("Saldo correto: R$ " . number_format($saldoCorreto, 2, ',', '.'));
        
        if ($carteira->balance != $saldoCorreto) {
            $carteira->update(['balance' => $saldoCorreto]);
            $this->info("✅ Saldo corrigido!");
        } else {
            $this->info("✅ Saldo já está correto.");
        }
        
        // Mostrar últimas 5 transações
        $this->info("\nÚltimas 5 transações:");
        $transacoes = $carteira->transacoes()->latest()->limit(5)->get();
        foreach ($transacoes as $transacao) {
            $this->line("- {$transacao->tipo}: R$ " . number_format($transacao->valor, 2, ',', '.') . " - {$transacao->descricao} ({$transacao->created_at})");
        }
    }
}
