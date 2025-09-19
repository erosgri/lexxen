<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContaBancaria;

class ListarCarteirasUsuarioCommand extends Command
{
    protected $signature = 'carteira:listar-usuario {agencia} {conta}';
    protected $description = 'Lista todas as carteiras de um usuário baseado na conta bancária';

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
        
        $user = $contaBancaria->user;
        $this->info("Usuário: {$user->email}");
        $this->info("Conta bancária: {$contaBancaria->agencia}-{$contaBancaria->numero}");
        
        $owner = $user->tipo_usuario === 'pessoa_fisica' ? $user->pessoaFisica : $user->pessoaJuridica;
        $carteiras = $owner->carteiras()->orderBy('created_at')->get();
        
        $this->info("\nCarteiras do usuário:");
        foreach ($carteiras as $carteira) {
            $this->line("ID: {$carteira->id} | Nome: {$carteira->name} | Saldo: R$ " . number_format($carteira->balance, 2, ',', '.') . " | Tipo: {$carteira->type} | Status: {$carteira->status} | Criada: {$carteira->created_at}");
        }
        
        // Mostrar qual carteira é usada para transferências (DEFAULT)
        $carteiraDefault = $owner->carteiras()->where('type', 'DEFAULT')->first();
        if ($carteiraDefault) {
            $this->info("\nCarteira usada para transferências (DEFAULT):");
            $this->line("ID: {$carteiraDefault->id} | Nome: {$carteiraDefault->name} | Saldo: R$ " . number_format($carteiraDefault->balance, 2, ',', '.'));
        }
    }
}
