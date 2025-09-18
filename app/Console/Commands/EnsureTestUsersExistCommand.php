<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\ContaBancaria; // Adicionar o model
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EnsureTestUsersExistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-test-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Garante que os usuários de teste (Jean Paes e Maria da Silva) existam com suas carteiras.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando e criando usuários de teste...');

        $this->createUser([
            'name' => 'Sr. Jean Molina Paes',
            'email' => 'jean.paes@example.com',
        ]);

        $this->createUser([
            'name' => 'Sra. Maria da Silva',
            'email' => 'maria.silva@example.com',
        ]);

        $this->info('Usuários de teste verificados e/ou criados com sucesso!');
        return 0;
    }

    private function createUser(array $userData): void
    {
        $faker = Faker::create('pt_BR');

        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'password' => Hash::make('password'),
                'tipo_usuario' => 'pessoa_fisica',
                'status_aprovacao' => 'aprovado',
                'ativo' => true,
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->line(" - Usuário {$userData['name']} criado.");
        } else {
            $this->line(" - Usuário {$userData['name']} já existe.");
        }

        $pessoaFisica = PessoaFisica::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nome_completo' => $userData['name'],
                'cpf' => $faker->unique()->numerify('###########'),
                'data_nascimento' => $faker->date(),
                'endereco' => $faker->streetAddress,
                'numero' => $faker->buildingNumber,
                'bairro' => $faker->city,
                'cidade' => $faker->city,
                'estado' => $faker->stateAbbr,
                'cep' => str_replace('-', '', $faker->postcode),
            ]
        );

        if ($pessoaFisica->wasRecentlyCreated) {
            $this->line("   - Perfil Pessoa Física criado.");
        }

        // Garante que o usuário tenha uma conta bancária
        $contaBancaria = ContaBancaria::firstOrCreate(
            ['user_id' => $user->id, 'tipo_conta' => 'corrente'],
            [
                'numero' => $faker->unique()->numerify('#####-##'),
                'agencia' => $faker->numerify('####'),
                'status' => 'ATIVA',
            ]
        );

        if ($contaBancaria->wasRecentlyCreated) {
            $this->line("   - Conta Bancária criada.");
        }

        if ($pessoaFisica->carteiras()->where('type', 'DEFAULT')->doesntExist()) {
            $pessoaFisica->carteiras()->create([
                'name' => 'Principal',
                'balance' => 1000,
                'type' => 'DEFAULT',
                'status' => 'ATIVA',
                'approval_status' => 'approved',
            ]);
            $this->line("   - Carteira principal criada.");
        }
    }
}
