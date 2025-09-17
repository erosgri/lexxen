@extends('layouts.app')

@section('title', 'Gerenciar Contas Bancárias - ' . config('app.name'))

@section('content')
<div class="container">
    <h2>Gerenciar Contas Bancárias</h2>

    <!-- Filtros -->
    <div class="mb-3">
        <a href="{{ route('contas-bancarias.index') }}" class="btn btn-secondary">Todas</a>
        <a href="{{ route('contas-bancarias.index', ['status' => 'AGUARDANDO_APROVACAO']) }}" class="btn btn-warning">Aguardando Aprovação</a>
        <a href="{{ route('contas-bancarias.index', ['status' => 'ATIVA']) }}" class="btn btn-success">Ativas</a>
        <a href="{{ route('contas-bancarias.index', ['status' => 'BLOQUEADA']) }}" class="btn btn-danger">Bloqueadas</a>
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
                            <th class="text-end">Saldo</th>
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
                                <td>{{ ucfirst($conta->tipo_conta) }}</td>
                                <td class="text-end fw-bold">{{ $conta->getSaldoFormatadoAttribute() }}</td>
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
                                <td colspan="7" class="text-center">Nenhuma conta encontrada.</td>
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
