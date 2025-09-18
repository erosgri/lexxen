<?php

namespace App\Services;

use App\Models\User;
use App\Models\Carteira;
use App\DTOs\CarteiraDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CarteiraService
{
    public function getCarteirasByUser(int $userId)
    {
        $cacheKey = "carteiras_user_{$userId}";
        
        return Cache::remember($cacheKey, 300, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return collect();
            }

            $owner = $user->tipo_usuario === 'pessoa_fisica' 
                ? $user->pessoaFisica 
                : $user->pessoaJuridica;

            if (!$owner) {
                return collect();
            }

            return $owner->carteiras()
                ->select('id', 'name', 'balance', 'type', 'status', 'approval_status', 'created_at', 'updated_at')
                ->where('status', 'ATIVA')
                ->where('approval_status', 'approved')
                ->orderBy('type')
                ->orderBy('created_at')
                ->get();
        });
    }

    public function createCarteira(User $user, CarteiraDTO $dto): Carteira
    {
        if ($dto->isDefault()) {
            throw new \Exception('Usuários não podem criar carteiras do tipo DEFAULT');
        }

        $owner = $user->tipo_usuario === 'pessoa_fisica' 
            ? $user->pessoaFisica 
            : $user->pessoaJuridica;

        if (!$owner) {
            throw new \Exception('Perfil do usuário não encontrado');
        }

        return DB::transaction(function () use ($owner, $dto) {
            $carteira = $owner->carteiras()->create([
                'name' => $dto->name,
                'type' => $dto->type,
                'balance' => $dto->balance,
                'status' => 'AGUARDANDO_LIBERACAO',
                'approval_status' => 'pending',
            ]);

            $this->clearUserCache($owner->user_id);
            
            return $carteira;
        });
    }

    public function updateCarteira(Carteira $carteira, array $data): Carteira
    {
        return DB::transaction(function () use ($carteira, $data) {
            $carteira->update($data);
            
            $this->clearUserCache($carteira->owner->user_id);
            
            return $carteira->fresh();
        });
    }

    public function deleteCarteira(Carteira $carteira): bool
    {
        if ($carteira->type === 'DEFAULT') {
            throw new \Exception('Não é possível deletar a carteira principal');
        }

        if ($carteira->balance > 0) {
            throw new \Exception('Não é possível deletar uma carteira com saldo');
        }

        return DB::transaction(function () use ($carteira) {
            $result = $carteira->delete();
            
            $this->clearUserCache($carteira->owner->user_id);
            
            return $result;
        });
    }

    public function restoreCarteira(int $carteiraId): Carteira
    {
        return DB::transaction(function () use ($carteiraId) {
            $carteira = Carteira::withTrashed()->findOrFail($carteiraId);
            $carteira->restore();
            
            $this->clearUserCache($carteira->owner->user_id);
            
            return $carteira->fresh();
        });
    }

    public function getCarteiraBalance(Carteira $carteira): float
    {
        $cacheKey = "carteira_balance_{$carteira->id}";
        
        return Cache::remember($cacheKey, 60, function () use ($carteira) {
            return $carteira->balance;
        });
    }

    public function updateCarteiraBalance(Carteira $carteira, float $newBalance): void
    {
        $carteira->update(['balance' => $newBalance]);
        
        Cache::forget("carteira_balance_{$carteira->id}");
        $this->clearUserCache($carteira->owner->user_id);
    }

    protected function clearUserCache(int $userId): void
    {
        Cache::forget("carteiras_user_{$userId}");
        Cache::forget("extratos_user_{$userId}_*");
        Cache::forget("resumo_user_{$userId}_*");
    }
}
