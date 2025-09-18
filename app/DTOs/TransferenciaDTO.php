<?php

namespace App\DTOs;

class TransferenciaDTO
{
    public function __construct(
        public readonly string $tipo,
        public readonly int $carteiraOrigemId,
        public readonly float $valor,
        public readonly string $descricao,
        public readonly ?int $carteiraDestinoId = null,
        public readonly ?string $agenciaDestino = null,
        public readonly ?string $contaDestino = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tipo: $data['tipo'],
            carteiraOrigemId: $data['carteira_origem_id'],
            valor: (float) $data['valor'],
            descricao: $data['descricao'] ?? '',
            carteiraDestinoId: $data['carteira_destino_id'] ?? null,
            agenciaDestino: $data['agencia_destino'] ?? null,
            contaDestino: $data['conta_destino'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'carteira_origem_id' => $this->carteiraOrigemId,
            'carteira_destino_id' => $this->carteiraDestinoId,
            'valor' => $this->valor,
            'descricao' => $this->descricao,
            'agencia_destino' => $this->agenciaDestino,
            'conta_destino' => $this->contaDestino,
        ];
    }

    public function isTransferenciaEntreCarteiras(): bool
    {
        return $this->tipo === 'entre_carteiras';
    }

    public function isTransferenciaParaOutros(): bool
    {
        return $this->tipo === 'para_outros';
    }
}
