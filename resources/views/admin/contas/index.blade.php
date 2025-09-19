@extends('layouts.app')

@section('title', 'Gerenciar Contas Bancárias - ' . config('app.name'))

@section('content')
<div class="container">
    <h2>Gerenciar Contas Bancárias</h2>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Por Status:</h6>
                    <div class="btn-group mb-3" role="group">
                        <a href="{{ route('contas-bancarias.index') }}" 
                           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            Todas
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['status' => 'AGUARDANDO_APROVACAO']) }}" 
                           class="btn {{ request('status') == 'AGUARDANDO_APROVACAO' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Aguardando Aprovação
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['status' => 'ATIVA']) }}" 
                           class="btn {{ request('status') == 'ATIVA' ? 'btn-success' : 'btn-outline-success' }}">
                            Ativas
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['status' => 'BLOQUEADA']) }}" 
                           class="btn {{ request('status') == 'BLOQUEADA' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Bloqueadas
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Por Tipo de Conta:</h6>
                    <div class="btn-group mb-3" role="group">
                        <a href="{{ route('contas-bancarias.index') }}" 
                           class="btn {{ !request('tipo_conta') ? 'btn-primary' : 'btn-outline-primary' }}">
                            Todas as Contas
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['tipo_conta' => 'corrente']) }}" 
                           class="btn {{ request('tipo_conta') == 'corrente' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="fas fa-credit-card me-1"></i>Conta Corrente
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['tipo_conta' => 'poupanca']) }}" 
                           class="btn {{ request('tipo_conta') == 'poupanca' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="fas fa-piggy-bank me-1"></i>Conta Poupança
                        </a>
                        <a href="{{ route('contas-bancarias.index', ['tipo_conta' => 'empresarial']) }}" 
                           class="btn {{ request('tipo_conta') == 'empresarial' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="fas fa-building me-1"></i>Conta Empresarial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Ag./Conta</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contas as $conta)
                            <tr>
                                <td>{{ $conta->user->name }}</td>
                                <td>{{ $conta->user->email }}</td>
                                <td>{{ $conta->agencia }} / {{ $conta->numero }}</td>
                                <td>
                                    @switch($conta->tipo_conta)
                                        @case('corrente')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-credit-card me-1"></i>Conta Corrente
                                            </span>
                                            @break
                                        @case('poupanca')
                                            <span class="badge bg-info">
                                                <i class="fas fa-piggy-bank me-1"></i>Conta Poupança
                                            </span>
                                            @break
                                        @case('empresarial')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-building me-1"></i>Conta Empresarial
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($conta->tipo_conta) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if ($conta->status == 'ATIVA')
                                        <span class="badge bg-success">ATIVA</span>
                                    @elseif ($conta->status == 'AGUARDANDO_APROVACAO')
                                        <span class="badge bg-warning text-dark">AGUARDANDO APROVAÇÃO</span>
                                    @else
                                        <span class="badge bg-danger">{{ $conta->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($conta->status == 'AGUARDANDO_APROVACAO')
                                        <form action="{{ route('contas-bancarias.approve', $conta) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Aprovar</button>
                                        </form>
                                        <form action="{{ route('contas-bancarias.reprove', $conta) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Reprovar</button>
                                        </form>
                                    @elseif($conta->status == 'ATIVA')
                                        <form action="{{ route('contas-bancarias.reprove', $conta) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Bloquear</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma conta encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $contas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
