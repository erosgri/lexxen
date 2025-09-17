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
        'name',
        'balance',
        'type',
        'status',
        'approval_status',
    ];

    /**
     * Relacionamento com ContaBancaria
     */
    public function contaBancaria()
    {
        return $this->belongsTo(ContaBancaria::class, 'conta_bancaria_id');
    }
}
