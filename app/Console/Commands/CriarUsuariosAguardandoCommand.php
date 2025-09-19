<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use App\Models\ContaBancaria;
use App\Models\Carteira;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class CriarUsuariosAguardandoCommand extends Command
{
    protected $signature = 'usuarios:criar-aguardando {--count=20 : Número de usuários para criar}';
    protected $description = 'Cria usuários aguardando aprovação para teste';

    public function handle()
    {
        $count = $this->option('count');
        $faker = Faker::create('pt_BR');
        
        $this->info("Criando {$count} usuários aguardando aprovação...");
        
        $criados = 0;
        $erros = 0;
        
        for ($i = 0; $i < $count; $i++) {
            try {
                // Decidir aleatoriamente entre PF e PJ
                $tipoUsuario = $faker->randomElement(['pessoa_fisica', 'pessoa_juridica']);
                
                // Criar usuário
                $user = User::create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('password123'),
                    'tipo_usuario' => $tipoUsuario,
                    'status_aprovacao' => 'aguardando',
                    'ativo' => true,
                ]);
                
                if ($tipoUsuario === 'pessoa_fisica') {
                    // Criar pessoa física
                    $pessoaFisica = PessoaFisica::create([
                        'user_id' => $user->id,
                        'nome_completo' => $user->name,
                        'cpf' => $faker->cpf(false),
                        'data_nascimento' => $faker->date('Y-m-d', '2000-01-01'),
                        'telefone' => $faker->cellphoneNumber(),
                        'endereco' => $faker->streetAddress(),
                        'numero' => $faker->buildingNumber(),
                        'bairro' => $faker->citySuffix(),
                        'cidade' => $faker->city(),
                        'estado' => $faker->stateAbbr(),
                        'cep' => $faker->numerify('########'),
                    ]);
                    
                    // Criar conta bancária
                    $conta = ContaBancaria::create([
                        'user_id' => $user->id,
                        'numero' => ContaBancaria::gerarNumeroConta(),
                        'agencia' => $faker->numerify('####'),
                        'tipo_conta' => $faker->randomElement(['corrente', 'poupanca']),
                        'status' => 'AGUARDANDO_APROVACAO',
                    ]);
                    
                    // Criar carteira principal
                    $tipoContaFormatado = ucfirst($conta->tipo_conta);
                    $nomeCarteira = 'Principal - ' . $tipoContaFormatado;
                    
                    $carteira = $pessoaFisica->carteiras()->create([
                        'name' => $nomeCarteira,
                        'balance' => 0,
                        'type' => 'DEFAULT',
                        'status' => 'AGUARDANDO_LIBERACAO',
                        'approval_status' => 'pending',
                    ]);
                    
                } else {
                    // Criar pessoa jurídica
                    $pessoaJuridica = PessoaJuridica::create([
                        'user_id' => $user->id,
                        'razao_social' => $faker->company(),
                        'nome_fantasia' => $faker->companySuffix() . ' ' . $faker->word(),
                        'cnpj' => $faker->cnpj(false),
                        'inscricao_estadual' => $faker->numerify('###.###.###.###'),
                        'telefone' => $faker->cellphoneNumber(),
                        'endereco' => $faker->streetAddress(),
                        'numero' => $faker->buildingNumber(),
                        'bairro' => $faker->citySuffix(),
                        'cidade' => $faker->city(),
                        'estado' => $faker->stateAbbr(),
                        'cep' => $faker->numerify('########'),
                        'representante_legal' => $faker->name(),
                        'cpf_representante' => $faker->cpf(false),
                    ]);
                    
                    // Criar conta bancária
                    $conta = ContaBancaria::create([
                        'user_id' => $user->id,
                        'numero' => ContaBancaria::gerarNumeroConta(),
                        'agencia' => $faker->numerify('####'),
                        'tipo_conta' => $faker->randomElement(['corrente', 'poupanca', 'empresarial']),
                        'status' => 'AGUARDANDO_APROVACAO',
                    ]);
                    
                    // Criar carteira principal
                    $tipoContaFormatado = ucfirst($conta->tipo_conta);
                    $nomeCarteira = 'Principal - ' . $tipoContaFormatado;
                    
                    $carteira = $pessoaJuridica->carteiras()->create([
                        'name' => $nomeCarteira,
                        'balance' => 0,
                        'type' => 'DEFAULT',
                        'status' => 'AGUARDANDO_LIBERACAO',
                        'approval_status' => 'pending',
                    ]);
                }
                
                $criados++;
                $this->line("✅ Usuário {$criados}: {$user->name} ({$tipoUsuario}) - {$user->email}");
                
            } catch (\Exception $e) {
                $erros++;
                $this->error("❌ Erro ao criar usuário: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("🎉 Criação concluída!");
        $this->info("✅ Usuários criados: {$criados}");
        $this->info("❌ Erros: {$erros}");
        $this->info("📊 Total de usuários aguardando aprovação: " . User::where('status_aprovacao', 'aguardando')->count());
    }
}
