<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PessoaFisica extends Model
{
    use HasFactory;

    protected $table = 'pessoa_fisica';

    protected $fillable = [
        'user_id',
        'nome_completo',
        'cpf',
        'rg',
        'data_nascimento',
        'sexo',
        'telefone',
        'celular',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
        ];
    }

    /**
     * Relacionamento com User
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formata o CPF para exibição
     */
    public function getCpfFormatadoAttribute()
    {
        $cpf = $this->cpf;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    /**
     * Formata o CEP para exibição
     */
    public function getCepFormatadoAttribute()
    {
        $cep = $this->cep;
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }

    /**
     * Get all of the user's wallets.
     */
    public function carteiras()
    {
        return $this->morphMany(Carteira::class, 'owner');
    }
}
