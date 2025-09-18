<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carteira extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'carteiras';

    protected $fillable = [
        'name',
        'balance',
        'type',
        'status',
        'approval_status',
    ];

    /**
     * Get the parent owner model (PessoaFisica or PessoaJuridica). 
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Get the transacoes for the carteira.
     */
    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'conta_id');
    }

    public function extratos()
    {
        return $this->hasMany(Extrato::class);
    }

    /**
     * Obtém o saldo sempre atualizado (sem cache)
     */
    public function getSaldoAtualizadoAttribute(): float
    {
        return $this->fresh()->balance;
    }

    /**
     * Obtém o saldo formatado sempre atualizado
     */
    public function getSaldoFormatadoAtualizadoAttribute(): string
    {
        return 'R$ ' . number_format($this->saldo_atualizado, 2, ',', '.');
    }

    /**
     * Limpa todos os caches relacionados a esta carteira
     */
    public function limparCache(): void
    {
        if ($this->owner && method_exists($this->owner, 'user')) {
            $user = $this->owner->user ?? null;
            if ($user) {
                \Cache::forget("carteiras_user_{$user->id}");
                \Cache::forget("carteira_balance_{$this->id}");
                \Cache::tags(['extratos', 'resumos'])->flush();
            }
        }
    }
}
