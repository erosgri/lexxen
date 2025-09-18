<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PessoaJuridica extends Model
{
    use HasFactory;

    protected $table = 'pessoa_juridica';

    protected $fillable = [
        'user_id',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'telefone',
        'celular',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'representante_legal',
        'cpf_representante',
    ];

    /**
     * Get the user that owns the PessoaJuridica
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formata o CNPJ para exibição
     */
    public function getCnpjFormatadoAttribute()
    {
        $cnpj = $this->cnpj;
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
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
     * Formata o CPF do representante para exibição
     */
    public function getCpfRepresentanteFormatadoAttribute()
    {
        $cpf = $this->cpf_representante;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    /**
     * Get all of the user's wallets.
     */
    public function carteiras()
    {
        return $this->morphMany(Carteira::class, 'owner');
    }
}
