@extends('layouts.app')

@section('title', 'Contas Bancárias')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-credit-card me-2"></i>Contas Bancárias</h1>
    <a href="{{ route('contas-bancarias.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nova Conta
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Número da Conta</th>
                        <th>Agência</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contasBancarias as $conta)
                    <tr>
                        <td>{{ $conta->id }}</td>
                        <td>{{ $conta->numero_conta }}</td>
                        <td>{{ $conta->agencia }}</td>
                        <td>
                            @switch($conta->tipo_conta)
                                @case('corrente')
                                    <span class="badge bg-primary">Conta Corrente</span>
                                    @break
                                @case('poupanca')
                                    <span class="badge bg-success">Poupança</span>
                                    @break
                                @case('salario')
                                    <span class="badge bg-info">Salário</span>
                                    @break
                            @endswitch
                        </td>
                        <td>{{ $conta->user->name }}</td>
                        <td>{{ $conta->saldo_formatado }}</td>
                        <td>
                            @switch($conta->status)
                                @case('ativa')
                                    <span class="badge bg-success">Ativa</span>
                                    @break
                                @case('inativa')
                                    <span class="badge bg-secondary">Inativa</span>
                                    @break
                                @case('bloqueada')
                                    <span class="badge bg-danger">Bloqueada</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('contas-bancarias.show', $conta->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('contas-bancarias.edit', $conta->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('contas-bancarias.destroy', $conta->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Tem certeza que deseja remover esta conta?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma conta bancária encontrada</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection









