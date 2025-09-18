<?php

namespace App\Http\Controllers;

use App\Models\Carteira;
use App\Models\Transacao;
use App\Models\Extrato;
use App\Models\ContaBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtratoController extends Controller
{
    /**
     * Mostra o extrato com filtros avançados.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $owner = null;
        
        if ($user->tipo_usuario === 'pessoa_fisica') {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica') {
            $owner = $user->pessoaJuridica;
        }

        if (!$owner) {
            return back()->with('error', 'Perfil não encontrado.');
        }

        // Buscar carteiras do usuário
        $carteiras = $owner->carteiras()
            ->where('status', 'ATIVA')
            ->where('approval_status', 'approved')
            ->get();

        if ($carteiras->isEmpty()) {
            return back()->with('error', 'Nenhuma carteira ativa encontrada.');
        }

        // Aplicar filtros - usar modelo Extrato em vez de Transacao
        $query = Extrato::whereIn('carteira_id', $carteiras->pluck('id'));

        // Filtro por carteira
        if ($request->filled('carteira_id')) {
            $query->where('carteira_id', $request->carteira_id);
        }

        // Filtro por tipo de operação
        if ($request->filled('tipo_operacao')) {
            $query->where('tipo_operacao', $request->tipo_operacao);
        }

        // Filtro por data inicial
        if ($request->filled('data_inicial')) {
            $query->whereDate('data_operacao', '>=', $request->data_inicial);
        }

        // Filtro por data final
        if ($request->filled('data_final')) {
            $query->whereDate('data_operacao', '<=', $request->data_final);
        }

        // Filtro por descrição
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        // Filtro por valor mínimo
        if ($request->filled('valor_minimo')) {
            $query->where('valor', '>=', $request->valor_minimo);
        }

        // Filtro por valor máximo
        if ($request->filled('valor_maximo')) {
            $query->where('valor', '<=', $request->valor_maximo);
        }

        // Ordenação
        $ordenacao = $request->get('ordenacao', 'desc');
        $query->orderBy('data_operacao', $ordenacao);

        // Paginação
        $transacoes = $query->paginate(20)->withQueryString();

        // Calcular resumo financeiro
        $resumo = $this->calcularResumo($carteiras, $request);

        // Dados para os filtros
        $filtros = [
            'carteiras' => $carteiras,
            'tipos_operacao' => [
                'transferencia_origem' => 'Transferência Enviada',
                'transferencia_destino' => 'Transferência Recebida',
                'saque' => 'Saque',
                'deposito' => 'Depósito',
                'outros' => 'Outros'
            ],
            'ordenacoes' => ['desc' => 'Mais recentes', 'asc' => 'Mais antigas'],
        ];

        return view('extrato.index', compact('transacoes', 'resumo', 'filtros'));
    }

    /**
     * Mostra o extrato de uma carteira específica.
     */
    public function carteira(Carteira $carteira, Request $request)
    {
        $user = Auth::user();
        $owner = null;
        
        if ($user->tipo_usuario === 'pessoa_fisica') {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica') {
            $owner = $user->pessoaJuridica;
        }

        if (!$owner || !$owner->carteiras->contains($carteira)) {
            abort(403, 'Acesso não autorizado.');
        }

        // Aplicar filtros - usar modelo Extrato
        $query = $carteira->extratos();

        // Filtro por tipo de operação
        if ($request->filled('tipo_operacao')) {
            $query->where('tipo_operacao', $request->tipo_operacao);
        }

        // Filtro por data inicial
        if ($request->filled('data_inicial')) {
            $query->whereDate('data_operacao', '>=', $request->data_inicial);
        }

        // Filtro por data final
        if ($request->filled('data_final')) {
            $query->whereDate('data_operacao', '<=', $request->data_final);
        }

        // Filtro por descrição
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        // Ordenação
        $ordenacao = $request->get('ordenacao', 'desc');
        $query->orderBy('data_operacao', $ordenacao);

        // Paginação
        $transacoes = $query->paginate(20)->withQueryString();

        // Calcular resumo da carteira
        $resumo = $this->calcularResumoCarteira($carteira, $request);

        // Dados para os filtros
        $filtros = [
            'tipos_operacao' => [
                'transferencia_origem' => 'Transferência Enviada',
                'transferencia_destino' => 'Transferência Recebida',
                'saque' => 'Saque',
                'deposito' => 'Depósito',
                'outros' => 'Outros'
            ],
            'ordenacoes' => ['desc' => 'Mais recentes', 'asc' => 'Mais antigas'],
        ];

        return view('extrato.carteira', compact('carteira', 'transacoes', 'resumo', 'filtros'));
    }


    /**
     * Calcula o resumo financeiro geral.
     */
    protected function calcularResumo($carteiras, Request $request)
    {
        $query = Extrato::whereIn('carteira_id', $carteiras->pluck('id'));

        // Aplicar mesmos filtros do extrato
        if ($request->filled('carteira_id')) {
            $query->where('carteira_id', $request->carteira_id);
        }

        if ($request->filled('tipo_operacao')) {
            $query->where('tipo_operacao', $request->tipo_operacao);
        }

        if ($request->filled('data_inicial')) {
            $query->whereDate('data_operacao', '>=', $request->data_inicial);
        }

        if ($request->filled('data_final')) {
            $query->whereDate('data_operacao', '<=', $request->data_final);
        }

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->filled('valor_minimo')) {
            $query->where('valor', '>=', $request->valor_minimo);
        }

        if ($request->filled('valor_maximo')) {
            $query->where('valor', '<=', $request->valor_maximo);
        }

        $extratos = $query->get();

        $totalCreditos = $extratos->whereIn('tipo_operacao', ['transferencia_destino', 'deposito'])->sum('valor');
        $totalDebitos = $extratos->whereIn('tipo_operacao', ['transferencia_origem', 'saque'])->sum('valor');
        $saldoAtual = $carteiras->sum('balance');

        // Calcular saldo inicial (saldo atual - movimentação do período)
        $saldoInicial = $saldoAtual - $totalCreditos + $totalDebitos;

        return [
            'saldo_inicial' => $saldoInicial,
            'saldo_atual' => $saldoAtual,
            'total_creditos' => $totalCreditos,
            'total_debitos' => $totalDebitos,
            'saldo_periodo' => $totalCreditos - $totalDebitos,
            'quantidade_transacoes' => $extratos->count(),
        ];
    }

    /**
     * Calcula o resumo de uma carteira específica.
     */
    protected function calcularResumoCarteira(Carteira $carteira, Request $request)
    {
        $query = $carteira->extratos();

        // Aplicar mesmos filtros
        if ($request->filled('tipo_operacao')) {
            $query->where('tipo_operacao', $request->tipo_operacao);
        }

        if ($request->filled('data_inicial')) {
            $query->whereDate('data_operacao', '>=', $request->data_inicial);
        }

        if ($request->filled('data_final')) {
            $query->whereDate('data_operacao', '<=', $request->data_final);
        }

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        $extratos = $query->get();

        $totalCreditos = $extratos->whereIn('tipo_operacao', ['transferencia_destino', 'deposito'])->sum('valor');
        $totalDebitos = $extratos->whereIn('tipo_operacao', ['transferencia_origem', 'saque'])->sum('valor');
        $saldoAtual = $carteira->balance;

        // Calcular saldo inicial (saldo atual - movimentação do período)
        $saldoInicial = $saldoAtual - $totalCreditos + $totalDebitos;

        return [
            'saldo_inicial' => $saldoInicial,
            'saldo_atual' => $saldoAtual,
            'total_creditos' => $totalCreditos,
            'total_debitos' => $totalDebitos,
            'saldo_periodo' => $totalCreditos - $totalDebitos,
            'quantidade_transacoes' => $extratos->count(),
        ];
    }
}