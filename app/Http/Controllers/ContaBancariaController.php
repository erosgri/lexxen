<?php

namespace App\Http\Controllers;

use App\Models\ContaBancaria;
use Illuminate\Http\Request;

class ContaBancariaController extends Controller
{
    /**
     * Mostra a lista de contas bancárias.
     */
    public function index(Request $request)
    {
        $query = ContaBancaria::with('user'); // Carrega apenas o usuário

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tipo_conta') && !empty($request->get('tipo_conta'))) {
            $query->where('tipo_conta', $request->get('tipo_conta'));
        }

        $contas = $query->latest()->paginate(20);

        return view('admin.contas.index', compact('contas'));
    }

    /**
     * Aprova uma conta bancária.
     */
    public function approve(ContaBancaria $conta)
    {
        $conta->update(['status' => 'ATIVA']);
        
        // Ativa apenas a carteira específica da conta aprovada
        $user = $conta->user;
        $owner = null;
        
        if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
            $owner = $user->pessoaJuridica;
        }
        
        if ($owner) {
            // Busca a carteira específica criada para esta conta
            $tipoContaFormatado = ucfirst($conta->tipo_conta);
            $nomeCarteira = 'Principal - ' . $tipoContaFormatado;
            
            // Ativa apenas a carteira específica desta conta
            $carteira = $owner->carteiras()
                ->where('name', $nomeCarteira)
                ->where('status', 'AGUARDANDO_LIBERACAO')
                ->where('approval_status', 'pending')
                ->first();
                
            if ($carteira) {
                $carteira->update([
                    'status' => 'ATIVA',
                    'approval_status' => 'approved'
                ]);
            }
        }
        
        return back()->with('success', 'Conta ativada com sucesso.');
    }

    /**
     * Reprova (bloqueia) uma conta bancária.
     */
    public function reprove(ContaBancaria $conta)
    {
        $conta->update(['status' => 'BLOQUEADA']);
        
        // Desativa as carteiras do usuário quando a conta for reprovada
        $user = $conta->user;
        if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
            $user->pessoaFisica->carteiras()
                ->where('status', 'AGUARDANDO_LIBERACAO')
                ->update([
                    'status' => 'DESATIVA',
                    'approval_status' => 'rejected'
                ]);
        } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
            $user->pessoaJuridica->carteiras()
                ->where('status', 'AGUARDANDO_LIBERACAO')
                ->update([
                    'status' => 'DESATIVA',
                    'approval_status' => 'rejected'
                ]);
        }
        
        return back()->with('success', 'Conta bloqueada com sucesso.');
    }
}