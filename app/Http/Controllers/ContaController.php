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
        $user = Auth::user();
        
        // Verificar se o usuário está aprovado
        if (!$user->isAprovado()) {
            return redirect()->route('home')->with('error', 'Apenas usuários aprovados podem abrir novas carteiras.');
        }
        
        return view('conta.abrir');
    }

    /**
     * Processa a criação de uma nova conta bancária.
     */
    public function abrirConta(Request $request)
    {
        $request->validate([
            'tipo_conta' => ['required', 'in:corrente,poupanca,empresarial'],
        ]);

        $user = Auth::user();
        
        // Verificar se o usuário está aprovado
        if (!$user->isAprovado()) {
            return redirect()->route('home')->with('error', 'Apenas usuários aprovados podem abrir novas carteiras.');
        }

        $faker = Faker::create('pt_BR');

        // Determina o tipo de conta
        $tipoConta = $request->input('tipo_conta');

        $conta = ContaBancaria::create([
            'user_id' => $user->id,
            'numero' => ContaBancaria::gerarNumeroConta(),
            'agencia' => $faker->numerify('####'),
            'tipo_conta' => $tipoConta,
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
            $tipoContaFormatado = ucfirst($tipoConta);
            $nomeCarteira = 'Principal - ' . $tipoContaFormatado;
            
            $owner->carteiras()->create([
                'name' => $nomeCarteira,
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
    public function transferencia(Request $request, $id)
    {
        \Log::info('Transferência iniciada', [
            'carteira_id' => $id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $user = Auth::user();
        $owner = null;
        if ($user->tipo_usuario === 'pessoa_fisica') {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica') {
            $owner = $user->pessoaJuridica;
        }

        if (!$owner) {
            \Log::error('Perfil não encontrado', ['user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Perfil não encontrado.'], 400);
        }

        // Busca a carteira pelo ID
        $carteiraOrigem = $owner->carteiras()->where('id', $id)->first();
        
        if (!$carteiraOrigem) {
            \Log::error('Carteira não encontrada', ['carteira_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Carteira não encontrada.'], 400);
        }

        try {
            $request->validate([
                'agencia_destino' => ['required', 'string'],
                'conta_destino' => ['required', 'string'],
                'valor' => ['required', 'numeric', 'min:0.01', 'max:' . $carteiraOrigem->balance],
                'descricao' => ['nullable', 'string', 'max:255'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Dados inválidos', 'errors' => $e->errors()], 422);
        }

        $valor = $request->input('valor');
        $descricao = $request->input('descricao', '');

        $agencia = $request->input('agencia_destino');
        $conta = $request->input('conta_destino');
        $contaSemHifen = str_replace('-', '', $conta);
        
        $contaDestino = ContaBancaria::where('agencia', $agencia)
                                     ->where(function ($query) use ($conta, $contaSemHifen) {
                                         $query->where('numero', $conta)
                                               ->orWhere('numero', $contaSemHifen)
                                               ->orWhereRaw("REPLACE(numero, '-', '') = ?", [$contaSemHifen]);
                                     })
                                     ->first();

        if (!$contaDestino) {
            \Log::error('Conta de destino não encontrada', [
                'agencia' => $agencia,
                'conta' => $conta,
                'conta_sem_hifen' => $contaSemHifen
            ]);
            return response()->json(['success' => false, 'message' => 'Conta de destino não encontrada.'], 400);
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
            \Log::error('Perfil de destino não encontrado', ['conta_destino_id' => $contaDestino->id]);
            return response()->json(['success' => false, 'message' => 'Perfil de destino não encontrado.'], 400);
        }

        // Busca a carteira específica da conta bancária de destino
        $carteiraDestino = $ownerDestino->carteiras()
            ->where('name', 'like', '%' . $contaDestino->agencia . '%')
            ->where('name', 'like', '%' . $contaDestino->numero . '%')
            ->first();
        
        // Se não encontrar carteira específica, cria uma nova carteira para esta conta bancária
        if (!$carteiraDestino) {
            $tipoContaFormatado = ucfirst($contaDestino->tipo_conta);
            $nomeCarteira = $tipoContaFormatado . ' - ' . $contaDestino->agencia . '/' . $contaDestino->numero;
            
            $carteiraDestino = $ownerDestino->carteiras()->create([
                'name' => $nomeCarteira,
                'balance' => 0,
                'type' => 'WALLET',
                'status' => 'ATIVA',
                'approval_status' => 'approved',
            ]);
            
            \Log::info('Carteira específica criada para conta bancária', [
                'conta_bancaria' => $contaDestino->agencia . '-' . $contaDestino->numero,
                'carteira_id' => $carteiraDestino->id,
                'carteira_name' => $carteiraDestino->name
            ]);
        }
        
        if (!$carteiraDestino) {
            \Log::error('Carteira de destino não encontrada', ['user_destino_id' => $userDestino->id]);
            return response()->json(['success' => false, 'message' => 'Carteira de destino não encontrada.'], 400);
        }

        try {
            DB::transaction(function () use ($carteiraOrigem, $carteiraDestino, $valor, $contaDestino, $descricao) {
                // Debita da carteira de origem
                $carteiraOrigem->balance -= $valor;
                $carteiraOrigem->save();
                $carteiraOrigem->transacoes()->create([
                    'tipo' => 'debit',
                    'valor' => $valor,
                    'descricao' => 'Transferência para conta ' . $contaDestino->numero . ($descricao ? ' - ' . $descricao : ''),
                ]);

                // Credita na carteira de destino
                $carteiraDestino->balance += $valor;
                $carteiraDestino->save();
                $carteiraDestino->transacoes()->create([
                    'tipo' => 'credit',
                    'valor' => $valor,
                    'descricao' => 'Transferência recebida da conta ' . $contaDestino->numero . ($descricao ? ' - ' . $descricao : ''),
                ]);

                // Limpar cache das carteiras
                $this->limparCacheTransferencia($carteiraOrigem, $carteiraDestino);
            });

            \Log::info('Transferência realizada com sucesso', [
                'carteira_origem_id' => $carteiraOrigem->id,
                'carteira_destino_id' => $carteiraDestino->id,
                'valor' => $valor
            ]);

            return response()->json(['success' => true, 'message' => 'Transferência realizada com sucesso!']);
        } catch (\Exception $e) {
            \Log::error('Erro ao processar transferência', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erro ao processar transferência: ' . $e->getMessage()], 500);
        }
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
