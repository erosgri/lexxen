<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carteira extends Model
{
    use HasFactory;

    protected $table = 'carteiras';

    protected $fillable = [
        'name',
        'balance',
        'type',
        'status',
        'approval_status',
    ];

    /**
     * Get the parent owner model (PessoaFisica or PessoaJuridica). atualizado
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
