@extends('layouts.app')

@section('title', 'Minha Carteira - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel do Cliente</h2>
        <a href="{{ route('conta.abrir.form') }}" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i> Abrir Nova Carteira</a>
    </div>

    @if($contas->isNotEmpty())
        <!-- Listagem de Contas -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><i class="fas fa-wallet me-2"></i>Minha Carteira</h4>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($contas as $conta)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                <h5 class="mb-1">Carteira {{ ucfirst($conta->tipo_conta) }} - Nº {{ $conta->numero }}</h5>
                                <p class="mb-1">Agência: {{ $conta->agencia }}</p>
                            </div>
                            <div class="text-end">
                                @php
                                    // Apenas contas ATIVAS podem ter saldo real
                                    // Mas apenas se tiverem carteira específica (com agência/conta no nome)
                                    // Contas recém-ativadas sem carteira específica sempre começam com R$ 0,00
                                    if ($conta->status == 'ATIVA') {
                                        // Encontra a carteira correspondente usando agência e número da conta
                                        $identificadorConta = "{$conta->agencia}/{$conta->numero}";
                                        $carteiraDaConta = $carteiras->first(function($carteira) use ($identificadorConta) {
                                            return str_contains($carteira->name, $identificadorConta);
                                        });
                                        
                                        // Se encontrou carteira específica, mostra o saldo
                                        if ($carteiraDaConta) {
                                            $saldoExibido = $carteiraDaConta->saldo_formatado_atualizado ?? 'R$ 0,00';
                                        } else {
                                            // Se não tem carteira específica, sempre R$ 0,00
                                            // (não busca por tipo para evitar herdar saldo de outras carteiras)
                                            $saldoExibido = 'R$ 0,00';
                                        }
                                    } else {
                                        // Todas as contas não ativas sempre mostram R$ 0,00
                                        $saldoExibido = 'R$ 0,00';
                                    }
                                @endphp
                                <h5 class="fw-bold">Saldo: {{ $saldoExibido }}</h5>
                                @if ($conta->status == 'ATIVA')
                                    <span class="badge bg-success">ATIVA</span>
                                @elseif ($conta->status == 'AGUARDANDO_APROVACAO')
                                    <span class="badge bg-warning text-dark">AGUARDANDO APROVAÇÃO</span>
                                @else
                                    <span class="badge bg-danger">{{ $conta->status }}</span>
                                @endif
                            </div>
                        </div>
                        @if ($conta->status == 'ATIVA')
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalTransferencia">
                                    <i class="fas fa-exchange-alt me-1"></i> Transferir
                                </button>
                                <a href="{{ route('extrato.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-file-invoice-dollar me-1"></i> Extrato</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Resumo de Transações -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-history me-2"></i>Últimas Transações</h4>
            </div>
            <div class="card-body">
                @if($transacoes->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transacoes as $transacao)
                                    <tr>
                                        <td>{{ $transacao->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($transacao->tipo == 'credit')
                                                <span class="badge bg-success">CRÉDITO</span>
                                            @else
                                                <span class="badge bg-danger">DÉBITO</span>
                                            @endif
                                        </td>
                                        <td>{{ $transacao->descricao }}</td>
                                        <td class="text-end fw-bold">
                                             @if($transacao->tipo == 'credit')
                                                + R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                            @else
                                                - R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center">Nenhuma transação encontrada na sua conta ativa principal.</p>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Nenhuma carteira encontrada.</h5>
            <p>Clique em "Abrir Nova Carteira" para começar.</p>
        </div>
    @endif
</div>

<!-- Modal de Transferência -->
<div class="modal fade" id="modalTransferencia" tabindex="-1" aria-labelledby="modalTransferenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTransferenciaLabel"><i class="fas fa-exchange-alt me-2"></i>Realizar Transferência</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Abas de Navegação -->
                <ul class="nav nav-tabs" id="transferenciaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="entre-carteiras-tab" data-bs-toggle="tab" data-bs-target="#entre-carteiras" type="button" role="tab" aria-controls="entre-carteiras" aria-selected="true">Entre Minhas Carteiras</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="para-outros-tab" data-bs-toggle="tab" data-bs-target="#para-outros" type="button" role="tab" aria-controls="para-outros" aria-selected="false">Para Outros Usuários</button>
                    </li>
                </ul>

                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="transferenciaTabContent">
                    <!-- Aba: Entre Minhas Carteiras -->
                    <div class="tab-pane fade show active p-3" id="entre-carteiras" role="tabpanel" aria-labelledby="entre-carteiras-tab">
                        <form id="formEntreCarteiras">
                            <input type="hidden" name="tipo" value="entre_carteiras">
                            <div class="mb-3">
                                <label for="carteira_origem_id_entre" class="form-label">De:</label>
                                <select class="form-select" id="carteira_origem_id_entre" name="carteira_origem_id" required>
                                    @foreach($carteiras as $carteira)
                                        <option value="{{ $carteira->id }}">
                                            {{ $carteira->name }} (Saldo: {{ $carteira->saldo_formatado_atualizado }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="carteira_destino_id_entre" class="form-label">Para:</label>
                                <select class="form-select" id="carteira_destino_id_entre" name="carteira_destino_id" required>
                                     @foreach($carteiras as $carteira)
                                        <option value="{{ $carteira->id }}">{{ $carteira->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="valor_entre" class="form-label">Valor:</label>
                                <input type="text" class="form-control" id="valor_entre" name="valor" inputmode="decimal" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao_entre" class="form-label">Descrição (Opcional):</label>
                                <input type="text" class="form-control" id="descricao_entre" name="descricao">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Transferir</button>
                        </form>
                    </div>
                    <!-- Aba: Para Outros Usuários -->
                    <div class="tab-pane fade p-3" id="para-outros" role="tabpanel" aria-labelledby="para-outros-tab">
                        <form id="formParaOutros">
                            <input type="hidden" name="tipo" value="para_outros">
                             <div class="mb-3">
                                <label for="carteira_origem_id_outros" class="form-label">De:</label>
                                <select class="form-select" id="carteira_origem_id_outros" name="carteira_origem_id" required>
                                     @foreach($carteiras as $carteira)
                                        <option value="{{ $carteira->id }}">
                                            {{ $carteira->name }} (Saldo: {{ $carteira->saldo_formatado_atualizado }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="agencia_destino" class="form-label">Agência Destino:</label>
                                    <input type="text" class="form-control" id="agencia_destino" name="agencia_destino" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="conta_destino" class="form-label">Conta Destino:</label>
                                    <input type="text" class="form-control" id="conta_destino" name="conta_destino" required>
                                </div>
                            </div>
                            
                            <!-- Campo para mostrar o beneficiário -->
                            <div id="beneficiario-info" class="mb-3 d-none">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="fas fa-user me-2"></i>Beneficiário:</h6>
                                    <div id="beneficiario-dados">
                                        <!-- Dados do beneficiário serão inseridos aqui -->
                                    </div>
                                </div>
                            </div>
                             <div class="mb-3">
                                <label for="valor_outros" class="form-label">Valor:</label>
                                <input type="text" class="form-control" id="valor_outros" name="valor" inputmode="decimal" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao_outros" class="form-label">Descrição (Opcional):</label>
                                <input type="text" class="form-control" id="descricao_outros" name="descricao">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Transferir</button>
                        </form>
                    </div>
                </div>
                 <div id="transferencia-alert" class="alert mt-3 d-none" role="alert"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Máscaras de Input
    const contaDestinoEl = document.getElementById('conta_destino');
    const valorEntreEl = document.getElementById('valor_entre');
    const valorOutrosEl = document.getElementById('valor_outros');

    const contaMask = IMask(contaDestinoEl, {
        mask: '00000-00'
    });

    const currencyOptions = {
        mask: 'R$ num',
        blocks: {
            num: {
                mask: Number,
                scale: 2,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                radix: ',',
                mapToRadix: ['.']
            }
        }
    };
    const valorEntreMask = IMask(valorEntreEl, currencyOptions);
    const valorOutrosMask = IMask(valorOutrosEl, currencyOptions);

    // Elementos para busca de beneficiário
    const agenciaDestinoEl = document.getElementById('agencia_destino');
    const beneficiarioInfoEl = document.getElementById('beneficiario-info');
    const beneficiarioDadosEl = document.getElementById('beneficiario-dados');
    let buscaTimeout = null;

    // Função para buscar beneficiário
    function buscarBeneficiario(agencia, conta) {
        if (!agencia || !conta || agencia.length < 4 || conta.length < 6) {
            beneficiarioInfoEl.classList.add('d-none');
            return;
        }

        // Limpar timeout anterior
        if (buscaTimeout) {
            clearTimeout(buscaTimeout);
        }

        // Debounce - aguardar 500ms após parar de digitar
        buscaTimeout = setTimeout(async () => {
            try {
                const apiToken = document.querySelector('meta[name="api-token"]').getAttribute('content');
                const response = await fetch(`{{ url('/api/beneficiario') }}/${agencia}/${conta}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const beneficiario = result.beneficiario;
                    beneficiarioDadosEl.innerHTML = `
                        <div class="row">
                            <div class="col-md-8">
                                <strong>${beneficiario.nome}</strong><br>
                                <small class="text-muted">${beneficiario.tipo_pessoa}</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">
                                    ${beneficiario.banco}<br>
                                    Ag: ${beneficiario.agencia} | C/C: ${beneficiario.conta}
                                </small>
                            </div>
                        </div>
                    `;
                    beneficiarioInfoEl.classList.remove('d-none');
                } else {
                    beneficiarioInfoEl.classList.add('d-none');
                }
            } catch (error) {
                console.error('Erro ao buscar beneficiário:', error);
                beneficiarioInfoEl.classList.add('d-none');
            }
        }, 500);
    }

    // Event listeners para busca de beneficiário
    agenciaDestinoEl.addEventListener('input', function() {
        const agencia = this.value.trim();
        const conta = contaDestinoEl.value.trim();
        buscarBeneficiario(agencia, conta);
    });

    contaDestinoEl.addEventListener('input', function() {
        const agencia = agenciaDestinoEl.value.trim();
        const conta = this.value.trim();
        buscarBeneficiario(agencia, conta);
    });

    const formEntreCarteiras = document.getElementById('formEntreCarteiras');
    const formParaOutros = document.getElementById('formParaOutros');
    const alerta = document.getElementById('transferencia-alert');

    const handleFormSubmit = async (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const apiToken = document.querySelector('meta[name="api-token"]').getAttribute('content');

        // Remove a máscara antes de enviar para a API
        if (data.valor) {
            if (form.id === 'formEntreCarteiras') {
                data.valor = valorEntreMask.unmaskedValue;
            } else {
                data.valor = valorOutrosMask.unmaskedValue;
            }
        }
        // Mantém a máscara (com hífen) para que o backend consiga localizar a conta
        // Não remover o hífen aqui, o backend aceitará ambos os formatos

        // Limpa alerta anterior
        alerta.classList.add('d-none');
        alerta.textContent = '';

        try {
            const response = await fetch('{{ url("/api/transferencias") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + apiToken
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (!response.ok) {
                // Monta mensagem de erro
                let errorMessage = result.message || 'Ocorreu um erro.';
                if (result.errors) {
                    errorMessage += '<br>' + Object.values(result.errors).map(e => e.join(', ')).join('<br>');
                }
                showAlert(errorMessage, 'danger');
            } else {
                showAlert(result.message || 'Transferência enviada para processamento!', 'success');
                
                // Fechar o modal após 2 segundos
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTransferencia'));
                    if (modal) {
                        modal.hide();
                    }
                    // Limpar formulários
                    form.reset();
                    // Recarregar a página para atualizar saldos
                    window.location.reload();
                }, 2000);
            }
        } catch (error) {
            showAlert('Não foi possível conectar à API. Verifique sua conexão.', 'danger');
        }
    };

    formEntreCarteiras.addEventListener('submit', handleFormSubmit);
    formParaOutros.addEventListener('submit', handleFormSubmit);

    function showAlert(message, type) {
        alerta.innerHTML = message;
        alerta.className = `alert alert-${type} mt-3`;
        alerta.classList.remove('d-none');
    }
});
</script>
@endpush
