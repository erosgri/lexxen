<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carteira extends Model
{
    use HasFactory;

    protected $table = 'carteiras';

    protected $fillable = [
        'conta_bancaria_id',
        'nome',
        'saldo',
    ];

    /**
     * Relacionamento com ContaBancaria
     */
    public function contaBancaria()
    {
        return $this->belongsTo(ContaBancaria::class, 'conta_bancaria_id');
    }
}
