<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Carteira;
use App\Models\Transfer;
use App\Observers\TransferObserver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando o seeder de transferências de teste...');
        
        // Limpar tabelas para evitar duplicatas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('transfers')->truncate();
        DB::table('extratos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Tabelas de transferências e extratos limpas.');

        // Desativar observer para não criar extratos duplicados durante o seed
        Transfer::withoutEvents(function () {
            $faker = Faker::create();

            // Encontrar Usuário A (Jean Paes) e sua carteira principal
            $userA = User::where('email', 'jean.paes@example.com')->with('pessoaFisica.carteiras')->first();
            if (!$userA || !$userA->pessoaFisica || $userA->pessoaFisica->carteiras->isEmpty()) {
                $this->command->error('Usuário "Jean Molina Paes" ou sua carteira principal não encontrados. Rode o seeder específico primeiro.');
                return;
            }
            $carteiraA = $userA->pessoaFisica->carteiras->firstWhere('type', 'DEFAULT');

            // Encontrar Usuário B (Maria da Silva) e sua carteira principal
            $userB = User::where('email', 'maria.silva@example.com')->with('pessoaFisica.carteiras')->first();
            if (!$userB || !$userB->pessoaFisica || $userB->pessoaFisica->carteiras->isEmpty()) {
                $this->command->error('Usuário "Maria da Silva" ou sua carteira principal não encontrados. Rode o comando app:ensure-test-users primeiro.');
                return;
            }
            $carteiraB = $userB->pessoaFisica->carteiras->firstWhere('type', 'DEFAULT');

            if (!$carteiraA || !$carteiraB) {
                $this->command->error('Não foi possível encontrar as carteiras principais para os usuários de teste.');
                return;
            }

            $this->command->info("Usuário A: {$userA->name} (Carteira ID: {$carteiraA->id})");
            $this->command->info("Usuário B: {$userB->name} (Carteira ID: {$carteiraB->id})");

            DB::beginTransaction();
            try {
                for ($i = 0; $i < 50; $i++) {
                    $valor = $faker->randomFloat(2, 5, 150);
                    $data = Carbon::now()->subDays($faker->numberBetween(0, 30));

                    // Alternar a direção da transferência
                    if ($i % 2 == 0) {
                        // A -> B
                        $carteiraOrigem = $carteiraA;
                        $carteiraDestino = $carteiraB;
                    } else {
                        // B -> A
                        $carteiraOrigem = $carteiraB;
                        $carteiraDestino = $carteiraA;
                    }
                    
                    // Garantir saldo suficiente para a transação
                    $carteiraOrigem->balance += $valor;
                    $carteiraOrigem->save();

                    // Criar a transferência
                    $transfer = Transfer::create([
                        'carteira_origem_id' => $carteiraOrigem->id,
                        'carteira_destino_id' => $carteiraDestino->id,
                        'valor' => $valor,
                        'status' => 'completed',
                        'descricao' => 'Transferência de teste ' . ($i + 1),
                        'idempotency_key' => \Illuminate\Support\Str::uuid()->toString(), // Adiciona a chave única
                        'created_at' => $data,
                        'updated_at' => $data,
                        'processed_at' => $data,
                    ]);

                    // Simular a transação de saldo
                    $carteiraOrigem->balance -= $valor;
                    $carteiraDestino->balance += $valor;
                    $carteiraOrigem->save();
                    $carteiraDestino->save();

                    // Chamar o método protegido do observer manualmente para criar o extrato
                    $observer = new TransferObserver();
                    $reflection = new \ReflectionClass($observer);
                    $method = $reflection->getMethod('registrarExtratoTransferencia');
                    $method->setAccessible(true);
                    $method->invoke($observer, $transfer);
                }
                
                DB::commit();
                $this->command->info('50 transferências de teste criadas com sucesso!');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error('Ocorreu um erro ao criar as transferências: ' . $e->getMessage());
            }
        });
    }
}
