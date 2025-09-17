<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ContaBancaria;
use Faker\Factory as Faker;

class ZeroBalanceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $faker = Faker::create('pt_BR');

            // 1. Create User
            $user = User::create([
                'name' => 'Usuario Saldo Zero',
                'email' => 'zero@bank.com',
                'password' => Hash::make('12345678'),
                'tipo_usuario' => 'pessoa_fisica',
                'status_aprovacao' => 'aprovado',
            ]);

            // 2. Create PessoaFisica profile
            $user->pessoaFisica()->create([
                'nome_completo' => $user->name,
                'cpf' => $faker->unique()->cpf(false),
                'data_nascimento' => $faker->date(),
                'endereco' => 'N/A', 'numero' => 'N/A', 'bairro' => 'N/A', 'cidade' => 'N/A', 'estado' => 'NA', 'cep' => '00000000',
            ]);

            // 3. Create Bank Account
            $conta = $user->contasBancarias()->create([
                'numero' => ContaBancaria::gerarNumeroConta(),
                'agencia' => $faker->numerify('####'),
                'tipo_conta' => 'corrente',
                'status' => 'ATIVA',
            ]);

            // 4. Create Wallet with Zero Balance
            $conta->carteiras()->create([
                'nome' => 'Principal',
                'saldo' => 0,
            ]);
        });
        
        $this->command->info('Usuário zero@bank.com criado com sucesso.');
    }
}
