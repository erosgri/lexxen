<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExtratoResource;
use App\Services\ExtratoService;
use App\DTOs\ExtratoFilterDTO;
use App\Models\Carteira;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExtratoController extends Controller
{
    public function __construct(
        private ExtratoService $extratoService
    ) {}

    /**
     * Listar extratos do usuário
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('view-extrato');

        $filters = ExtratoFilterDTO::fromArray($request->all());
        $extratos = $this->extratoService->getExtratosByUser($request->user()->id, $filters);

        return ExtratoResource::collection($extratos);
    }

    /**
     * Listar extratos de uma carteira específica
     */
    public function carteira(Request $request, Carteira $carteira): AnonymousResourceCollection
    {
        Gate::authorize('access-carteira', $carteira);

        $filters = ExtratoFilterDTO::fromArray($request->all());
        $extratos = $this->extratoService->getExtratosByCarteira($carteira, $filters);

        return ExtratoResource::collection($extratos);
    }

    /**
     * Resumo financeiro do usuário
     */
    public function resumo(Request $request): JsonResponse
    {
        Gate::authorize('view-extrato');

        $filters = ExtratoFilterDTO::fromArray($request->all());
        $resumo = $this->extratoService->getResumoFinanceiro($request->user()->id, $filters);

        return response()->json([
            'data' => $resumo,
        ]);
    }

    /**
     * Resumo financeiro de uma carteira específica
     */
    public function resumoCarteira(Request $request, Carteira $carteira): JsonResponse
    {
        Gate::authorize('access-carteira', $carteira);

        $filters = ExtratoFilterDTO::fromArray($request->all());
        $resumo = $this->extratoService->getResumoCarteira($carteira, $filters);

        return response()->json([
            'data' => $resumo,
        ]);
    }

    /**
     * Limpar cache do extrato
     */
    public function clearCache(Request $request): JsonResponse
    {
        $this->extratoService->clearCache($request->user()->id);

        return response()->json([
            'message' => 'Cache do extrato limpo com sucesso',
        ]);
    }
}




