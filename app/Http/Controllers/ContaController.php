<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContaBancaria;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller
{
    /**
     * Mostra o formulário para abrir uma nova conta.
     */
    public function abrirContaForm()
    {
        return view('conta.abrir');
    }

    /**
     * Processa a criação de uma nova conta bancária.
     */
    public function abrirConta(Request $request)
    {
        $request->validate([
            'tipo_conta' => ['required', 'in:corrente,poupanca,salario'],
        ]);

        $faker = Faker::create('pt_BR');
        $user = Auth::user();

        $conta = ContaBancaria::create([
            'user_id' => $user->id,
            'numero' => ContaBancaria::gerarNumeroConta(),
            'agencia' => $faker->numerify('####'),
            'tipo_conta' => $request->input('tipo_conta'),
            'status' => 'AGUARDANDO_APROVACAO', // Alterado de ATIVA
        ]);

        // Cria a carteira principal para a nova conta
        $conta->carteiras()->create([
            'nome' => 'Principal',
            'saldo' => 0,
        ]);

        return redirect()->route('home')->with('success', 'Nova conta aberta com sucesso!');
    }

    /**
     * Mostra o extrato completo de uma conta.
     */
    public function extrato(ContaBancaria $conta)
    {
        // Verifica se a conta pertence ao usuário logado
        if ($conta->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $transacoes = $conta->transacoes()->latest()->paginate(15);

        return view('conta.extrato', compact('conta', 'transacoes'));
    }

    /**
     * Mostra o formulário para realizar um saque.
     */
    public function saqueForm(ContaBancaria $conta)
    {
        if ($conta->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('conta.saque', compact('conta'));
    }

    /**
     * Processa a operação de saque.
     */
    public function saque(Request $request, ContaBancaria $conta)
    {
        if ($conta->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'valor' => ['required', 'numeric', 'min:0.01', 'max:' . $conta->saldo],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $conta) {
            // Assumimos que a operação é na carteira principal
            $carteira = $conta->carteiras()->first();

            // Atualiza o saldo da carteira
            $carteira->saldo -= $request->input('valor');
            $carteira->save();

            // Cria o registro da transação
            $conta->transacoes()->create([
                'tipo' => 'SAQUE',
                'valor' => $request->input('valor'),
                'descricao' => $request->input('descricao') ?? 'Saque realizado',
            ]);
        });

        return redirect()->route('home')->with('success', 'Saque realizado com sucesso!');
    }

    /**
     * Mostra o formulário para realizar uma transferência.
     */
    public function transferenciaForm(ContaBancaria $conta)
    {
        if ($conta->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('conta.transferencia', compact('conta'));
    }

    /**
     * Processa a operação de transferência.
     */
    public function transferencia(Request $request, ContaBancaria $contaOrigem)
    {
        if ($contaOrigem->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'agencia_destino' => ['required', 'string'],
            'conta_destino' => ['required', 'string'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:' . $contaOrigem->saldo],
        ]);

        $valor = $request->input('valor');

        $contaDestino = ContaBancaria::where('agencia', $request->input('agencia_destino'))
                                     ->where('numero', $request->input('conta_destino'))
                                     ->first();

        if (!$contaDestino) {
            return back()->with('error', 'Conta de destino não encontrada.');
        }

        if ($contaDestino->id === $contaOrigem->id) {
            return back()->with('error', 'Você não pode transferir para a mesma conta.');
        }

        DB::transaction(function () use ($contaOrigem, $contaDestino, $valor) {
            $carteiraOrigem = $contaOrigem->carteiras()->first();
            $carteiraDestino = $contaDestino->carteiras()->first();

            // Debita da conta de origem
            $carteiraOrigem->saldo -= $valor;
            $carteiraOrigem->save();
            $contaOrigem->transacoes()->create([
                'tipo' => 'TRANSFERENCIA_ENVIADA',
                'valor' => $valor,
                'descricao' => 'Transferência para conta ' . $contaDestino->numero,
            ]);

            // Credita na conta de destino
            $carteiraDestino->saldo += $valor;
            $carteiraDestino->save();
            $contaDestino->transacoes()->create([
                'tipo' => 'TRANSFERENCIA_RECEBIDA',
                'valor' => $valor,
                'descricao' => 'Transferência recebida da conta ' . $contaOrigem->numero,
            ]);
        });

        return redirect()->route('home')->with('success', 'Transferência realizada com sucesso!');
    }
}
