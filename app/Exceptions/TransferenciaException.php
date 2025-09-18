<?php

namespace App\Exceptions;

use Exception;

class TransferenciaException extends Exception
{
    public static function saldoInsuficiente(float $saldoAtual, float $valorSolicitado): self
    {
        return new self(
            "Saldo insuficiente. Saldo atual: R$ " . number_format($saldoAtual, 2, ',', '.') . 
            ", Valor solicitado: R$ " . number_format($valorSolicitado, 2, ',', '.')
        );
    }

    public static function contaBloqueada(string $userName): self
    {
        return new static("A conta do usuário '{$userName}' está bloqueada e não pode realizar ou receber transferências.");
    }

    public static function carteiraDesabilitada(string $nomeCarteira): self
    {
        return new self("A carteira '{$nomeCarteira}' está desabilitada e não pode transferir ou receber dinheiro.");
    }

    public static function carteiraDestinoDesabilitada(string $nomeCarteira): self
    {
        return new self("A carteira de destino '{$nomeCarteira}' está desabilitada e não pode receber dinheiro.");
    }

    public static function transferenciaDuplicada(string $idempotencyKey): self
    {
        return new self("Transferência duplicada detectada. Chave: {$idempotencyKey}");
    }

    public static function carteiraDefaultNaoEncontrada(int $userId): self
    {
        return new self("Carteira DEFAULT não encontrada para o usuário ID: {$userId}");
    }
}
