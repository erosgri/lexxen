@extends('layouts.app')

@section('title', 'Pessoas Jurídicas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-building me-2"></i>Pessoas Jurídicas</h1>
        <a href="{{ route('pessoa-juridica.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nova Pessoa Jurídica
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pessoa-juridica.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="tipo_conta" class="form-label">Filtrar por Tipo de Conta</label>
                    <select name="tipo_conta" id="tipo_conta" class="form-select">
                        <option value="">Todas</option>
                        <option value="corrente" {{ request('tipo_conta') == 'corrente' ? 'selected' : '' }}>Corrente</option>
                        <option value="salario" {{ request('tipo_conta') == 'salario' ? 'selected' : '' }}>Salário</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('pessoa-juridica.index') }}" class="btn btn-secondary ms-2">Limpar</a>
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
                            <th>Razão Social</th>
                            <th>CNPJ</th>
                            <th>Email</th>
                            <th>Contas</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pessoasJuridicas as $pessoa)
                            <tr>
                                <td>{{ $pessoa->id }}</td>
                                <td>{{ $pessoa->razao_social }}</td>
                                <td>{{ $pessoa->cnpj }}</td>
                                <td>{{ $pessoa->user->email }}</td>
                                <td>
                                    @if($pessoa->contas_info->isNotEmpty())
                                        @foreach($pessoa->contas_info as $tipo => $quantidade)
                                            <span class="badge bg-secondary">{{ ucfirst($tipo) }}: {{ $quantidade }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-light text-dark">Nenhuma</span>
                                    @endif
                                </td>
                                <td>{{ $pessoa->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('pessoa-juridica.show', $pessoa->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pessoa-juridica.edit', $pessoa->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pessoa-juridica.destroy', $pessoa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover?')">
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
                                <td colspan="7" class="text-center">Nenhuma pessoa jurídica encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection




