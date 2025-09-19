<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarteiraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => $this->balance,
            'balance_formatted' => 'R$ ' . number_format($this->balance, 2, ',', '.'),
            'type' => $this->type,
            'type_formatted' => $this->type === 'DEFAULT' ? 'Principal' : 'Carteira',
            'status' => $this->status,
            'status_formatted' => $this->getStatusFormatted(),
            'approval_status' => $this->approval_status,
            'approval_status_formatted' => $this->getApprovalStatusFormatted(),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function getStatusFormatted(): string
    {
        return match($this->status) {
            'ATIVA' => 'Ativa',
            'DESATIVA' => 'Desativada',
            'AGUARDANDO_LIBERACAO' => 'Aguardando Liberação',
            default => 'Desconhecido'
        };
    }

    private function getApprovalStatusFormatted(): string
    {
        return match($this->approval_status) {
            'approved' => 'Aprovada',
            'pending' => 'Pendente',
            'rejected' => 'Rejeitada',
            default => 'Desconhecido'
        };
    }
}









