@extends('layouts.app')

@section('title', 'Detalhes da Pessoa Física')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user me-2"></i>Detalhes da Pessoa Física</h1>
    <div>
        <a href="{{ route('pessoa-fisica.edit', $pessoaFisica->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Editar
        </a>
        <a href="{{ route('pessoa-fisica.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações Pessoais</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nome Completo:</strong><br>
                        {{ $pessoaFisica->nome_completo }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>CPF:</strong><br>
                        {{ $pessoaFisica->cpf_formatado }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>RG:</strong><br>
                        {{ $pessoaFisica->rg ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Data de Nascimento:</strong><br>
                        {{ $pessoaFisica->data_nascimento->format('d/m/Y') }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Sexo:</strong><br>
                        @if($pessoaFisica->sexo)
                            @switch($pessoaFisica->sexo)
                                @case('M')
                                    Masculino
                                    @break
                                @case('F')
                                    Feminino
                                    @break
                                @case('O')
                                    Outro
                                    @break
                            @endswitch
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Idade:</strong><br>
                        {{ $pessoaFisica->data_nascimento->age }} anos
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Contato</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong><br>
                        {{ $pessoaFisica->user->email }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Telefone:</strong><br>
                        {{ $pessoaFisica->telefone ?? '-' }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Celular:</strong><br>
                        {{ $pessoaFisica->celular ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Endereço</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <strong>Endereço:</strong><br>
                        {{ $pessoaFisica->endereco }}, {{ $pessoaFisica->numero }}
                        @if($pessoaFisica->complemento)
                            - {{ $pessoaFisica->complemento }}
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>CEP:</strong><br>
                        {{ $pessoaFisica->cep_formatado }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Bairro:</strong><br>
                        {{ $pessoaFisica->bairro }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Cidade:</strong><br>
                        {{ $pessoaFisica->cidade }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Estado:</strong><br>
                        {{ $pessoaFisica->estado }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Conta</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Status da Conta:</strong><br>
                    @if($pessoaFisica->user->ativo)
                        <span class="badge bg-success">Ativa</span>
                    @else
                        <span class="badge bg-danger">Inativa</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Tipo de Usuário:</strong><br>
                    <span class="badge bg-info">Pessoa Física</span>
                </div>
                <div class="mb-3">
                    <strong>Cadastrado em:</strong><br>
                    {{ $pessoaFisica->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="mb-3">
                    <strong>Última atualização:</strong><br>
                    {{ $pessoaFisica->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('pessoa-fisica.edit', $pessoaFisica->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar Dados
                    </a>
                    <a href="{{ route('users.edit', $pessoaFisica->user->id) }}" class="btn btn-info">
                        <i class="fas fa-user-cog me-1"></i>Editar Conta
                    </a>
                    <form action="{{ route('pessoa-fisica.destroy', $pessoaFisica->id) }}" method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Tem certeza que deseja remover esta pessoa física?')">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


