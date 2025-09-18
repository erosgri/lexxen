<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ContaBancaria;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
            'tipo_conta' => ['required', 'in:corrente_pf,poupanca_pf,corrente_pj,poupanca_pj'],
        ]);

        $faker = Faker::create('pt_BR');
        $user = Auth::user();

        // Determina o tipo de conta e o tipo de usuário baseado na seleção
        $tipoConta = $request->input('tipo_conta');
        $isPJ = str_ends_with($tipoConta, '_pj');
        $tipoContaBase = str_replace(['_pf', '_pj'], '', $tipoConta);

        $conta = ContaBancaria::create([
            'user_id' => $user->id,
            'numero' => ContaBancaria::gerarNumeroConta(),
            'agencia' => $faker->numerify('####'),
            'tipo_conta' => $tipoContaBase,
            'status' => 'AGUARDANDO_APROVACAO',
        ]);

        // Cria a carteira principal para o usuário
        $owner = null;
        if ($user->tipo_usuario === 'pessoa_fisica') {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica') {
            $owner = $user->pessoaJuridica;
        }

        if ($owner) {
            $tipoContaFormatado = ucfirst($tipoContaBase);
            $sufixo = $isPJ ? ' (PJ)' : ' (PF)';
            
            $owner->carteiras()->create([
                'name' => 'Principal - ' . $tipoContaFormatado . $sufixo,
                'balance' => 0,
                'type' => 'DEFAULT',
                'status' => 'AGUARDANDO_LIBERACAO',
                'approval_status' => 'pending', // Carteira aguarda aprovação da conta bancária
            ]);
        }

        return redirect()->route('home')->with('success', 'Solicitação de nova carteira enviada! Aguarde a aprovação do administrador.');
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

        // Busca transações através das carteiras do usuário
        $user = Auth::user();
        $transacoes = collect();
        
        if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
            $transacoes = $user->pessoaFisica->carteiras()
                ->with('transacoes')
                ->get()
                ->pluck('transacoes')
                ->flatten()
                ->sortByDesc('created_at');
        } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
            $transacoes = $user->pessoaJuridica->carteiras()
                ->with('transacoes')
                ->get()
                ->pluck('transacoes')
                ->flatten()
                ->sortByDesc('created_at');
        }

        return view('conta.extrato', compact('conta', 'transacoes'));
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

        // Busca a carteira principal do usuário
        $carteiraOrigem = $owner->carteiras()->where('type', 'DEFAULT')->first();
        
        if (!$carteiraOrigem) {
            return back()->with('error', 'Carteira não encontrada.');
        }

        $request->validate([
            'agencia_destino' => ['required', 'string'],
            'conta_destino' => ['required', 'string'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:' . $carteiraOrigem->balance],
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

        // Busca a carteira do usuário de destino
        $userDestino = $contaDestino->user;
        $ownerDestino = null;
        if ($userDestino->tipo_usuario === 'pessoa_fisica') {
            $ownerDestino = $userDestino->pessoaFisica;
        } elseif ($userDestino->tipo_usuario === 'pessoa_juridica') {
            $ownerDestino = $userDestino->pessoaJuridica;
        }

        if (!$ownerDestino) {
            return back()->with('error', 'Perfil de destino não encontrado.');
        }

        $carteiraDestino = $ownerDestino->carteiras()->where('type', 'DEFAULT')->first();
        
        if (!$carteiraDestino) {
            return back()->with('error', 'Carteira de destino não encontrada.');
        }

        DB::transaction(function () use ($carteiraOrigem, $carteiraDestino, $valor, $contaDestino, $contaOrigem) {
            // Debita da carteira de origem
            $carteiraOrigem->balance -= $valor;
            $carteiraOrigem->save();
            $carteiraOrigem->transacoes()->create([
                'tipo' => 'debit',
                'valor' => $valor,
                'descricao' => 'Transferência para conta ' . $contaDestino->numero,
            ]);

            // Credita na carteira de destino
            $carteiraDestino->balance += $valor;
            $carteiraDestino->save();
            $carteiraDestino->transacoes()->create([
                'tipo' => 'credit',
                'valor' => $valor,
                'descricao' => 'Transferência recebida da conta ' . $contaOrigem->numero,
            ]);

            // Limpar cache das carteiras
            $this->limparCacheTransferencia($carteiraOrigem, $carteiraDestino);
        });

        return redirect()->route('home')->with('success', 'Transferência realizada com sucesso!');
    }

    /**
     * Limpa o cache das carteiras após transferência
     */
    protected function limparCacheTransferencia($carteiraOrigem, $carteiraDestino)
    {
        $userIds = [];
        
        // Obter user_id da carteira de origem
        if ($carteiraOrigem->owner && $carteiraOrigem->owner->user) {
            $userIds[] = $carteiraOrigem->owner->user->id;
        }
        
        // Obter user_id da carteira de destino
        if ($carteiraDestino->owner && $carteiraDestino->owner->user) {
            $userIds[] = $carteiraDestino->owner->user->id;
        }
        
        // Limpar cache para ambos os usuários
        foreach (array_unique($userIds) as $userId) {
            \Cache::forget("carteiras_user_{$userId}");
            \Cache::forget("extratos_user_{$userId}_*");
            \Cache::forget("resumo_user_{$userId}_*");
            \Cache::forget("carteira_balance_{$carteiraOrigem->id}");
            \Cache::forget("carteira_balance_{$carteiraDestino->id}");
        }
    }
}
