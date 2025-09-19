<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use App\Models\ContaBancaria;
use App\Models\Carteira;
use App\Models\Transacao;
use App\Models\Extrato;
use Illuminate\Support\Facades\DB;

class LimparUsuariosAguardandoCommand extends Command
{
    protected $signature = 'usuarios:limpar-aguardando {--keep=20 : Número de usuários para manter}';
    protected $description = 'Mantém apenas 20 usuários aguardando aprovação e exclui permanentemente o resto';

    public function handle()
    {
        $keep = $this->option('keep');
        
        $this->info("Mantendo apenas {$keep} usuários aguardando aprovação...");
        
        // Buscar todos os usuários aguardando aprovação
        $usuariosAguardando = User::where('status_aprovacao', 'aguardando')
            ->orderBy('created_at', 'asc') // Manter os mais antigos
            ->get();
        
        $totalAguardando = $usuariosAguardando->count();
        
        if ($totalAguardando <= $keep) {
            $this->info("Apenas {$totalAguardando} usuários aguardando. Nenhuma exclusão necessária.");
            return Command::SUCCESS;
        }
        
        $usuariosParaExcluir = $usuariosAguardando->skip($keep);
        $usuariosParaManter = $usuariosAguardando->take($keep);
        
        $this->info("Total de usuários aguardando: {$totalAguardando}");
        $this->info("Usuários para manter: {$usuariosParaManter->count()}");
        $this->info("Usuários para excluir: {$usuariosParaExcluir->count()}");
        
        if (!$this->confirm("Tem certeza que deseja excluir permanentemente {$usuariosParaExcluir->count()} usuários?")) {
            $this->info("Operação cancelada.");
            return Command::SUCCESS;
        }
        
        $excluidos = 0;
        $erros = 0;
        
        foreach ($usuariosParaExcluir as $user) {
            try {
                DB::transaction(function () use ($user) {
                    // Excluir transações e extratos das carteiras
                    $carteiras = collect();
                    
                    if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
                        $carteiras = $user->pessoaFisica->carteiras;
                    } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
                        $carteiras = $user->pessoaJuridica->carteiras;
                    }
                    
                    foreach ($carteiras as $carteira) {
                        // Excluir transações
                        Transacao::where('conta_id', $carteira->id)->delete();
                        
                        // Excluir extratos
                        Extrato::where('carteira_id', $carteira->id)->delete();
                    }
                    
                    // Excluir carteiras
                    Carteira::whereIn('id', $carteiras->pluck('id'))->delete();
                    
                    // Excluir contas bancárias
                    ContaBancaria::where('user_id', $user->id)->delete();
                    
                    // Excluir perfil (pessoa física ou jurídica)
                    if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
                        $user->pessoaFisica->delete();
                    } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
                        $user->pessoaJuridica->delete();
                    }
                    
                    // Excluir usuário
                    $user->delete();
                });
                
                $excluidos++;
                $this->line("✅ Usuário excluído: {$user->name} ({$user->email})");
                
            } catch (\Exception $e) {
                $erros++;
                $this->error("❌ Erro ao excluir usuário {$user->name}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("🎉 Limpeza concluída!");
        $this->info("✅ Usuários excluídos: {$excluidos}");
        $this->info("❌ Erros: {$erros}");
        $this->info("📊 Usuários aguardando restantes: " . User::where('status_aprovacao', 'aguardando')->count());
        
        return Command::SUCCESS;
    }
}
