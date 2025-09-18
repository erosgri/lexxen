@extends('layouts.app')

@section('title', 'Extrato - ' . $carteira->name . ' - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-file-invoice-dollar me-2"></i>Extrato da Carteira</h2>
            <p class="text-muted mb-0">{{ $carteira->name }}</p>
        </div>
        <div>
            <a href="{{ route('extrato.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Resumo da Carteira -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Saldo Atual</h6>
                            <h4>R$ {{ number_format($resumo['saldo_atual'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Créditos</h6>
                            <h4>R$ {{ number_format($resumo['total_creditos'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Débitos</h6>
                            <h4>R$ {{ number_format($resumo['total_debitos'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Movimentação</h6>
                            <h4>R$ {{ number_format($resumo['saldo_periodo'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('extrato.carteira', $carteira) }}">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="tipo_operacao" class="form-label">Tipo</label>
                        <select name="tipo_operacao" id="tipo_operacao" class="form-select">
                            <option value="">Todos</option>
                            @foreach($filtros['tipos_operacao'] as $valor => $label)
                                <option value="{{ $valor }}" {{ request('tipo_operacao') == $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="data_inicial" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="data_inicial" name="data_inicial" 
                               value="{{ request('data_inicial') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="data_final" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="data_final" name="data_final" 
                               value="{{ request('data_final') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="ordenacao" class="form-label">Ordenação</label>
                        <select name="ordenacao" id="ordenacao" class="form-select">
                            @foreach($filtros['ordenacoes'] as $valor => $label)
                                <option value="{{ $valor }}" {{ request('ordenacao') == $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="descricao" name="descricao" 
                               placeholder="Buscar na descrição..." value="{{ request('descricao') }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <a href="{{ route('extrato.carteira', $carteira) }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Transações -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-list me-2"></i>Transações ({{ $resumo['quantidade_transacoes'] }})</h5>
            <small class="text-muted">
                @if(request()->hasAny(['tipo', 'data_inicial', 'data_final', 'descricao']))
                    Filtros aplicados
                @else
                    Todas as transações
                @endif
            </small>
        </div>
        <div class="card-body">
            @if($transacoes->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Data/Hora</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transacoes as $extrato)
                                <tr>
                                    <td>
                                        <div>{{ $extrato->data_operacao->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $extrato->data_operacao->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($extrato->isEntrada())
                                            <span class="badge bg-success">
                                                <i class="fas fa-arrow-up me-1"></i>{{ $extrato->tipo_operacao_formatado }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-arrow-down me-1"></i>{{ $extrato->tipo_operacao_formatado }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $extrato->descricao }}</div>
                                        @if($extrato->tipo_operacao === 'transferencia_origem' || $extrato->tipo_operacao === 'transferencia_destino')
                                            <small class="text-muted">
                                                @if($extrato->conta_origem && $extrato->conta_destino)
                                                    <i class="fas fa-exchange-alt me-1"></i>
                                                    De: {{ $extrato->conta_origem }} → Para: {{ $extrato->conta_destino }}
                                                @elseif($extrato->conta_origem)
                                                    <i class="fas fa-arrow-right me-1"></i>
                                                    De: {{ $extrato->conta_origem }}
                                                @elseif($extrato->conta_destino)
                                                    <i class="fas fa-arrow-left me-1"></i>
                                                    Para: {{ $extrato->conta_destino }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        @if($extrato->isEntrada())
                                            <span class="text-success">
                                                + {{ $extrato->valor_formatado }}
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                - {{ $extrato->valor_formatado }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted">{{ $extrato->saldo_formatado }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $transacoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma transação encontrada</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['tipo', 'data_inicial', 'data_final', 'descricao']))
                            Tente ajustar os filtros para encontrar transações.
                        @else
                            Esta carteira ainda não possui transações registradas.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
