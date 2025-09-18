<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\ExtratoService;
use App\DTOs\ExtratoFilterDTO;
use Illuminate\Support\Carbon;

class TestExtratoFilterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-extrato-filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa as regras de filtragem e paginação do extrato.';

    /**
     * Execute the console command.
     */
    public function handle(ExtratoService $extratoService)
    {
        $this->info('🧪 Iniciando teste de filtro e paginação do extrato...');

        // 1. Encontrar o usuário de teste
        $userA = User::where('email', 'jean.paes@example.com')->first();
        if (!$userA) {
            $this->error('Usuário "Jean Molina Paes" não encontrado. Rode o seeder `app:ensure-test-users` primeiro.');
            return 1;
        }
        $this->line("✅ Usuário de teste encontrado: {$userA->name}");

        // 2. Definir filtros
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);
        $filters = new ExtratoFilterDTO(
            carteiraId: null, // Todas as carteiras do usuário
            dataInicial: $startDate->format('Y-m-d'),
            dataFinal: $endDate->format('Y-m-d'),
            perPage: 10
        );
        $this->line("📅 Filtrando extratos de {$startDate->format('d/m/Y')} a {$endDate->format('d/m/Y')}");
        $this->line("📄 Páginação: {$filters->perPage} itens por página.");

        // 3. Chamar o serviço
        $paginatedExtratos = $extratoService->getExtratosByUser($userA->id, $filters);

        // 4. Validar e exibir resultados
        if ($paginatedExtratos->isEmpty()) {
            $this->warn('⚠️ Nenhum extrato encontrado para o período especificado.');
            return 0;
        }

        $this->info("📊 Resultados da Página 1 (Total: {$paginatedExtratos->total()})");
        $this->table(
            ['ID Extrato', 'Data', 'Tipo', 'Valor', 'Descrição'],
            $paginatedExtratos->map(function ($extrato) {
                return [
                    $extrato->id,
                    Carbon::parse($extrato->data_operacao)->format('d/m/Y H:i'),
                    $extrato->tipo_operacao,
                    'R$ ' . number_format($extrato->valor, 2, ',', '.'),
                    $extrato->descricao,
                ];
            })
        );

        // Validações
        $isSorted = true;
        $previousDate = null;
        $outOfRange = false;

        foreach ($paginatedExtratos->items() as $extrato) {
            $currentDate = Carbon::parse($extrato->data_operacao);
            if ($previousDate && $currentDate->isAfter($previousDate)) {
                $isSorted = false;
            }
            if (!$currentDate->between($startDate->startOfDay(), $endDate->endOfDay())) {
                $outOfRange = true;
            }
            $previousDate = $currentDate;
        }

        if ($isSorted) {
            $this->info('✅ Validação de Ordenação: OK! Extratos estão em ordem decrescente.');
        } else {
            $this->error('❌ Validação de Ordenação: FALHOU! Extratos não estão em ordem decrescente.');
        }

        if (!$outOfRange) {
            $this->info('✅ Validação de Filtro de Data: OK! Todos os extratos estão dentro do período de 7 dias.');
        } else {
            $this->error('❌ Validação de Filtro de Data: FALHOU! Encontrado extrato fora do período.');
        }

        if ($paginatedExtratos->count() <= $filters->perPage) {
            $this->info("✅ Validação de Paginação: OK! Retornou {$paginatedExtratos->count()} itens (limite de {$filters->perPage}).");
        } else {
            $this->error("❌ Validação de Paginação: FALHOU! Retornou mais itens que o limite por página.");
        }

        $this->info('🎉 Teste concluído!');
        return 0;
    }
}
