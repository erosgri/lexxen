<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ContaBancaria;
use Faker\Factory as Faker;

class ContaBancariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Pega todos os usuários que não são administradores
        $users = User::where('tipo_usuario', '!=', 'admin')->get();

        if ($users->isEmpty()) {
            $this->command->info('No non-admin users found to seed bank accounts.');
            return;
        }

        $this->command->info('Seeding bank accounts for ' . $users->count() . ' users...');

        foreach ($users as $user) {
            ContaBancaria::create([
                'user_id' => $user->id,
                'numero' => $faker->unique()->numerify('#####-##'),
                'agencia' => $faker->numerify('####'),
                'tipo_conta' => $faker->randomElement(['corrente', 'poupanca']),
                'status' => 'ATIVA',
            ]);
        }

        $this->command->info('Bank account seeding completed.');
    }
}
