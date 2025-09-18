<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'carteira_origem_id' => $this->carteira_origem_id,
            'carteira_origem_name' => $this->carteiraOrigem->name ?? null,
            'carteira_destino_id' => $this->carteira_destino_id,
            'carteira_destino_name' => $this->carteiraDestino->name ?? null,
            'valor' => $this->valor,
            'valor_formatted' => 'R$ ' . number_format($this->valor, 2, ',', '.'),
            'descricao' => $this->descricao,
            'status' => $this->status,
            'status_formatted' => $this->getStatusFormatted(),
            'idempotency_key' => $this->idempotency_key,
            'processed_at' => $this->processed_at,
            'processed_at_formatted' => $this->processed_at?->format('d/m/Y H:i:s'),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function getStatusFormatted(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'completed' => 'Concluída',
            'failed' => 'Falhou',
            default => 'Desconhecido'
        };
    }
}





