<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaBancaria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas_bancarias';

    protected $fillable = [
        'user_id',
        'numero_conta',
        'agencia',
        'tipo_conta',
        'saldo',
        'limite',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'decimal:2',
            'limite' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gera número de conta único
     */
    public static function gerarNumeroConta()
    {
        do {
            $numero = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $numero = $numero . '-' . rand(0, 9);
        } while (self::where('numero_conta', $numero)->exists());

        return $numero;
    }

    /**
     * Formata o saldo para exibição
     */
    public function getSaldoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->saldo, 2, ',', '.');
    }

    /**
     * Formata o limite para exibição
     */
    public function getLimiteFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->limite, 2, ',', '.');
    }
}
