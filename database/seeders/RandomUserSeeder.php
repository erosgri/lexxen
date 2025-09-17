<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Faker\Factory as Faker;

class RandomUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $count = 20;

        $this->command->info("Creating {$count} random users...");

        for ($i = 0; $i < $count; $i++) {
            DB::transaction(function () use ($faker) {
                $tipo = $faker->randomElement(['pessoa_fisica', 'pessoa_juridica']);
                
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('12345678'),
                    'tipo_usuario' => $tipo,
                    'status_aprovacao' => 'aprovado',
                ]);

                if ($tipo === 'pessoa_fisica') {
                    $user->pessoaFisica()->create([
                        'nome_completo' => $user->name,
                        'cpf' => $faker->unique()->cpf(false),
                        'rg' => $faker->rg(false),
                        'data_nascimento' => $faker->date(),
                        'endereco' => $faker->streetName,
                        'numero' => $faker->buildingNumber,
                        'complemento' => $faker->secondaryAddress,
                        'bairro' => $faker->city,
                        'cidade' => $faker->city,
                        'estado' => $faker->stateAbbr,
                        'cep' => preg_replace('/\D/', '', $faker->postcode),
                    ]);
                } else { // pessoa_juridica
                    $user->pessoaJuridica()->create([
                        'razao_social' => $faker->company,
                        'nome_fantasia' => $faker->company,
                        'cnpj' => $faker->unique()->cnpj(false),
                        'representante_legal' => $faker->name,
                        'cpf_representante' => $faker->cpf(false),
                        'endereco' => $faker->streetName,
                        'numero' => $faker->buildingNumber,
                        'complemento' => $faker->secondaryAddress,
                        'bairro' => $faker->city,
                        'cidade' => $faker->city,
                        'estado' => $faker->stateAbbr,
                        'cep' => preg_replace('/\D/', '', $faker->postcode),
                    ]);
                }
            });
        }
    }
}
