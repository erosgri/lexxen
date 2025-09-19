<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContaBancaria;

class BuscarCarteiraContaCommand extends Command
{
    protected $signature = 'carteira:buscar-conta {agencia} {conta}';
    protected $description = 'Busca a carteira específica de uma conta bancária';

    public function handle()
    {
        $agencia = $this->argument('agencia');
        $conta = $this->argument('conta');
        
        $contaBancaria = ContaBancaria::where('agencia', $agencia)
            ->where(function ($query) use ($conta) {
                $contaSemHifen = str_replace('-', '', $conta);
                $query->where('numero', $conta)
                      ->orWhere('numero', $contaSemHifen)
                      ->orWhereRaw("REPLACE(numero, '-', '') = ?", [$contaSemHifen]);
            })
            ->first();
        
        if (!$contaBancaria) {
            $this->error("Conta bancária {$agencia}-{$conta} não encontrada!");
            return;
        }
        
        $this->info("Conta bancária: {$contaBancaria->agencia}-{$contaBancaria->numero}");
        
        $user = $contaBancaria->user;
        $owner = $user->tipo_usuario === 'pessoa_fisica' ? $user->pessoaFisica : $user->pessoaJuridica;
        
        // Busca carteira específica da conta bancária
        $carteiraEspecifica = $owner->carteiras()
            ->where('name', 'like', '%' . $contaBancaria->agencia . '%')
            ->where('name', 'like', '%' . $contaBancaria->numero . '%')
            ->first();
        
        if ($carteiraEspecifica) {
            $this->info("✅ Carteira específica encontrada:");
            $this->line("ID: {$carteiraEspecifica->id} | Nome: {$carteiraEspecifica->name} | Saldo: R$ " . number_format($carteiraEspecifica->balance, 2, ',', '.'));
        } else {
            $this->warn("❌ Carteira específica não encontrada para a conta {$agencia}-{$conta}");
            
            // Busca carteira DEFAULT
            $carteiraDefault = $owner->carteiras()->where('type', 'DEFAULT')->first();
            if ($carteiraDefault) {
                $this->info("Usando carteira DEFAULT:");
                $this->line("ID: {$carteiraDefault->id} | Nome: {$carteiraDefault->name} | Saldo: R$ " . number_format($carteiraDefault->balance, 2, ',', '.'));
            }
        }
        
        // Lista todas as carteiras para referência
        $this->info("\nTodas as carteiras do usuário:");
        $carteiras = $owner->carteiras()->orderBy('created_at')->get();
        foreach ($carteiras as $carteira) {
            $this->line("ID: {$carteira->id} | Nome: {$carteira->name} | Saldo: R$ " . number_format($carteira->balance, 2, ',', '.'));
        }
    }
}
