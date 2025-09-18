<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ContaBancaria;
use App\Models\Transacao;
use Faker\Factory as Faker;

class ContasTransacoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        
        // Pega todos os usuários que não são administradores
        $users = User::where('tipo_usuario', '!=', 'admin')->with(['pessoaFisica', 'pessoaJuridica'])->get();

        if ($users->isEmpty()) {
            $this->command->info('No non-admin users found to seed accounts and transactions.');
            return;
        }

        $this->command->info('Seeding accounts and transactions for ' . $users->count() . ' users...');
        $bar = $this->command->getOutput()->createProgressBar($users->count());

        foreach ($users as $user) {
            $owner = $user->tipo_usuario === 'pf' ? $user->pessoaFisica : $user->pessoaJuridica;

            if (!$owner) {
                continue;
            }
            
            // Cria a carteira principal com saldo inicial
            $carteira = $owner->carteiras()->create([
                'name' => 'Principal',
                'balance' => $faker->randomFloat(2, 100, 10000),
                'type' => 'DEFAULT',
                'status' => 'ATIVA',
                'approval_status' => 'approved',
            ]);

            // Cria transações aleatórias para a carteira
            $numeroDeTransacoes = rand(5, 15);
            for ($i = 0; $i < $numeroDeTransacoes; $i++) {
                Transacao::create([
                    'conta_id' => $carteira->id, // Usa o ID da carteira
                    'tipo' => $faker->randomElement(['credit', 'debit']),
                    'valor' => $faker->randomFloat(2, 10, 500),
                    'descricao' => $faker->sentence,
                    'created_at' => $faker->dateTimeThisYear(),
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\nSeeding completed.");
    }
}
