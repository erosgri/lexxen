<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\ContaBancaria;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CreateUserWithTwoAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user-with-two-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o usuário Sr. Jean Molina Paes com uma conta poupança e uma conta corrente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faker = Faker::create('pt_BR');

        $this->info('Verificando se o usuário Sr. Jean Molina Paes já existe...');
        $user = User::where('email', 'jean.paes@example.com')->first();

        if ($user) {
            $this->info('Usuário já existe. Apenas criando as contas bancárias.');
        } else {
            $this->info('Criando usuário PF: Sr. Jean Molina Paes');
            $user = User::create([
                'name' => 'Sr. Jean Molina Paes',
                'email' => 'jean.paes@example.com',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'pessoa_fisica',
                'approval_status' => 'approved',
            ]);

            PessoaFisica::create([
                'user_id' => $user->id,
                'nome_completo' => 'Sr. Jean Molina Paes',
                'cpf' => $faker->unique()->numerify('###########'),
                'rg' => $faker->unique()->numerify('#########'),
                'data_nascimento' => '1980-01-15',
                'telefone' => $faker->phoneNumber,
                'celular' => $faker->phoneNumber,
                'endereco' => $faker->streetName,
                'numero' => $faker->buildingNumber,
                'bairro' => $faker->city,
                'cidade' => $faker->city,
                'estado' => $faker->stateAbbr,
                'cep' => str_replace('-', '', $faker->postcode),
            ]);
            $this->info('Usuário PF criado com sucesso.');
        }
        
        // -- Criação da Conta Poupança --
        $this->info('Criando conta poupança...');
        ContaBancaria::create([
            'user_id' => $user->id,
            'numero' => $faker->unique()->numerify('#####-##'),
            'agencia' => $faker->numerify('####'),
            'tipo_conta' => 'poupanca',
            'status' => 'ATIVA',
        ]);
        $this->info('Conta poupança criada.');

        // -- Criação da Conta Corrente --
        $this->info('Criando conta corrente...');
        ContaBancaria::create([
            'user_id' => $user->id,
            'numero' => $faker->unique()->numerify('#####-##'),
            'agencia' => $faker->numerify('####'),
            'tipo_conta' => 'corrente',
            'status' => 'ATIVA',
        ]);
        $this->info('Conta corrente criada.');
        
        $this->info('Comando executado com sucesso!');
        return 0;
    }
}
