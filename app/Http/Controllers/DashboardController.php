<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->tipo_usuario === 'admin') {
            $totalUsers = \App\Models\User::count();
            $totalPessoaFisica = \App\Models\User::where('tipo_usuario', 'pessoa_fisica')->count();
            $totalPessoaJuridica = \App\Models\User::where('tipo_usuario', 'pessoa_juridica')->count();
            $usersAtivos = \App\Models\User::where('ativo', true)->count();
            $totalContasBancarias = \App\Models\ContaBancaria::count();
            $contasAtivas = \App\Models\ContaBancaria::where('status', 'ATIVA')->count();
            $contasBloqueadas = \App\Models\ContaBancaria::where('status', 'BLOQUEADA')->count();
            $contasAguardandoAprovacao = \App\Models\ContaBancaria::where('status', 'AGUARDANDO_APROVACAO')->count();
            $usersAguardando = \App\Models\User::where('status_aprovacao', 'aguardando')->count();
            $usersAprovados = \App\Models\User::where('status_aprovacao', 'aprovado')->count();
            $usersReprovadosBloqueados = \App\Models\User::whereIn('status_aprovacao', ['reprovado', 'bloqueado'])->count();
            
            // Estatísticas de transferências
            $totalTransferencias = \App\Models\Transfer::count();
            $transferenciasHoje = \App\Models\Transfer::whereDate('created_at', today())->count();
            $transferenciasPendentes = \App\Models\Transfer::where('status', 'pending')->count();
            $transferenciasConcluidas = \App\Models\Transfer::where('status', 'completed')->count();
            
            // Estatísticas de carteiras
            $totalCarteiras = \App\Models\Carteira::count();
            $carteirasAtivas = \App\Models\Carteira::where('status', 'ATIVA')->count();
            $carteirasAguardandoLiberacao = \App\Models\Carteira::where('status', 'AGUARDANDO_LIBERACAO')->count();
            
            return view('dashboard', compact(
                'totalUsers', 
                'totalPessoaFisica', 
                'totalPessoaJuridica', 
                'usersAtivos',
                'totalContasBancarias',
                'contasAtivas',
                'contasBloqueadas',
                'contasAguardandoAprovacao',
                'usersAguardando',
                'usersAprovados',
                'usersReprovadosBloqueados',
                'totalTransferencias',
                'transferenciasHoje',
                'transferenciasPendentes',
                'transferenciasConcluidas',
                'totalCarteiras',
                'carteirasAtivas',
                'carteirasAguardandoLiberacao'
            ));
        }

        // Para usuários não-admin
        $user = Auth::user()->fresh(['contasBancarias', 'pessoaFisica.carteiras', 'pessoaJuridica.carteiras']);
        $contas = $user->contasBancarias;
        
        $transacoes = [];
        $carteiras = collect(); // Inicializa a coleção de carteiras

        if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
            $owner = $user->pessoaFisica;
            // A busca de carteiras agora será feita a partir do usuário 100% atualizado
            $carteiras = $owner->carteiras()
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->orderBy('created_at')
                ->get();
            $transacoes = $carteiras->pluck('transacoes')->flatten()->sortByDesc('created_at')->take(10);
        } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
            $owner = $user->pessoaJuridica;
            // A busca de carteiras agora será feita a partir do usuário 100% atualizado
            $carteiras = $owner->carteiras()
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->orderBy('created_at')
                ->get();
            $transacoes = $carteiras->pluck('transacoes')->flatten()->sortByDesc('created_at')->take(10);
        }
        
        // Garantir que apenas carteiras do usuário logado sejam carregadas
        $carteiras = $carteiras->filter(function($carteira) use ($user) {
            if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
                return $carteira->owner_id === $user->pessoaFisica->id && 
                       $carteira->owner_type === 'App\Models\PessoaFisica';
            } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
                return $carteira->owner_id === $user->pessoaJuridica->id && 
                       $carteira->owner_type === 'App\Models\PessoaJuridica';
            }
            return false;
        });

        // Calcular saldo total de todas as carteiras ativas
        $saldoTotal = $carteiras->sum('balance');

        // Separar contas por tipo de conta
        $contasFisicas = $contas->filter(function($conta) {
            return in_array($conta->tipo_conta, ['corrente', 'poupanca']);
        });
        
        $contasJuridicas = $contas->filter(function($conta) {
            return $conta->tipo_conta === 'empresarial';
        });

        return view('conta.index', compact('user', 'contas', 'contasFisicas', 'contasJuridicas', 'transacoes', 'carteiras', 'saldoTotal'));
    }
}

