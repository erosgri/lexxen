<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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
}

