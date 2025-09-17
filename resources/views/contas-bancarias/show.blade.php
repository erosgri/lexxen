@extends('layouts.app')

@section('title', 'Detalhes da Conta Bancária')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-credit-card me-2"></i>Detalhes da Conta Bancária</h1>
    <div>
        <a href="{{ route('contas-bancarias.edit', $contaBancaria->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Editar
        </a>
        <a href="{{ route('contas-bancarias.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Conta</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Número da Conta:</strong><br>
                        {{ $contaBancaria->numero_conta }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Agência:</strong><br>
                        {{ $contaBancaria->agencia }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Tipo de Conta:</strong><br>
                        @switch($contaBancaria->tipo_conta)
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
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong><br>
                        @switch($contaBancaria->status)
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
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações Financeiras</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Saldo Atual:</strong><br>
                        <span class="h4 text-success">{{ $contaBancaria->saldo_formatado }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Limite:</strong><br>
                        <span class="h4 text-info">{{ $contaBancaria->limite_formatado }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Cliente</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Nome:</strong><br>
                    {{ $contaBancaria->user->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong><br>
                    {{ $contaBancaria->user->email }}
                </div>
                <div class="mb-3">
                    <strong>Tipo:</strong><br>
                    @if($contaBancaria->user->tipo_usuario === 'pessoa_fisica')
                        <span class="badge bg-info">Pessoa Física</span>
                    @else
                        <span class="badge bg-warning">Pessoa Jurídica</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Status de Aprovação:</strong><br>
                    @switch($contaBancaria->user->status_aprovacao)
                        @case('aprovado')
                            <span class="badge bg-success">Aprovado</span>
                            @break
                        @case('aguardando')
                            <span class="badge bg-warning">Aguardando</span>
                            @break
                        @case('reprovado')
                            <span class="badge bg-danger">Reprovado</span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('contas-bancarias.edit', $contaBancaria->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar Conta
                    </a>
                    <a href="{{ route('users.show', $contaBancaria->user->id) }}" class="btn btn-info">
                        <i class="fas fa-user me-1"></i>Ver Cliente
                    </a>
                    <form action="{{ route('contas-bancarias.destroy', $contaBancaria->id) }}" method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Tem certeza que deseja remover esta conta?')">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



