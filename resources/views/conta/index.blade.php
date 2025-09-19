@extends('layouts.app')

@section('title', 'Minha Carteira - ' . config('app.name'))

@php
function formatarTipoConta($tipoConta) {
    switch($tipoConta) {
        case 'corrente':
            return 'Conta Corrente';
        case 'poupanca':
            return 'Conta Poupança';
        case 'empresarial':
            return 'Conta Empresarial';
        default:
            return ucfirst($tipoConta);
    }
}
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel do Cliente</h2>
        <div>
            @if(Auth::user()->isAprovado())
                <a href="{{ route('conta.abrir.form') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Abrir Nova Carteira
                </a>
            @else
                <button class="btn btn-secondary" disabled title="Apenas usuários aprovados podem abrir novas carteiras">
                    <i class="fas fa-plus-circle me-1"></i> Abrir Nova Carteira
                </button>
            @endif
        </div>
    </div>

    <!-- Seleção de Conta -->
        <div class="card mb-4">
            <div class="card-header">
            <h4><i class="fas fa-wallet me-2"></i>Selecionar Conta</h4>
        </div>
        <div class="card-body">

            @if($contas->isNotEmpty())
                <!-- Dropdown para selecionar tipo de conta -->
                <div class="mb-4">
                    <label for="tipo-conta-select" class="form-label">Tipo de Conta:</label>
                    <select id="tipo-conta-select" class="form-select">
                        <option value="">Selecione o tipo de conta</option>
                        <option value="fisica">👤 Contas Pessoais ({{ $contasFisicas->count() }} contas)</option>
                        <option value="juridica">🏢 Contas Empresariais ({{ $contasJuridicas->count() }} contas)</option>
                    </select>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <h5><i class="fas fa-info-circle me-2"></i>Nenhuma conta bancária encontrada</h5>
                    <p>Você precisa ter pelo menos uma conta bancária aprovada para acessar esta funcionalidade.</p>
                    @if(Auth::user()->isAprovado())
                        <a href="{{ route('conta.abrir.form') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Abrir Nova Conta
                        </a>
                    @endif
                </div>
            @endif

            @if($contas->isNotEmpty())
                <!-- Contas Físicas -->
                <div id="contas-fisicas" class="row" style="display: none;">
                    <div class="col-12 mb-3">
                        <h5><i class="fas fa-user me-2"></i>Contas Pessoais</h5>
                    </div>
                    @if($contasFisicas->count() > 0)
                        @foreach ($contasFisicas as $conta)
                        @if($conta->status == 'ATIVA')
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 conta-card" data-conta-id="{{ $conta->id }}" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ formatarTipoConta($conta->tipo_conta) }}</h5>
                                        <p class="card-text">
                                            <strong>Agência:</strong> {{ $conta->agencia }}<br>
                                            <strong>Conta:</strong> {{ $conta->numero }}
                                        </p>
                                        <span class="badge bg-success">ATIVA</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100" style="opacity: 0.5;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ formatarTipoConta($conta->tipo_conta) }}</h5>
                                        <p class="card-text">
                                            <strong>Agência:</strong> {{ $conta->agencia }}<br>
                                            <strong>Conta:</strong> {{ $conta->numero }}
                                        </p>
                                        @if ($conta->status == 'AGUARDANDO_APROVACAO')
                                            <span class="badge bg-warning text-dark">AGUARDANDO APROVAÇÃO</span>
                                        @else
                                            <span class="badge bg-danger">{{ $conta->status }}</span>
                                        @endif
                                    </div>
                                </div>
            </div>
                        @endif
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>Nenhuma conta pessoal encontrada.
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Contas Jurídicas -->
                <div id="contas-juridicas" class="row" style="display: none;">
                    <div class="col-12 mb-3">
                        <h5><i class="fas fa-building me-2"></i>Contas Empresariais</h5>
                    </div>
                    @if($contasJuridicas->count() > 0)
                        @foreach ($contasJuridicas as $conta)
                        @if($conta->status == 'ATIVA')
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 conta-card" data-conta-id="{{ $conta->id }}" style="cursor: pointer;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ formatarTipoConta($conta->tipo_conta) }}</h5>
                                        <p class="card-text">
                                            <strong>Agência:</strong> {{ $conta->agencia }}<br>
                                            <strong>Conta:</strong> {{ $conta->numero }}
                                        </p>
                                    <span class="badge bg-success">ATIVA</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100" style="opacity: 0.5;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ formatarTipoConta($conta->tipo_conta) }}</h5>
                                        <p class="card-text">
                                            <strong>Agência:</strong> {{ $conta->agencia }}<br>
                                            <strong>Conta:</strong> {{ $conta->numero }}
                                        </p>
                                        @if ($conta->status == 'AGUARDANDO_APROVACAO')
                                    <span class="badge bg-warning text-dark">AGUARDANDO APROVAÇÃO</span>
                                @else
                                    <span class="badge bg-danger">{{ $conta->status }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>Nenhuma conta empresarial encontrada.
                            </div>
                        </div>
                    @endif
                </div>
                            </div>
                        @endif

        <!-- Detalhes da Conta Selecionada -->
        <div id="detalhes-conta" class="card mb-4" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-credit-card me-2"></i>Detalhes da Conta</h4>
                <div>
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTransferencia" id="btn-transferir-conta">
                        <i class="fas fa-exchange-alt me-1"></i> Transferir
                    </button>
                    <a href="#" class="btn btn-secondary" id="btn-extrato">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Extrato
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informações da Conta</h5>
                        <p><strong>Tipo:</strong> <span id="conta-tipo"></span></p>
                        <p><strong>Agência:</strong> <span id="conta-agencia"></span></p>
                        <p><strong>Número:</strong> <span id="conta-numero"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Saldo Atual</h5>
                        <h3 class="text-primary" id="conta-saldo">R$ 0,00</h3>
                    </div>
                </div>
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
                <div id="info-conta-selecionada"></div>
                
                <!-- Transferência -->
                <div id="secao-transferencia">
                    <h6><i class="fas fa-exchange-alt me-2"></i>Realizar Transferência</h6>

                <!-- Formulário de Transferência -->
                <div class="p-3">

                    <form id="formTransferencia">
                            <input type="hidden" name="tipo" value="entre_carteiras">
                        
                        <!-- Carteira de Origem (Fixa) -->
                            <div class="mb-3">
                            <label class="form-label">De:</label>
                            <div class="alert alert-info">
                                <strong id="conta-origem-info">Conta selecionada será exibida aqui</strong>
                            </div>
                            <input type="hidden" id="carteira_origem_id" name="carteira_origem_id" value="">
                            </div>

                        <!-- Destino (Campos Separados) -->
                            <div class="mb-3">
                            <label class="form-label">Para:</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="agencia_destino" class="form-label">Agência:</label>
                                    <input type="text" class="form-control" id="agencia_destino" name="agencia_destino" 
                                           placeholder="Ex: 1234" maxlength="10">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="conta_destino" class="form-label">Conta:</label>
                                    <input type="text" class="form-control" id="conta_destino" name="conta_destino" 
                                           placeholder="Ex: 56789-0" maxlength="20">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="buscarDestinatarioTeste()">
                                        <i class="fas fa-search me-1"></i>Buscar Destinatário
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campo para mostrar o beneficiário (apenas para transferências para outros) -->
                        <div id="beneficiario-info" class="mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="mb-2"><i class="fas fa-user me-2"></i>Beneficiário:</h6>
                                <div id="beneficiario-dados">
                                    <!-- Dados do beneficiário serão inseridos aqui -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Valor -->
                             <div class="mb-3">
                            <label for="valor" class="form-label">Valor:</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="valor" name="valor" 
                                       step="0.01" min="0.01" required placeholder="0,00">
                            </div>
                        </div>
                        
                        <!-- Descrição -->
                            <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (opcional):</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" 
                                   placeholder="Ex: Transferência entre carteiras">
                        </div>
                        
                        <!-- Botão de Transferência -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-exchange-alt me-2"></i>Transferir
                            </button>
                            </div>
                        </form>
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
    // Função para formatar tipo de conta
    function formatarTipoConta(tipoConta) {
        switch(tipoConta) {
            case 'corrente':
                return 'Conta Corrente';
            case 'poupanca':
                return 'Conta Poupança';
            case 'empresarial':
                return 'Conta Empresarial';
            default:
                return tipoConta.charAt(0).toUpperCase() + tipoConta.slice(1);
        }
    }

    // Dados das contas e carteiras do servidor
    const contas = @json($contas);
    // Inicializar carteiras com dados do servidor
    let carteiras = @json($carteiras);
    
    // Filtrar apenas carteiras válidas na inicialização
    carteiras = carteiras.filter(carteira => {
        return carteira && 
               carteira.id && 
               carteira.name && 
               carteira.status === 'ATIVA' && 
               carteira.approval_status === 'approved' &&
               !carteira.deleted_at;
    });
    
    console.log('Carteiras inicializadas:', carteiras.length);
    
    console.log('=== DEBUG DADOS ===');
    console.log('Contas carregadas:', contas);
    console.log('Carteiras carregadas:', carteiras);
    console.log('==================');
    

    // Controlar o dropdown de tipo de conta
    const tipoContaSelect = document.getElementById('tipo-conta-select');
    const contasFisicas = document.getElementById('contas-fisicas');
    const contasJuridicas = document.getElementById('contas-juridicas');
    
    tipoContaSelect.addEventListener('change', function() {
        const valor = this.value;
        
        // Ocultar todas as seções
        contasFisicas.style.display = 'none';
        contasJuridicas.style.display = 'none';
        
        // Mostrar a seção selecionada
        if (valor === 'fisica') {
            contasFisicas.style.display = 'block';
        } else if (valor === 'juridica') {
            contasJuridicas.style.display = 'block';
        }
        
        // Ocultar detalhes da conta se houver
        document.getElementById('detalhes-conta').style.display = 'none';
    });
    

    // Event listeners para seleção de conta
    document.querySelectorAll('.conta-card').forEach(card => {
        card.addEventListener('click', function() {
            const contaId = this.getAttribute('data-conta-id');
            const conta = contas.find(c => c.id == contaId);
            
            if (conta) {
                // Atualizar detalhes da conta
                document.getElementById('conta-tipo').textContent = formatarTipoConta(conta.tipo_conta);
                document.getElementById('conta-agencia').textContent = conta.agencia;
                document.getElementById('conta-numero').textContent = conta.numero;
                
                // Buscar carteira específica desta conta baseada na agência e número
                console.log('Buscando carteira específica para conta:', conta.agencia, conta.numero);
                console.log('Carteiras disponíveis:', carteiras.map(c => ({ id: c.id, name: c.name, balance: c.balance })));
                
                let carteiraEspecifica = carteiras.find(carteira => {
                    // Buscar carteira específica que contenha a agência e número da conta
                    const encontrou = carteira.name.includes(conta.agencia) && carteira.name.includes(conta.numero);
                    if (encontrou) {
                        console.log('Carteira específica encontrada:', carteira.name, 'Saldo:', carteira.balance);
                    }
                    return encontrou;
                });
                
                // Se não encontrar carteira específica, buscar carteira "Principal" do tipo correto
                if (!carteiraEspecifica) {
                    const tipoFormatado = conta.tipo_conta.charAt(0).toUpperCase() + conta.tipo_conta.slice(1);
                    carteiraEspecifica = carteiras.find(carteira => {
                        return carteira.name === `Principal - ${tipoFormatado}` || 
                               carteira.name === `Principal - ${tipoFormatado} (PF)` ||
                               carteira.name === `Principal - ${tipoFormatado} (PJ)`;
                    });
                }
                
                // Se não encontrar carteira específica, usar a primeira carteira ativa disponível
                if (!carteiraEspecifica) {
                    console.log('Carteira específica não encontrada, buscando primeira carteira ativa...');
                    carteiraEspecifica = carteiras.find(c => c.status === 'ATIVA' && c.approval_status === 'approved') || { balance: 0, id: null };
                    console.log('Carteira encontrada como fallback:', carteiraEspecifica);
                }
                
                if (carteiraEspecifica) {
                    // Mostrar saldo da carteira específica
                    const saldoFormatado = 'R$ ' + carteiraEspecifica.balance.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    document.getElementById('conta-saldo').textContent = saldoFormatado;
                    
                    // Atualizar link do extrato
                    document.getElementById('btn-extrato').href = `/extrato/carteira/${carteiraEspecifica.id}`;
                    
                    // Atualizar botão de transferir com dados da carteira específica
                    const btnTransferir = document.getElementById('btn-transferir-conta');
                    if (btnTransferir) {
                        if (carteiraEspecifica.id) {
                            btnTransferir.setAttribute('data-carteira-id', carteiraEspecifica.id);
                            console.log('Botão de transferir configurado com carteira ID:', carteiraEspecifica.id);
                        } else {
                            console.error('Carteira ID é null! Não é possível configurar o botão de transferir.');
                        }
                        btnTransferir.setAttribute('data-conta-id', conta.id);
                        btnTransferir.setAttribute('data-conta-tipo', conta.tipo_conta);
                        btnTransferir.setAttribute('data-conta-agencia', conta.agencia);
                        btnTransferir.setAttribute('data-conta-numero', conta.numero);
                    }
                } else {
                    document.getElementById('conta-saldo').textContent = 'R$ 0,00';
                    document.getElementById('btn-extrato').href = '#';
                }
                
                // Mostrar seção de detalhes
                document.getElementById('detalhes-conta').style.display = 'block';
                
                // Destacar conta selecionada
                document.querySelectorAll('.conta-card').forEach(c => c.classList.remove('border-primary'));
                this.classList.add('border-primary');
            }
        });
    });



    // Função para atualizar dados das carteiras
    function atualizarDadosCarteiras() {
        console.log('Atualizando dados das carteiras...');
        
        // Fazer requisição para obter dados atualizados
        // Obter CSRF token de forma segura
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        fetch('/api/carteiras', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Dados atualizados recebidos:', data);
            
            // Filtrar apenas carteiras válidas e ativas
            const carteirasValidas = (data.data || data).filter(carteira => {
                return carteira && 
                       carteira.id && 
                       carteira.name && 
                       carteira.status === 'ATIVA' && 
                       carteira.approval_status === 'approved' &&
                       !carteira.deleted_at; // Excluir carteiras deletadas
            });
            
            console.log('Carteiras válidas encontradas:', carteirasValidas.length);
            
            // Atualizar variável global carteiras
            carteiras = carteirasValidas;
            
            // Atualizar dropdown de origem
            const selectOrigem = document.getElementById('carteira_origem_id');
            if (selectOrigem) {
                selectOrigem.innerHTML = '<option value="">Selecione a carteira de origem</option>';
                
                carteirasValidas.forEach(carteira => {
                    if (carteira.balance > 0) {
                        const option = document.createElement('option');
                        option.value = carteira.id;
                        const saldoFormatado = 'R$ ' + parseFloat(carteira.balance).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        option.textContent = `${carteira.name} (Saldo: ${saldoFormatado})`;
                        selectOrigem.appendChild(option);
                    }
                });
                
                console.log('Dropdown origem atualizado com', selectOrigem.children.length - 1, 'opções');
            }
            
            // Atualizar dropdown de destino (para transferências entre carteiras)
            const selectDestino = document.getElementById('carteira_destino_id');
            if (selectDestino) {
                selectDestino.innerHTML = '<option value="">Selecione a carteira de destino</option>';
                
                carteirasValidas.forEach(carteira => {
                    const option = document.createElement('option');
                    option.value = carteira.id;
                    const saldoFormatado = 'R$ ' + parseFloat(carteira.balance).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    option.textContent = `${carteira.name} (Saldo: ${saldoFormatado})`;
                    selectDestino.appendChild(option);
                });
                
                console.log('Dropdown destino atualizado com', selectDestino.children.length - 1, 'opções');
            }
            
            console.log('Dados das carteiras atualizados com sucesso!');
        })
        .catch(error => {
            console.error('Erro ao atualizar dados das carteiras:', error);
            // Em caso de erro, limpar dropdowns
            const selectOrigem = document.getElementById('carteira_origem_id');
            const selectDestino = document.getElementById('carteira_destino_id');
            
            if (selectOrigem) selectOrigem.innerHTML = '<option value="">Erro ao carregar carteiras</option>';
            if (selectDestino) selectDestino.innerHTML = '<option value="">Erro ao carregar carteiras</option>';
        });
    }

    // Event listener para quando o modal de transferência for aberto
    document.getElementById('modalTransferencia').addEventListener('show.bs.modal', function (event) {
        console.log('Modal de transferência aberto - definindo conta de origem...');
        
        const btnTransferir = event.relatedTarget;
        const carteiraId = btnTransferir.getAttribute('data-carteira-id');
        const contaTipo = btnTransferir.getAttribute('data-conta-tipo');
        const contaAgencia = btnTransferir.getAttribute('data-conta-agencia');
        const contaNumero = btnTransferir.getAttribute('data-conta-numero');
        
        if (carteiraId) {
            // Definir conta de origem fixa
            const inputOrigem = document.getElementById('carteira_origem_id');
            const infoOrigem = document.getElementById('conta-origem-info');
            
            if (inputOrigem && infoOrigem) {
                inputOrigem.value = carteiraId;
                console.log('Carteira origem definida:', carteiraId);
                
                // Buscar informações da carteira
                const carteiraSelecionada = carteiras.find(c => c.id == carteiraId);
                if (carteiraSelecionada) {
                    const saldoFormatado = 'R$ ' + parseFloat(carteiraSelecionada.balance).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    infoOrigem.innerHTML = `${carteiraSelecionada.name} - Agência: ${contaAgencia} - Conta: ${contaNumero} (Saldo: ${saldoFormatado})`;
                } else {
                    infoOrigem.innerHTML = `${contaTipo} - Agência: ${contaAgencia} - Conta: ${contaNumero}`;
                }
            } else {
                console.error('Elementos não encontrados:', {inputOrigem, infoOrigem});
            }
            
            // Limpar campos de destino
            const agenciaDestino = document.getElementById('agencia_destino');
            const contaDestino = document.getElementById('conta_destino');
            if (agenciaDestino) agenciaDestino.value = '';
            if (contaDestino) contaDestino.value = '';
            
            // Ocultar informações do beneficiário
            const beneficiarioInfo = document.getElementById('beneficiario-info');
            if (beneficiarioInfo) {
                beneficiarioInfo.classList.add('d-none');
            }
            
            // Teste dos elementos após 2 segundos
            setTimeout(function() {
                console.log('=== TESTE DOS ELEMENTOS APÓS ABRIR MODAL ===');
                const agenciaInput = document.getElementById('agencia_destino');
                const contaInput = document.getElementById('conta_destino');
                const beneficiarioInfo = document.getElementById('beneficiario-info');
                const beneficiarioDados = document.getElementById('beneficiario-dados');
                
                console.log('Agência Input:', agenciaInput);
                console.log('Conta Input:', contaInput);
                console.log('Beneficiário Info:', beneficiarioInfo);
                console.log('Beneficiário Dados:', beneficiarioDados);
                
                if (agenciaInput && contaInput) {
                    console.log('✅ Elementos encontrados!');
                } else {
                    console.error('❌ Elementos não encontrados!');
                }
            }, 2000);
        }
    });

    // Função global para teste
    window.buscarDestinatarioTeste = function() {
        console.log('=== TESTE MANUAL DA BUSCA ===');
        buscarDestinatario();
    };

    // Buscar informações do destinatário
    function buscarDestinatario() {
        const agencia = document.getElementById('agencia_destino').value.trim();
        const conta = document.getElementById('conta_destino').value.trim();
        const beneficiarioInfo = document.getElementById('beneficiario-info');
        const beneficiarioDados = document.getElementById('beneficiario-dados');
        
        console.log('=== DEBUG BUSCAR DESTINATÁRIO ===');
        console.log('Agência:', agencia);
        console.log('Conta:', conta);
        console.log('Beneficiário Info:', beneficiarioInfo);
        console.log('Beneficiário Dados:', beneficiarioDados);
        
        if (agencia && conta) {
            console.log('Buscando destinatário:', agencia, conta);
            
            // Fazer requisição para buscar informações do destinatário
            console.log('Fazendo requisição para /api/buscar-destinatario');
            console.log('Dados:', { agencia: agencia, conta: conta });
            
            fetch('/api/buscar-destinatario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    agencia: agencia,
                    conta: conta
                })
            })
            .then(response => {
                console.log('Resposta recebida:', response);
                return response.json();
            })
            .then(data => {
                console.log('Dados da resposta:', data);
                if (data.success && data.destinatario) {
                    const destinatario = data.destinatario;
                    beneficiarioDados.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${destinatario.nome || 'Nome não disponível'}</h6>
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-university me-1"></i>
                                    Agência: ${agencia} | Conta: ${conta}
                                </p>
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-credit-card me-1"></i>
                                    ${destinatario.tipo_conta || 'Tipo não disponível'}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Conta Ativa
                                </span>
                            </div>
                        </div>
                    `;
                    beneficiarioInfo.classList.remove('d-none');
                } else {
                    beneficiarioDados.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-warning">Conta não encontrada</h6>
                                <p class="mb-1 text-muted">
                                    <i class="fas fa-university me-1"></i>
                                    Agência: ${agencia} | Conta: ${conta}
                                </p>
                                <p class="mb-0 text-muted">
                                    Verifique se os dados estão corretos
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Não Encontrada
                                </span>
                            </div>
                        </div>
                    `;
                    beneficiarioInfo.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Erro ao buscar destinatário:', error);
                beneficiarioDados.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-danger">Erro na busca</h6>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-university me-1"></i>
                                Agência: ${agencia} | Conta: ${conta}
                            </p>
                            <p class="mb-0 text-muted">
                                Não foi possível buscar as informações
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Erro
                            </span>
                        </div>
                    </div>
                `;
                beneficiarioInfo.classList.remove('d-none');
            });
        } else {
            beneficiarioInfo.classList.add('d-none');
        }
    }

    // Event listeners para buscar destinatário
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== CONFIGURANDO EVENT LISTENERS ===');
        
        // Aguardar um pouco para garantir que o modal foi carregado
        setTimeout(function() {
            const agenciaInput = document.getElementById('agencia_destino');
            const contaInput = document.getElementById('conta_destino');
            
            console.log('Agência Input:', agenciaInput);
            console.log('Conta Input:', contaInput);
            
            if (agenciaInput && contaInput) {
                console.log('Event listeners configurados com sucesso');
                
                // Buscar quando ambos os campos tiverem valor
                agenciaInput.addEventListener('input', function() {
                    console.log('Evento input na agência:', this.value);
                    buscarDestinatario();
                });
                
                contaInput.addEventListener('input', function() {
                    console.log('Evento input na conta:', this.value);
                    buscarDestinatario();
                });
                
                // Teste manual - buscar quando ambos os campos tiverem valor
                agenciaInput.addEventListener('blur', function() {
                    console.log('Evento blur na agência:', this.value);
                    buscarDestinatario();
                });
                
                contaInput.addEventListener('blur', function() {
                    console.log('Evento blur na conta:', this.value);
                    buscarDestinatario();
                });
            } else {
                console.error('Elementos não encontrados!');
            }
        }, 1000);
    });

    // Máscaras de Input
    const contaDestinoEl = document.getElementById('conta_destino');
    if (contaDestinoEl) {
        const contaMask = IMask(contaDestinoEl, {
            mask: '00000000-0' // Máscara mais genérica
        });
    }

    // Listener para o formulário de transferência
    const formTransferencia = document.getElementById('formTransferencia');
    if (formTransferencia) {
        formTransferencia.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('Formulário de transferência enviado.');

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.tipo = 'para_outros'; // Garante o tipo correto

            console.log('Dados do formulário:', data);

            // Validações
            if (!data.carteira_origem_id || data.carteira_origem_id === '') {
                alert('Erro: A carteira de origem não foi identificada. Por favor, feche o modal e tente novamente.');
                return;
            }
            const valor = parseFloat(data.valor);
            if (isNaN(valor) || valor <= 0) {
                alert('O valor da transferência deve ser um número maior que zero.');
                return;
            }
            if (!data.agencia_destino || !data.conta_destino) {
                alert('Por favor, preencha a agência e a conta de destino.');
                return;
            }

            // Lógica de envio
            const btnSubmit = this.querySelector('button[type="submit"]');
            const btnOriginalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processando...';
            btnSubmit.disabled = true;

            console.log('Enviando para /api/transferencias com os dados:', data);

            // Obter CSRF token de forma segura
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            // Usar rota web em vez da API
            formData.append('carteira_origem_id', data.carteira_origem_id);
            formData.append('agencia_destino', data.agencia_destino);
            formData.append('conta_destino', data.conta_destino);
            formData.append('valor', data.valor);
            formData.append('descricao', data.descricao || '');
            formData.append('tipo', data.tipo);
            formData.append('_token', csrfToken);
            
            // Usar ID da carteira que está sendo exibida (obtido do botão de transferir)
            const btnTransferir = document.getElementById('btn-transferir-conta');
            const carteiraId = btnTransferir ? btnTransferir.getAttribute('data-carteira-id') : 1;
            
            console.log('Usando carteira ID:', carteiraId);
            
            fetch(`/conta/transferencia/${carteiraId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Resposta da API recebida:', response);
                return response.json();
            })
            .then(result => {
                console.log('Resultado do processamento:', result);
                if (result.success) {
                    alert('Transferência processada com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao processar a transferência: ' + (result.message || 'Ocorreu um erro desconhecido.'));
                }
            })
            .catch(error => {
                console.error('Erro de comunicação com a API:', error);
                alert('Erro de comunicação ao tentar realizar a transferência.');
            })
            .finally(() => {
                btnSubmit.innerHTML = btnOriginalText;
                btnSubmit.disabled = false;
            });
        });
    } else {
        console.error('O formulário de transferência #formTransferencia não foi encontrado.');
    }

});
</script>
@endpush
