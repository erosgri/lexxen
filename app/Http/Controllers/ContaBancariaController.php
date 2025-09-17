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
        $query = ContaBancaria::with(['user', 'carteiras']); // Carrega usuário e carteiras

        if ($request->has('status')) {
            $query->where('status', $request->status);
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
        return back()->with('success', 'Conta ativada com sucesso.');
    }

    /**
     * Reprova (bloqueia) uma conta bancária.
     */
    public function reprove(ContaBancaria $conta)
    {
        $conta->update(['status' => 'BLOQUEADA']);
        return back()->with('success', 'Conta bloqueada com sucesso.');
    }
}