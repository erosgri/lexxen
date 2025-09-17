<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Console\Command;

class VerificarUsuariosCommand extends Command
{
    protected $signature = 'usuarios:verificar';
    protected $description = 'Verifica e corrige usuários sem registros correspondentes';

    public function handle()
    {
        $this->info('Verificando usuários...');
        
        // Usuários sem Pessoa Física
        $usersSemPF = User::where('tipo_usuario', 'pessoa_fisica')
            ->whereDoesntHave('pessoaFisica')
            ->get();
            
        $this->info("Usuários sem Pessoa Física: {$usersSemPF->count()}");
        foreach ($usersSemPF as $user) {
            $this->line("  - ID: {$user->id} - {$user->name} - Status: {$user->status_aprovacao}");
            
            // Criar registro de Pessoa Física
            PessoaFisica::create([
                'user_id' => $user->id,
                'nome_completo' => $user->name,
                'cpf' => '00000000000', // CPF temporário
                'data_nascimento' => '1990-01-01',
                'sexo' => 'M',
                'telefone' => '0000000000',
                'endereco' => 'Endereço não informado',
                'numero' => '0',
                'bairro' => 'Bairro não informado',
                'cidade' => 'Cidade não informada',
                'estado' => 'XX',
                'cep' => '00000000'
            ]);
            $this->info("    ✓ Registro de Pessoa Física criado");
        }
        
        // Usuários sem Pessoa Jurídica
        $usersSemPJ = User::where('tipo_usuario', 'pessoa_juridica')
            ->whereDoesntHave('pessoaJuridica')
            ->get();
            
        $this->info("Usuários sem Pessoa Jurídica: {$usersSemPJ->count()}");
        foreach ($usersSemPJ as $user) {
            $this->line("  - ID: {$user->id} - {$user->name} - Status: {$user->status_aprovacao}");
            
            // Criar registro de Pessoa Jurídica
            PessoaJuridica::create([
                'user_id' => $user->id,
                'razao_social' => $user->name,
                'nome_fantasia' => $user->name,
                'cnpj' => '00000000000000', // CNPJ temporário
                'endereco' => 'Endereço não informado',
                'numero' => '0',
                'bairro' => 'Bairro não informado',
                'cidade' => 'Cidade não informada',
                'estado' => 'XX',
                'cep' => '00000000',
                'representante_legal' => 'Representante não informado',
                'cpf_representante' => '00000000000'
            ]);
            $this->info("    ✓ Registro de Pessoa Jurídica criado");
        }
        
        $this->info('Verificação concluída!');
    }
}