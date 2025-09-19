<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ContaBancaria;
use App\Models\Carteira;

class VerificarRegrasCarteirasCommand extends Command
{
    protected $signature = 'app:verificar-regras-carteiras';
    protected $description = 'Verifica se todas as regras de negócio das carteiras estão sendo seguidas';

    public function handle()
    {
        $this->info('🔍 Verificando regras de negócio das carteiras...');
        $this->newLine();

        $violacoes = [];

        // 1. Verificar se carteiras "Principal" têm saldo zero
        $this->line('1. Verificando carteiras "Principal" com saldo zero...');
        $carteirasPrincipaisComSaldo = Carteira::where('name', 'like', 'Principal - %')
            ->where('balance', '>', 0)
            ->get();

        if ($carteirasPrincipaisComSaldo->count() > 0) {
            $this->warn("   ⚠️ {$carteirasPrincipaisComSaldo->count()} carteiras 'Principal' com saldo > 0:");
            foreach ($carteirasPrincipaisComSaldo as $carteira) {
                $this->line("     - {$carteira->name}: R$ " . number_format($carteira->balance, 2, ',', '.'));
                $violacoes[] = "Carteira '{$carteira->name}' tem saldo R$ " . number_format($carteira->balance, 2, ',', '.');
            }
        } else {
            $this->info('   ✅ Todas as carteiras "Principal" têm saldo zero');
        }

        $this->newLine();

        // 2. Verificar se contas aprovadas têm carteiras ativas (apenas usuários aprovados)
        $this->line('2. Verificando contas aprovadas...');
        $contasAprovadas = ContaBancaria::where('status', 'ATIVA')
            ->whereHas('user', function($query) {
                $query->where('status_aprovacao', 'aprovado');
            })
            ->get();
        $contasSemCarteira = 0;

        foreach ($contasAprovadas as $conta) {
            $user = $conta->user;
            $owner = $user->tipo_usuario === 'pessoa_fisica' ? $user->pessoaFisica : $user->pessoaJuridica;
            
            if ($owner) {
                $tipoFormatado = ucfirst($conta->tipo_conta);
                $carteiraPrincipal = $owner->carteiras()
                    ->where('name', 'like', "Principal - {$tipoFormatado}%")
                    ->where('status', 'ATIVA')
                    ->where('approval_status', 'approved')
                    ->first();

                if (!$carteiraPrincipal) {
                    $contasSemCarteira++;
                    $this->warn("   ⚠️ Conta {$conta->numero} ({$conta->tipo_conta}) sem carteira ativa");
                    $violacoes[] = "Conta {$conta->numero} sem carteira ativa";
                }
            }
        }

        if ($contasSemCarteira === 0) {
            $this->info('   ✅ Todas as contas de usuários aprovados têm carteiras ativas');
        }

        $this->newLine();

        // 3. Verificar usuários não aprovados com carteiras ativas
        $this->line('3. Verificando usuários não aprovados...');
        $usuariosNaoAprovados = User::where('status_aprovacao', '!=', 'aprovado')
            ->whereIn('tipo_usuario', ['pessoa_fisica', 'pessoa_juridica'])
            ->get();

        $usuariosNaoAprovadosComCarteiras = 0;
        foreach ($usuariosNaoAprovados as $user) {
            $owner = $user->tipo_usuario === 'pessoa_fisica' ? $user->pessoaFisica : $user->pessoaJuridica;
            if ($owner) {
                $carteirasAtivas = $owner->carteiras()
                    ->where('status', 'ATIVA')
                    ->where('approval_status', 'approved')
                    ->count();
                
                if ($carteirasAtivas > 0) {
                    $usuariosNaoAprovadosComCarteiras++;
                    $this->warn("   ⚠️ Usuário {$user->name} não aprovado com {$carteirasAtivas} carteiras ativas");
                    $violacoes[] = "Usuário {$user->name} não aprovado com carteiras ativas";
                }
            }
        }

        if ($usuariosNaoAprovadosComCarteiras === 0) {
            $this->info('   ✅ Usuários não aprovados não têm carteiras ativas');
        }

        $this->newLine();

        // 4. Verificar independência das contas (apenas carteiras "Principal")
        $this->line('4. Verificando independência das carteiras "Principal"...');
        $carteirasPrincipaisComSaldo = Carteira::where('name', 'like', 'Principal - %')
            ->where('status', 'ATIVA')
            ->where('approval_status', 'approved')
            ->get();

        $carteirasPrincipaisComSaldosIguais = 0;
        $saldosPrincipais = $carteirasPrincipaisComSaldo->pluck('balance')->unique();
        
        // Verificar se há carteiras "Principal" com saldos iguais (exceto zero)
        $saldosNaoZero = $saldosPrincipais->filter(function($saldo) {
            return $saldo > 0;
        });
        
        if ($saldosNaoZero->count() > 0) {
            $this->warn("   ⚠️ Carteiras 'Principal' com saldo > 0 encontradas");
            foreach ($carteirasPrincipaisComSaldo->where('balance', '>', 0) as $carteira) {
                $this->line("     - {$carteira->name}: R$ " . number_format($carteira->balance, 2, ',', '.'));
                $violacoes[] = "Carteira 'Principal' {$carteira->name} com saldo R$ " . number_format($carteira->balance, 2, ',', '.');
            }
        } else {
            $this->info('   ✅ Carteiras "Principal" mantêm independência (todas com saldo zero)');
        }

        $this->newLine();

        // Resumo
        if (count($violacoes) === 0) {
            $this->info('🎉 Todas as regras de negócio estão sendo seguidas!');
        } else {
            $this->error('❌ Encontradas ' . count($violacoes) . ' violações:');
            foreach ($violacoes as $violacao) {
                $this->line("   - {$violacao}");
            }
        }

        return Command::SUCCESS;
    }
}
