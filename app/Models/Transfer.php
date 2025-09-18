<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'carteira_origem_id',
        'carteira_destino_id',
        'valor',
        'descricao',
        'status',
        'idempotency_key',
        'processed_at',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function carteiraOrigem(): BelongsTo
    {
        return $this->belongsTo(Carteira::class, 'carteira_origem_id');
    }

    public function carteiraDestino(): BelongsTo
    {
        return $this->belongsTo(Carteira::class, 'carteira_destino_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
            'processed_at' => now(),
        ]);
    }
}
