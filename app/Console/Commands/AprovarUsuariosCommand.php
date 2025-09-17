<?php

namespace App\Console\Commands;

use App\Jobs\AprovarUsuariosJob;
use App\Models\User;
use Illuminate\Console\Command;

class AprovarUsuariosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:aprovar 
                            {acao : aprovar ou reprovar}
                            {--ids=* : IDs dos usuários (separados por vírgula)}
                            {--status=aguardando : Status dos usuários para processar}
                            {--motivo= : Motivo da reprovação (obrigatório se acao=reprovar)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aprova ou reprova usuários em lote';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $acao = $this->argument('acao');
        $ids = $this->option('ids');
        $status = $this->option('status');
        $motivo = $this->option('motivo');

        if (!in_array($acao, ['aprovar', 'reprovar'])) {
            $this->error('Ação deve ser "aprovar" ou "reprovar"');
            return 1;
        }

        if ($acao === 'reprovar' && empty($motivo)) {
            $this->error('Motivo é obrigatório para reprovação');
            return 1;
        }

        $query = User::where('status_aprovacao', $status);

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('Nenhum usuário encontrado com os critérios especificados');
            return 0;
        }

        $this->info("Encontrados {$users->count()} usuário(s) para {$acao}");

        if ($this->confirm('Deseja continuar?')) {
            $userIds = $users->pluck('id')->toArray();
            
            AprovarUsuariosJob::dispatch($userIds, $acao, $motivo);
            
            $this->info("Job de {$acao}ção enviado para processamento!");
        }

        return 0;
    }
}