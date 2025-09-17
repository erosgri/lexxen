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
            $usersAguardando = \App\Models\User::where('status_aprovacao', 'aguardando')->count();
            $usersAprovados = \App\Models\User::where('status_aprovacao', 'aprovado')->count();
            $usersReprovados = \App\Models\User::where('status_aprovacao', 'reprovado')->count();
            
            return view('dashboard', compact(
                'totalUsers', 
                'totalPessoaFisica', 
                'totalPessoaJuridica', 
                'usersAtivos',
                'totalContasBancarias',
                'contasAtivas',
                'usersAguardando',
                'usersAprovados',
                'usersReprovados'
            ));
        }

        // Para usuários não-admin
        $user = Auth::user();
        $contas = $user->contasBancarias()->with('carteiras')->get(); // Carrega as carteiras para evitar N+1 queries
        
        // A lógica de transações pode ser ajustada para mostrar um resumo geral ou removida desta view principal
        $transacoes = []; 
        if ($contas->isNotEmpty()) {
            // Pega transações da primeira conta ativa, se houver, para o resumo
            $primeiraContaAtiva = $contas->firstWhere('status', 'ATIVA');
            if ($primeiraContaAtiva) {
                $transacoes = $primeiraContaAtiva->transacoes()->latest()->take(10)->get();
            }
        }

        return view('conta.index', compact('user', 'contas', 'transacoes'));
    }
}

