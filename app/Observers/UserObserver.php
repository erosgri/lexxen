<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ContaBancaria;
use App\Models\Carteira;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Verificar se o status_aprovacao mudou
        if ($user->wasChanged('status_aprovacao')) {
            $this->sincronizarStatusContas($user);
        }
    }

    /**
     * Sincroniza o status das contas bancárias e carteiras com o status do usuário
     */
    private function sincronizarStatusContas(User $user): void
    {
        $novoStatus = $user->status_aprovacao;
        
        // Obter todas as contas bancárias do usuário
        $contas = $user->contasBancarias;
        
        foreach ($contas as $conta) {
            $statusConta = $this->determinarStatusConta($novoStatus);
            $conta->update(['status' => $statusConta]);
            
            // Sincronizar carteiras associadas
            $this->sincronizarCarteiras($user, $statusConta);
        }
    }

    /**
     * Determina o status da conta baseado no status do usuário
     */
    private function determinarStatusConta(string $statusUsuario): string
    {
        return match($statusUsuario) {
            'aprovado' => 'ATIVA',
            'bloqueado', 'reprovado' => 'BLOQUEADA',
            'aguardando' => 'AGUARDANDO_APROVACAO',
            default => 'AGUARDANDO_APROVACAO'
        };
    }

    /**
     * Sincroniza as carteiras do usuário
     */
    private function sincronizarCarteiras(User $user, string $statusConta): void
    {
        $owner = $user->tipo_usuario === 'pessoa_fisica' 
            ? $user->pessoaFisica 
            : $user->pessoaJuridica;

        if (!$owner) {
            return;
        }

        $carteiras = $owner->carteiras;
        
        foreach ($carteiras as $carteira) {
            $statusCarteira = $this->determinarStatusCarteira($statusConta);
            $approvalStatus = $this->determinarApprovalStatus($user->status_aprovacao);
            
            $carteira->update([
                'status' => $statusCarteira,
                'approval_status' => $approvalStatus
            ]);
        }
    }

    /**
     * Determina o status da carteira baseado no status da conta
     */
    private function determinarStatusCarteira(string $statusConta): string
    {
        return match($statusConta) {
            'ATIVA' => 'ATIVA',
            'BLOQUEADA' => 'BLOQUEADA',
            'AGUARDANDO_APROVACAO' => 'AGUARDANDO_LIBERACAO',
            default => 'AGUARDANDO_LIBERACAO'
        };
    }

    /**
     * Determina o approval_status da carteira baseado no status do usuário
     */
    private function determinarApprovalStatus(string $statusUsuario): string
    {
        return match($statusUsuario) {
            'aprovado' => 'approved',
            'bloqueado' => 'blocked',
            'reprovado' => 'rejected',
            'aguardando' => 'pending',
            default => 'pending'
        };
    }
}