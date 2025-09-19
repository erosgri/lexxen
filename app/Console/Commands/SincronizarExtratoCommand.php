<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transacao;
use App\Models\Extrato;
use App\Models\Carteira;

class SincronizarExtratoCommand extends Command
{
    protected $signature = 'extrato:sincronizar';
    protected $description = 'Sincroniza transações existentes com o extrato';

    public function handle()
    {
        $this->info('Iniciando sincronização do extrato...');
        
        $transacoes = Transacao::all();
        $criadas = 0;
        $erros = 0;
        
        foreach ($transacoes as $transacao) {
            try {
                // Verificar se já existe no extrato
                $extratoExistente = Extrato::where('carteira_id', $transacao->conta_id)
                    ->where('data_operacao', $transacao->created_at)
                    ->where('valor', $transacao->valor)
                    ->first();
                
                if ($extratoExistente) {
                    continue; // Já existe, pular
                }
                
                // Determinar tipo de operação
                $tipoOperacao = $this->determinarTipoOperacao($transacao);
                
                // Buscar carteira
                $carteira = Carteira::find($transacao->conta_id);
                
                // Criar registro no extrato
                Extrato::create([
                    'carteira_id' => $transacao->conta_id,
                    'tipo_operacao' => $tipoOperacao,
                    'valor' => $transacao->valor,
                    'saldo_apos_operacao' => $carteira ? $carteira->balance : 0,
                    'conta_origem' => $transacao->tipo === 'debit' ? ($carteira ? $carteira->name : null) : null,
                    'conta_destino' => $transacao->tipo === 'credit' ? ($carteira ? $carteira->name : null) : null,
                    'descricao' => $transacao->descricao,
                    'data_operacao' => $transacao->created_at,
                ]);
                
                $criadas++;
                
            } catch (\Exception $e) {
                $erros++;
                $this->error("Erro ao sincronizar transação {$transacao->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Sincronização concluída!");
        $this->info("Registros criados: {$criadas}");
        $this->info("Erros: {$erros}");
    }
    
    private function determinarTipoOperacao(Transacao $transacao): string
    {
        if ($transacao->tipo === 'credit') {
            if (str_contains($transacao->descricao, 'Transferência recebida')) {
                return 'transferencia_destino';
            }
            return 'deposito';
        } else {
            if (str_contains($transacao->descricao, 'Transferência para')) {
                return 'transferencia_origem';
            }
            return 'saque';
        }
    }
}
