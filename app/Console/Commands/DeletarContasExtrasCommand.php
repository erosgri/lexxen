<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContaBancaria;
use App\Models\User;

class DeletarContasExtrasCommand extends Command
{
    protected $signature = 'app:deletar-contas-extras {--confirm : Confirma a exclusão sem perguntar}';
    protected $description = 'Deleta todas as contas extras de todos os usuários, mantendo apenas uma conta por usuário';

    public function handle()
    {
        $this->info('🔍 Verificando contas existentes...');
        
        // Contar contas por usuário
        $contasPorUsuario = ContaBancaria::with('user')
            ->get()
            ->groupBy('user_id');
        
        $totalContas = ContaBancaria::count();
        $totalUsuarios = User::count();
        
        $this->info("📊 Estatísticas:");
        $this->info("   Total de usuários: {$totalUsuarios}");
        $this->info("   Total de contas: {$totalContas}");
        
        // Mostrar detalhes por usuário
        $this->info("\n👥 Contas por usuário:");
        foreach ($contasPorUsuario as $userId => $contas) {
            $user = $contas->first()->user;
            $this->info("   {$user->name} (ID: {$userId}): {$contas->count()} contas");
            foreach ($contas as $conta) {
                $this->info("     - Conta {$conta->id}: {$conta->tipo_conta} ({$conta->status})");
            }
        }
        
        // Calcular quantas contas serão deletadas
        $contasParaDeletar = 0;
        foreach ($contasPorUsuario as $contas) {
            if ($contas->count() > 1) {
                $contasParaDeletar += $contas->count() - 1;
            }
        }
        
        if ($contasParaDeletar === 0) {
            $this->info("✅ Nenhuma conta extra encontrada. Cada usuário tem apenas uma conta.");
            return;
        }
        
        $this->warn("\n⚠️  {$contasParaDeletar} contas extras serão deletadas!");
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('Deseja continuar com a exclusão?')) {
                $this->info('❌ Operação cancelada.');
                return;
            }
        }
        
        $this->info("\n🗑️  Iniciando exclusão das contas extras...");
        
        $deletadas = 0;
        foreach ($contasPorUsuario as $userId => $contas) {
            if ($contas->count() > 1) {
                $user = $contas->first()->user;
                $this->info("   Processando usuário: {$user->name}");
                
                // Manter apenas a primeira conta (mais antiga)
                $contaParaManter = $contas->sortBy('created_at')->first();
                $contasParaExcluir = $contas->where('id', '!=', $contaParaManter->id);
                
                $this->info("     ✅ Mantendo: Conta {$contaParaManter->id} ({$contaParaManter->tipo_conta})");
                
                foreach ($contasParaExcluir as $conta) {
                    $this->info("     🗑️  Deletando: Conta {$conta->id} ({$conta->tipo_conta})");
                    $conta->delete();
                    $deletadas++;
                }
            }
        }
        
        $this->info("\n✅ Exclusão concluída!");
        $this->info("   Contas deletadas: {$deletadas}");
        $this->info("   Contas restantes: " . ContaBancaria::count());
    }
}

