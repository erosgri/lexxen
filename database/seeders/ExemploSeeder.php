<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Hash;

class ExemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário pessoa física
        $user1 = User::create([
            'name' => 'Grigolli Bank Admin',
            'email' => 'grigolli@bank.com',
            'password' => Hash::make('123456'),
            'tipo_usuario' => 'pessoa_fisica',
            'ativo' => true,
            'status_aprovacao' => 'aprovado',
            'aprovado_em' => now()
        ]);

        // Criar usuário pessoa jurídica
        $user2 = User::create([
            'name' => 'Empresa Exemplo',
            'email' => 'empresa@email.com',
            'password' => Hash::make('123456'),
            'tipo_usuario' => 'pessoa_juridica',
            'ativo' => true,
            'status_aprovacao' => 'aprovado',
            'aprovado_em' => now()
        ]);

        // Criar usuário aguardando aprovação
        $user3 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@email.com',
            'password' => Hash::make('123456'),
            'tipo_usuario' => 'pessoa_fisica',
            'ativo' => true,
            'status_aprovacao' => 'aguardando'
        ]);

        // Criar usuário reprovado
        $user4 = User::create([
            'name' => 'Empresa Teste',
            'email' => 'teste@email.com',
            'password' => Hash::make('123456'),
            'tipo_usuario' => 'pessoa_juridica',
            'ativo' => true,
            'status_aprovacao' => 'reprovado',
            'motivo_reprovacao' => 'Documentação incompleta'
        ]);

        // Criar pessoa física
        PessoaFisica::create([
            'user_id' => $user1->id,
            'nome_completo' => 'João Silva',
            'cpf' => '12345678900',
            'data_nascimento' => '1990-01-01',
            'sexo' => 'M',
            'telefone' => '1123456789',
            'celular' => '11987654321',
            'endereco' => 'Rua das Flores, 123',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234567'
        ]);

        // Criar pessoa jurídica
        PessoaJuridica::create([
            'user_id' => $user2->id,
            'razao_social' => 'Empresa Exemplo Ltda',
            'nome_fantasia' => 'Exemplo Corp',
            'cnpj' => '12345678000190',
            'endereco' => 'Av. Paulista, 1000',
            'numero' => '1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310100',
            'representante_legal' => 'João Silva',
            'cpf_representante' => '12345678900'
        ]);

        // Criar contas bancárias para usuários aprovados
        \App\Models\ContaBancaria::create([
            'user_id' => $user1->id,
            'numero_conta' => '123456-7',
            'agencia' => '0001',
            'tipo_conta' => 'corrente',
            'saldo' => 1500.00,
            'limite' => 2000.00,
            'status' => 'ativa'
        ]);

        \App\Models\ContaBancaria::create([
            'user_id' => $user1->id,
            'numero_conta' => '123456-8',
            'agencia' => '0001',
            'tipo_conta' => 'poupanca',
            'saldo' => 5000.00,
            'limite' => 0,
            'status' => 'ativa'
        ]);

        \App\Models\ContaBancaria::create([
            'user_id' => $user2->id,
            'numero_conta' => '789012-3',
            'agencia' => '0002',
            'tipo_conta' => 'corrente',
            'saldo' => 10000.00,
            'limite' => 5000.00,
            'status' => 'ativa'
        ]);
    }
}
