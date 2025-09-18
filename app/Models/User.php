<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario',
        'ativo',
        'status_aprovacao',
        'aprovado_em',
        'motivo_reprovacao',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
            'aprovado_em' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com PessoaFisica
     */
    public function pessoaFisica()
    {
        return $this->hasOne(PessoaFisica::class);
    }

    /**
     * Relacionamento com PessoaJuridica
     */
    public function pessoaJuridica()
    {
        return $this->hasOne(PessoaJuridica::class);
    }

    /**
     * Verifica se o usuário é pessoa física
     */
    public function isPessoaFisica()
    {
        return $this->tipo_usuario === 'pessoa_fisica';
    }

    /**
     * Verifica se o usuário é pessoa jurídica
     */
    public function isPessoaJuridica()
    {
        return $this->tipo_usuario === 'pessoa_juridica';
    }

    /**
     * Relacionamento com ContasBancarias
     */
    public function contasBancarias()
    {
        return $this->hasMany(ContaBancaria::class);
    }

    /**
     * Verifica se o usuário está aprovado
     */
    public function isAprovado()
    {
        return $this->status_aprovacao === 'aprovado';
    }

    /**
     * Verifica se o usuário está aguardando aprovação
     */
    public function isAguardandoAprovacao()
    {
        return $this->status_aprovacao === 'aguardando';
    }

    /**
     * Verifica se o usuário foi reprovado
     */
    public function isReprovado()
    {
        return $this->status_aprovacao === 'reprovado';
    }
}
