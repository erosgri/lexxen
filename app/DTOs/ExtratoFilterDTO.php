<?php

namespace App\DTOs;

class ExtratoFilterDTO
{
    public function __construct(
        public readonly ?int $carteiraId = null,
        public readonly ?string $tipoOperacao = null,
        public readonly ?string $dataInicial = null,
        public readonly ?string $dataFinal = null,
        public readonly ?string $descricao = null,
        public readonly ?float $valorMinimo = null,
        public readonly ?float $valorMaximo = null,
        public readonly string $ordenacao = 'desc',
        public readonly int $perPage = 20,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            carteiraId: $data['carteira_id'] ?? null,
            tipoOperacao: $data['tipo_operacao'] ?? null,
            dataInicial: $data['data_inicial'] ?? null,
            dataFinal: $data['data_final'] ?? null,
            descricao: $data['descricao'] ?? null,
            valorMinimo: $data['valor_minimo'] ? (float) $data['valor_minimo'] : null,
            valorMaximo: $data['valor_maximo'] ? (float) $data['valor_maximo'] : null,
            ordenacao: $data['ordenacao'] ?? 'desc',
            perPage: (int) ($data['per_page'] ?? 20),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'carteira_id' => $this->carteiraId,
            'tipo_operacao' => $this->tipoOperacao,
            'data_inicial' => $this->dataInicial,
            'data_final' => $this->dataFinal,
            'descricao' => $this->descricao,
            'valor_minimo' => $this->valorMinimo,
            'valor_maximo' => $this->valorMaximo,
            'ordenacao' => $this->ordenacao,
            'per_page' => $this->perPage,
        ], fn($value) => $value !== null);
    }

    public function hasDateFilter(): bool
    {
        return !empty($this->dataInicial) || !empty($this->dataFinal);
    }

    public function hasValueFilter(): bool
    {
        return $this->valorMinimo !== null || $this->valorMaximo !== null;
    }

    public function hasTextFilter(): bool
    {
        return !empty($this->descricao);
    }
}





