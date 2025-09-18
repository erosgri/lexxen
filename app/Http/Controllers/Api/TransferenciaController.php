<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransferenciaResource; // Corrigido para o novo resource
use App\Services\TransferenciaService;
use App\Http\Requests\Api\CriarTransferenciaRequest; // Usando o novo FormRequest
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TransferenciaController extends Controller
{
    public function __construct(
        private TransferenciaService $transferenciaService
    ) {}

    /**
     * Listar transferências do usuário autenticado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('view-extrato'); // Gate mais genérico para visualização

        $user = $request->user();
        $owner = $user->tipo_usuario === 'pessoa_fisica' 
            ? $user->pessoaFisica 
            : $user->pessoaJuridica;

        if (!$owner) {
            return TransferenciaResource::collection(collect());
        }

        $carteirasIds = $owner->carteiras->pluck('id');
        
        $transfers = Transfer::with(['carteiraOrigem', 'carteiraDestino'])
            ->where(function ($query) use ($carteirasIds) {
                $query->whereIn('carteira_origem_id', $carteirasIds)
                      ->orWhereIn('carteira_destino_id', $carteirasIds);
            })
            ->latest() // Mais limpo que orderBy
            ->paginate(15);

        return TransferenciaResource::collection($transfers);
    }

    /**
     * Criar uma nova transferência.
     */
    public function store(CriarTransferenciaRequest $request): JsonResponse
    {
        $transferenciaDTO = $request->toDTO();
        $resultado = [];

        if ($transferenciaDTO->tipo === 'entre_carteiras') {
            $resultado = $this->transferenciaService->processarTransferenciaEntreCarteiras($transferenciaDTO->toArray());
        } else {
            $resultado = $this->transferenciaService->processarTransferenciaParaOutros($transferenciaDTO->toArray());
        }
        
        return response()->json($resultado, $resultado['success'] ? 202 : 422); // 202 Accepted
    }

    /**
     * Exibir uma transferência específica.
     */
    public function show(Transfer $transfer): TransferenciaResource
    {
        Gate::authorize('access-transfer', $transfer);

        $transfer->load(['carteiraOrigem', 'carteiraDestino']);

        return new TransferenciaResource($transfer);
    }

    /**
     * Buscar dados de uma conta de destino.
     */
    public function buscarConta(Request $request): JsonResponse
    {
        // A lógica pode ser mantida ou movida para um service mais genérico
        return response()->json(['message' => 'Endpoint a ser implementado ou movido.']);
    }

    /**
     * Verificar o status de uma transferência via chave de idempotência.
     */
    public function verificarStatus(Request $request): JsonResponse
    {
        // A lógica pode ser mantida ou movida para um service mais genérico
        return response()->json(['message' => 'Endpoint a ser implementado ou movido.']);
    }

    /**
     * Buscar dados do beneficiário por agência e conta.
     */
    public function buscarBeneficiario(string $agencia, string $conta): JsonResponse
    {
        try {
            // Buscar conta de destino (tolerante a formatos com/sem hífen e espaços)
            $agenciaInformada = trim($agencia);
            $numeroInformadoOriginal = trim($conta);
            $numeroInformadoDigits = preg_replace('/\D+/', '', $numeroInformadoOriginal);

            $contaDestino = \App\Models\ContaBancaria::where('agencia', $agenciaInformada)
                ->where(function ($q) use ($numeroInformadoOriginal, $numeroInformadoDigits) {
                    $q->where('numero', $numeroInformadoOriginal)
                      ->orWhereRaw("REPLACE(numero, '-', '') = ?", [$numeroInformadoDigits]);
                })
                ->where('status', 'ATIVA')
                ->first();

            if (!$contaDestino) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conta não encontrada ou inativa'
                ], 404);
            }

            // Buscar dados do usuário proprietário da conta
            $user = $contaDestino->user;
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário proprietário da conta não encontrado'
                ], 404);
            }

            // Buscar dados do proprietário (PessoaFisica ou PessoaJuridica)
            $owner = null;
            $nomeCompleto = '';
            $tipoPessoa = '';

            if ($user->tipo_usuario === 'pessoa_fisica' && $user->pessoaFisica) {
                $owner = $user->pessoaFisica;
                $nomeCompleto = $owner->nome_completo;
                $tipoPessoa = 'Pessoa Física';
            } elseif ($user->tipo_usuario === 'pessoa_juridica' && $user->pessoaJuridica) {
                $owner = $user->pessoaJuridica;
                $nomeCompleto = $owner->razao_social;
                $tipoPessoa = 'Pessoa Jurídica';
            }

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados do proprietário não encontrados'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'beneficiario' => [
                    'nome' => $nomeCompleto,
                    'tipo_pessoa' => $tipoPessoa,
                    'agencia' => $contaDestino->agencia,
                    'conta' => $contaDestino->numero,
                    'banco' => 'Grigolli Bank' // Pode ser dinâmico no futuro
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
