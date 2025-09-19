@extends('layouts.app')

@section('title', 'Pessoas Físicas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user me-2"></i>Pessoas Físicas</h1>
        <a href="{{ route('pessoa-fisica.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nova Pessoa Física
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pessoa-fisica.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="tipo_conta" class="form-label">Filtrar por Tipo de Conta</label>
                    <select name="tipo_conta" id="tipo_conta" class="form-select">
                        <option value="">Todas</option>
                        <option value="corrente" {{ request('tipo_conta') == 'corrente' ? 'selected' : '' }}>Corrente</option>
                        <option value="poupanca" {{ request('tipo_conta') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('pessoa-fisica.index') }}" class="btn btn-secondary ms-2">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome Completo</th>
                            <th>CPF</th>
                            <th>Email</th>
                            <th>Contas</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pessoasFisicas as $pessoa)
                            <tr>
                                <td>{{ $pessoa->id }}</td>
                                <td>{{ $pessoa->nome_completo }}</td>
                                <td>{{ $pessoa->cpf }}</td>
                                <td>{{ $pessoa->user->email }}</td>
                                <td>
                                    @if($pessoa->contas_info->isNotEmpty())
                                        @foreach($pessoa->contas_info as $tipo => $quantidade)
                                            @switch($tipo)
                                                @case('corrente')
                                                    <span class="badge bg-primary me-1">
                                                        <i class="fas fa-credit-card me-1"></i>Conta Corrente: {{ $quantidade }}
                                                    </span>
                                                    @break
                                                @case('poupanca')
                                                    <span class="badge bg-info me-1">
                                                        <i class="fas fa-piggy-bank me-1"></i>Conta Poupança: {{ $quantidade }}
                                                    </span>
                                                    @break
                                                @case('empresarial')
                                                    <span class="badge bg-warning text-dark me-1">
                                                        <i class="fas fa-building me-1"></i>Conta Empresarial
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary me-1">{{ ucfirst($tipo) }}: {{ $quantidade }}</span>
                                            @endswitch
                                        @endforeach
                                    @else
                                        <span class="badge bg-light text-dark">Nenhuma</span>
                                    @endif
                                </td>
                                <td>{{ $pessoa->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('pessoa-fisica.show', $pessoa->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pessoa-fisica.edit', $pessoa->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pessoa-fisica.destroy', $pessoa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma pessoa física encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
