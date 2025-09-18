<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Extrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'carteira_id',
        'tipo_operacao',
        'valor',
        'saldo_apos_operacao',
        'conta_origem',
        'conta_destino',
        'descricao',
        'data_operacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'saldo_apos_operacao' => 'decimal:2',
        'data_operacao' => 'datetime',
    ];

    public function carteira(): BelongsTo
    {
        return $this->belongsTo(Carteira::class);
    }

    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getSaldoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->saldo_apos_operacao, 2, ',', '.');
    }

    public function getDataFormatadaAttribute(): string
    {
        return $this->data_operacao->format('d/m/Y H:i:s');
    }

    public function getTipoOperacaoFormatadoAttribute(): string
    {
        return match($this->tipo_operacao) {
            'transferencia_origem' => 'Transferência Enviada',
            'transferencia_destino' => 'Transferência Recebida',
            'saque' => 'Saque',
            'deposito' => 'Depósito',
            'outros' => 'Outros',
            default => 'Desconhecido'
        };
    }

    public function isEntrada(): bool
    {
        return in_array($this->tipo_operacao, ['transferencia_destino', 'deposito']);
    }

    public function isSaida(): bool
    {
        return in_array($this->tipo_operacao, ['transferencia_origem', 'saque']);
    }
}
