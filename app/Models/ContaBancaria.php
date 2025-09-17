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
        'numero',
        'agencia',
        'tipo_conta',
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
     * Relacionamento com Carteiras
     */
    public function carteiras()
    {
        return $this->hasMany(Carteira::class, 'conta_bancaria_id');
    }

    /**
     * Calcula o saldo da conta somando os saldos das carteiras.
     */
    public function getSaldoAttribute()
    {
        // O método 'sum' é otimizado e faz a soma diretamente no banco de dados.
        return $this->carteiras()->sum('saldo');
    }

    /**
     * Gera número de conta único
     */
    public static function gerarNumeroConta()
    {
        do {
            $numero = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $numero = $numero . '-' . rand(0, 9);
        } while (self::where('numero', $numero)->exists());

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
        return 'R$ 0,00'; // Limite foi removido, retornamos um valor padrão.
    }

    /**
     * Relacionamento com Transacoes
     */
    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'conta_id');
    }
}
