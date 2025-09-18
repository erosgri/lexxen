@extends('layouts.app')

@section('title', 'Extrato Completo - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Extrato da Conta {{ $conta->numero }}</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Voltar ao Painel</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history me-2"></i>Histórico de Transações</h4>
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

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $transacoes->links() }}
                </div>
            @else
                <p class="text-center">Nenhuma transação encontrada para esta conta.</p>
            @endif
        </div>
    </div>
</div>
@endsection






