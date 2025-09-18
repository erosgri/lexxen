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
        } while (self::where('numero', $numero)->exists());

        return $numero;
    }
}
