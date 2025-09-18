<?php

namespace App\Services;

use App\Models\Carteira;
use App\Models\Extrato;
use App\DTOs\ExtratoFilterDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExtratoService
{
    public function getExtratosByUser(int $userId, ExtratoFilterDTO $filters): LengthAwarePaginator
    {
        $cacheKey = "extratos_user_{$userId}_" . md5(serialize($filters->toArray()));
        
        return Cache::remember($cacheKey, 300, function () use ($userId, $filters) {
            $carteiras = $this->getUserCarteiras($userId);
            
            if ($carteiras->isEmpty()) {
                return new LengthAwarePaginator([], 0, $filters->perPage);
            }

            $query = Extrato::with(['carteira' => function ($query) {
                    $query->select('id', 'name', 'type', 'status');
                }])
                ->whereIn('carteira_id', $carteiras->pluck('id'));

            $this->applyFilters($query, $filters);

            return $query->paginate($filters->perPage);
        });
    }

    public function getExtratosByCarteira(Carteira $carteira, ExtratoFilterDTO $filters): LengthAwarePaginator
    {
        $cacheKey = "extratos_carteira_{$carteira->id}_" . md5(serialize($filters->toArray()));
        
        return Cache::remember($cacheKey, 300, function () use ($carteira, $filters) {
            $query = $carteira->extratos()
                ->select('id', 'carteira_id', 'tipo_operacao', 'valor', 'saldo_apos_operacao', 
                        'conta_origem', 'conta_destino', 'descricao', 'data_operacao', 'created_at');

            $this->applyFilters($query, $filters);

            return $query->paginate($filters->perPage);
        });
    }

    public function getResumoFinanceiro(int $userId, ExtratoFilterDTO $filters): array
    {
        $cacheKey = "resumo_user_{$userId}_" . md5(serialize($filters->toArray()));
        
        return Cache::remember($cacheKey, 300, function () use ($userId, $filters) {
            $carteiras = $this->getUserCarteiras($userId);
            
            if ($carteiras->isEmpty()) {
                return $this->getEmptyResumo();
            }

            $query = Extrato::whereIn('carteira_id', $carteiras->pluck('id'));
            $this->applyFilters($query, $filters);
            
            $extratos = $query->get();

            $totalCreditos = $extratos->whereIn('tipo_operacao', ['transferencia_destino', 'deposito'])->sum('valor');
            $totalDebitos = $extratos->whereIn('tipo_operacao', ['transferencia_origem', 'saque'])->sum('valor');
            $saldoAtual = $carteiras->sum('balance');
            $saldoInicial = $saldoAtual - $totalCreditos + $totalDebitos;

            return [
                'saldo_inicial' => $saldoInicial,
                'saldo_atual' => $saldoAtual,
                'total_creditos' => $totalCreditos,
                'total_debitos' => $totalDebitos,
                'saldo_periodo' => $totalCreditos - $totalDebitos,
                'quantidade_transacoes' => $extratos->count(),
            ];
        });
    }

    public function getResumoCarteira(Carteira $carteira, ExtratoFilterDTO $filters): array
    {
        $cacheKey = "resumo_carteira_{$carteira->id}_" . md5(serialize($filters->toArray()));
        
        return Cache::remember($cacheKey, 300, function () use ($carteira, $filters) {
            $query = $carteira->extratos();
            $this->applyFilters($query, $filters);
            
            $extratos = $query->get();

            $totalCreditos = $extratos->whereIn('tipo_operacao', ['transferencia_destino', 'deposito'])->sum('valor');
            $totalDebitos = $extratos->whereIn('tipo_operacao', ['transferencia_origem', 'saque'])->sum('valor');
            $saldoAtual = $carteira->balance;
            $saldoInicial = $saldoAtual - $totalCreditos + $totalDebitos;

            return [
                'saldo_inicial' => $saldoInicial,
                'saldo_atual' => $saldoAtual,
                'total_creditos' => $totalCreditos,
                'total_debitos' => $totalDebitos,
                'saldo_periodo' => $totalCreditos - $totalDebitos,
                'quantidade_transacoes' => $extratos->count(),
            ];
        });
    }

    protected function getUserCarteiras(int $userId)
    {
        $user = \App\Models\User::find($userId);
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
            ->where('status', 'ATIVA')
            ->where('approval_status', 'approved')
            ->get();
    }

    protected function applyFilters($query, ExtratoFilterDTO $filters): void
    {
        if ($filters->carteiraId) {
            $query->where('carteira_id', $filters->carteiraId);
        }

        if ($filters->tipoOperacao) {
            $query->where('tipo_operacao', $filters->tipoOperacao);
        }

        if ($filters->dataInicial) {
            $query->whereDate('data_operacao', '>=', $filters->dataInicial);
        }

        if ($filters->dataFinal) {
            $query->whereDate('data_operacao', '<=', $filters->dataFinal);
        }

        if ($filters->descricao) {
            $query->where('descricao', 'like', '%' . $filters->descricao . '%');
        }

        if ($filters->valorMinimo !== null) {
            $query->where('valor', '>=', $filters->valorMinimo);
        }

        if ($filters->valorMaximo !== null) {
            $query->where('valor', '<=', $filters->valorMaximo);
        }

        $query->orderBy('data_operacao', $filters->ordenacao);
    }

    protected function getEmptyResumo(): array
    {
        return [
            'saldo_inicial' => 0,
            'saldo_atual' => 0,
            'total_creditos' => 0,
            'total_debitos' => 0,
            'saldo_periodo' => 0,
            'quantidade_transacoes' => 0,
        ];
    }

    public function clearCache(int $userId): void
    {
        $carteiras = $this->getUserCarteiras($userId);
        
        foreach ($carteiras as $carteira) {
            Cache::forget("extratos_carteira_{$carteira->id}_*");
            Cache::forget("resumo_carteira_{$carteira->id}_*");
        }
        
        Cache::forget("extratos_user_{$userId}_*");
        Cache::forget("resumo_user_{$userId}_*");
    }
}
