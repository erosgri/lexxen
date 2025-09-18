<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ContaBancaria;
use App\Models\Carteira;

class DebugTransferenciaCommand extends Command
{
    protected $signature = 'app:debug-transferencia';
    protected $description = 'Debug específico da transferência que está falhando';

    public function handle()
    {
        $this->info('🔍 Debugando transferência específica...');
        
        // Dados da transferência que está falhando (da imagem)
        $agenciaDestino = '1981';
        $contaDestino = '75604-08';
        $valor = 4727.00;
        
        $this->info("Dados da transferência:");
        $this->line("  - Agência destino: {$agenciaDestino}");
        $this->line("  - Conta destino: {$contaDestino}");
        $this->line("  - Valor: R$ " . number_format($valor, 2, ',', '.'));
        
        // 1. Verificar se a conta de destino existe
        $this->info("\n🔍 PASSO 1: Verificando conta de destino...");
        
        $conta = ContaBancaria::where('agencia', $agenciaDestino)
            ->where('numero', $contaDestino)
            ->first();
            
        if ($conta) {
            $this->info("✅ Conta encontrada:");
            $this->line("  - ID: {$conta->id}");
            $this->line("  - Agência: {$conta->agencia}");
            $this->line("  - Número: {$conta->numero}");
            $this->line("  - Status: {$conta->status}");
            $this->line("  - Usuário: {$conta->user->name} (ID: {$conta->user_id})");
        } else {
            $this->error("❌ Conta NÃO encontrada!");
            
            // Buscar contas similares
            $contasPorAgencia = ContaBancaria::where('agencia', $agenciaDestino)->get();
            $contasPorNumero = ContaBancaria::where('numero', $contaDestino)->get();
            
            $this->line("Contas com agência {$agenciaDestino}: {$contasPorAgencia->count()}");
            $this->line("Contas com número {$contaDestino}: {$contasPorNumero->count()}");
            
            return 1;
        }
        
        // 2. Verificar se a conta está ativa
        $this->info("\n🔍 PASSO 2: Verificando status da conta...");
        
        if ($conta->status === 'ATIVA') {
            $this->info("✅ Conta está ATIVA");
        } else {
            $this->error("❌ Conta NÃO está ativa: {$conta->status}");
        }
        
        // 3. Verificar carteiras do usuário de destino
        $this->info("\n🔍 PASSO 3: Verificando carteiras do usuário de destino...");
        
        $userDestino = $conta->user;
        $ownerDestino = $userDestino->tipo_usuario === 'pessoa_fisica' 
            ? $userDestino->pessoaFisica 
            : $userDestino->pessoaJuridica;
            
        if (!$ownerDestino) {
            $this->error("❌ Owner não encontrado para usuário {$userDestino->name}");
            return 1;
        }
        
        $carteiras = $ownerDestino->carteiras;
        $this->info("Total de carteiras: {$carteiras->count()}");
        
        foreach ($carteiras as $carteira) {
            $this->line("  - ID: {$carteira->id} | Nome: {$carteira->name} | Status: {$carteira->status} | Approval: {$carteira->approval_status}");
        }
        
        // 4. Verificar carteira DEFAULT específica
        $this->info("\n🔍 PASSO 4: Verificando carteira DEFAULT...");
        
        $carteiraDefault = $ownerDestino->carteiras()
            ->where('type', 'DEFAULT')
            ->where('status', 'ATIVA')
            ->where('approval_status', 'approved')
            ->first();
            
        if ($carteiraDefault) {
            $this->info("✅ Carteira DEFAULT encontrada: {$carteiraDefault->name} (ID: {$carteiraDefault->id})");
        } else {
            $this->error("❌ Carteira DEFAULT NÃO encontrada!");
            $this->line("Critérios: type='DEFAULT', status='ATIVA', approval_status='approved'");
            
            // Mostrar carteiras que não atendem aos critérios
            $carteirasProblema = $ownerDestino->carteiras()
                ->where(function($query) {
                    $query->where('type', '!=', 'DEFAULT')
                          ->orWhere('status', '!=', 'ATIVA')
                          ->orWhere('approval_status', '!=', 'approved');
                })
                ->get();
                
            if ($carteirasProblema->isNotEmpty()) {
                $this->warn("Carteiras que não atendem aos critérios:");
                foreach ($carteirasProblema as $c) {
                    $this->line("  - {$c->name}: type={$c->type}, status={$c->status}, approval={$c->approval_status}");
                }
            }
        }
        
        // 5. Verificar usuário atual (quem está fazendo a transferência)
        $this->info("\n🔍 PASSO 5: Verificando usuário atual...");
        
        $userAtual = User::where('email', 'maria.silva@example.com')->first();
        if ($userAtual) {
            $this->info("Usuário atual: {$userAtual->name} (ID: {$userAtual->id})");
            
            // Verificar se é o mesmo usuário
            if ($userAtual->id === $userDestino->id) {
                $this->warn("⚠️  MESMO USUÁRIO! Maria está tentando transferir para ela mesma.");
                $this->line("Isso deveria ser rejeitado com mensagem específica.");
            } else {
                $this->info("✅ Usuários diferentes - transferência válida");
            }
        }
        
        return 0;
    }
}



