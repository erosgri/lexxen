<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AprovarUsuariosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $acao; // 'aprovar' ou 'reprovar'
    protected $motivo;

    /**
     * Create a new job instance.
     */
    public function __construct(array $userIds, string $acao, string $motivo = null)
    {
        $this->userIds = $userIds;
        $this->acao = $acao;
        $this->motivo = $motivo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::whereIn('id', $this->userIds)->get();
        
        foreach ($users as $user) {
            if ($this->acao === 'aprovar') {
                $user->update([
                    'status_aprovacao' => 'aprovado',
                    'aprovado_em' => now(),
                    'motivo_reprovacao' => null,
                ]);
                
                Log::info("Usuário {$user->email} aprovado com sucesso");
            } elseif ($this->acao === 'reprovar') {
                $user->update([
                    'status_aprovacao' => 'reprovado',
                    'motivo_reprovacao' => $this->motivo,
                ]);
                
                Log::info("Usuário {$user->email} reprovado. Motivo: {$this->motivo}");
            }
        }
    }
}