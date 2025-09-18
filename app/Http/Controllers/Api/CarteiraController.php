<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarteiraResource;
use App\Http\Requests\StoreCarteiraRequest;
use App\Services\CarteiraService;
use App\DTOs\CarteiraDTO;
use App\Models\Carteira;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CarteiraController extends Controller
{
    public function __construct(
        private CarteiraService $carteiraService
    ) {}

    /**
     * Listar carteiras do usuário
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $carteiras = $this->carteiraService->getCarteirasByUser($user->id);

        return CarteiraResource::collection($carteiras);
    }

    /**
     * Criar nova carteira
     */
    public function store(StoreCarteiraRequest $request): JsonResponse
    {
        Gate::authorize('create-carteira');

        $dto = CarteiraDTO::fromArray($request->validated());
        $carteira = $this->carteiraService->createCarteira($request->user(), $dto);

        return response()->json([
            'message' => 'Carteira criada com sucesso',
            'data' => new CarteiraResource($carteira),
        ], 201);
    }

    /**
     * Exibir carteira específica
     */
    public function show(Carteira $carteira): JsonResponse
    {
        Gate::authorize('access-carteira', $carteira);

        return response()->json([
            'data' => new CarteiraResource($carteira),
        ]);
    }

    /**
     * Atualizar carteira
     */
    public function update(Request $request, Carteira $carteira): JsonResponse
    {
        Gate::authorize('access-carteira', $carteira);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:ATIVA,DESATIVA',
        ]);

        $carteira->update($request->only(['name', 'status']));

        return response()->json([
            'message' => 'Carteira atualizada com sucesso',
            'data' => new CarteiraResource($carteira->fresh()),
        ]);
    }

    /**
     * Deletar carteira (soft delete)
     */
    public function destroy(Carteira $carteira): JsonResponse
    {
        Gate::authorize('access-carteira', $carteira);

        if ($carteira->type === 'DEFAULT') {
            return response()->json([
                'message' => 'Não é possível deletar a carteira principal',
            ], 422);
        }

        if ($carteira->balance > 0) {
            return response()->json([
                'message' => 'Não é possível deletar uma carteira com saldo',
            ], 422);
        }

        $carteira->delete();

        return response()->json([
            'message' => 'Carteira deletada com sucesso',
        ]);
    }

    /**
     * Restaurar carteira deletada
     */
    public function restore(int $id): JsonResponse
    {
        $carteira = Carteira::withTrashed()->findOrFail($id);
        
        Gate::authorize('access-carteira', $carteira);

        $carteira->restore();

        return response()->json([
            'message' => 'Carteira restaurada com sucesso',
            'data' => new CarteiraResource($carteira->fresh()),
        ]);
    }
}




