<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtratoResource extends JsonResource
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
            'carteira_id' => $this->carteira_id,
            'carteira_name' => $this->carteira->name ?? null,
            'tipo_operacao' => $this->tipo_operacao,
            'tipo_operacao_formatted' => $this->tipo_operacao_formatado,
            'valor' => $this->valor,
            'valor_formatted' => $this->valor_formatado,
            'saldo_apos_operacao' => $this->saldo_apos_operacao,
            'saldo_apos_operacao_formatted' => $this->saldo_formatado,
            'conta_origem' => $this->conta_origem,
            'conta_destino' => $this->conta_destino,
            'descricao' => $this->descricao,
            'data_operacao' => $this->data_operacao,
            'data_operacao_formatted' => $this->data_formatada,
            'is_entrada' => $this->isEntrada(),
            'is_saida' => $this->isSaida(),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i:s'),
        ];
    }
}









