<?php

namespace App\DTOs;

class CarteiraDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?float $balance = 0.0,
        public readonly ?string $status = 'ATIVA',
        public readonly ?string $approvalStatus = 'pending',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            balance: (float) ($data['balance'] ?? 0.0),
            status: $data['status'] ?? 'ATIVA',
            approvalStatus: $data['approval_status'] ?? 'pending',
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'balance' => $this->balance,
            'status' => $this->status,
            'approval_status' => $this->approvalStatus,
        ];
    }

    public function isDefault(): bool
    {
        return $this->type === 'DEFAULT';
    }

    public function isWallet(): bool
    {
        return $this->type === 'WALLET';
    }

    public function isActive(): bool
    {
        return $this->status === 'ATIVA';
    }

    public function isApproved(): bool
    {
        return $this->approvalStatus === 'approved';
    }
}









