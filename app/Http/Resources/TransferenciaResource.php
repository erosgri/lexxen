<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferenciaResource extends JsonResource
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
            'valor' => (float) $this->valor,
            'status' => $this->status,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'carteira_origem' => new CarteiraResource($this->whenLoaded('carteiraOrigem')),
            'carteira_destino' => new CarteiraResource($this->whenLoaded('carteiraDestino')),
            'data_solicitacao' => $this->created_at->toIso8601String(),
            'data_processamento' => $this->processed_at ? $this->processed_at->toIso8601String() : null,
            'idempotency_key' => $this->idempotency_key,
        ];
    }
}
