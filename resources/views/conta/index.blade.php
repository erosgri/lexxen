@extends('layouts.app')

@section('title', 'Minha Conta - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel do Cliente</h2>
        <a href="{{ route('conta.abrir.form') }}" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i> Abrir Nova Conta</a>
    </div>

    @if($contas->isNotEmpty())
        <!-- Listagem de Contas -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><i class="fas fa-wallet me-2"></i>Minhas Contas</h4>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($contas as $conta)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                <h5 class="mb-1">Conta {{ ucfirst($conta->tipo_conta) }} - Nº {{ $conta->numero }}</h5>
                                <p class="mb-1">Agência: {{ $conta->agencia }}</p>
                            </div>
                            <div class="text-end">
                                <h5 class="fw-bold">{{ $conta->getSaldoFormatadoAttribute() }}</h5>
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
                                <a href="{{ route('conta.transferencia.form', ['conta' => $conta->id]) }}" class="btn btn-primary btn-sm me-1"><i class="fas fa-exchange-alt me-1"></i> Transferir</a>
                                <a href="{{ route('conta.saque.form', ['conta' => $conta->id]) }}" class="btn btn-info btn-sm me-1 text-white"><i class="fas fa-money-bill-wave me-1"></i> Saque</a>
                                <a href="{{ route('conta.extrato', ['conta' => $conta->id]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-file-invoice-dollar me-1"></i> Extrato</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Resumo de Transações da Conta Ativa Principal -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-history me-2"></i>Últimas Transações (Conta Principal Ativa)</h4>
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
                                            @if($transacao->tipo == 'DEPOSITO' || $transacao->tipo == 'TRANSFERENCIA_RECEBIDA')
                                                <span class="badge bg-success">{{ $transacao->tipo }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $transacao->tipo }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transacao->descricao }}</td>
                                        <td class="text-end fw-bold">
                                             @if($transacao->tipo == 'DEPOSITO' || $transacao->tipo == 'TRANSFERENCIA_RECEBIDA')
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
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Nenhuma conta bancária encontrada.</h5>
            <p>Clique em "Abrir Nova Conta" para começar.</p>
        </div>
    @endif
</div>
@endsection
