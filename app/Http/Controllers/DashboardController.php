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
            $contasAtivas = \App\Models\ContaBancaria::where('status', 'ativa')->count();
            $contasBloqueadas = \App\Models\ContaBancaria::where('status', 'bloqueada')->count();
            $usersAguardando = \App\Models\User::where('status_aprovacao', 'aguardando')->count();
            $usersAprovados = \App\Models\User::where('status_aprovacao', 'aprovado')->count();
            $usersReprovadosBloqueados = \App\Models\User::whereIn('status_aprovacao', ['reprovado', 'bloqueado'])->count();
            
            return view('dashboard', compact(
                'totalUsers', 
                'totalPessoaFisica', 
                'totalPessoaJuridica', 
                'usersAtivos',
                'totalContasBancarias',
                'contasAtivas',
                'contasBloqueadas',
                'usersAguardando',
                'usersAprovados',
                'usersReprovadosBloqueados'
            ));
        }

        // Para usuários não-admin
        $user = Auth::user();
        $contas = $user->contasBancarias;
        
        $transacoes = [];
        $carteiras = collect(); // Inicializa a coleção de carteiras

        if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
            $owner = $user->pessoaFisica;
            // Buscar carteiras sempre frescos do banco para garantir dados atualizados
            $carteiras = $owner->carteiras()
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->orderBy('created_at')
                ->get();
            $transacoes = $carteiras->pluck('transacoes')->flatten()->sortByDesc('created_at')->take(10);
        } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
            $owner = $user->pessoaJuridica;
            // Buscar carteiras sempre frescos do banco para garantir dados atualizados
            $carteiras = $owner->carteiras()
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->orderBy('created_at')
                ->get();
            $transacoes = $carteiras->pluck('transacoes')->flatten()->sortByDesc('created_at')->take(10);
        }

        return view('conta.index', compact('user', 'contas', 'transacoes', 'carteiras'));
    }
}

