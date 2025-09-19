@extends('layouts.app')

@section('title', 'Detalhes da Pessoa Jurídica')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-building me-2"></i>Detalhes da Pessoa Jurídica</h1>
    <div>
        <a href="{{ route('pessoa-juridica.edit', $pessoaJuridica->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Editar
        </a>
        <a href="{{ route('pessoa-juridica.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Empresa</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Razão Social:</strong><br>
                        {{ $pessoaJuridica->razao_social }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Nome Fantasia:</strong><br>
                        {{ $pessoaJuridica->nome_fantasia ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>CNPJ:</strong><br>
                        {{ $pessoaJuridica->cnpj_formatado }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Inscrição Estadual:</strong><br>
                        {{ $pessoaJuridica->inscricao_estadual ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Inscrição Municipal:</strong><br>
                        {{ $pessoaJuridica->inscricao_municipal ?? '-' }}
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
                        {{ $pessoaJuridica->user->email }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Telefone:</strong><br>
                        {{ $pessoaJuridica->telefone ?? '-' }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>Celular:</strong><br>
                        {{ $pessoaJuridica->celular ?? '-' }}
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
                        {{ $pessoaJuridica->endereco }}, {{ $pessoaJuridica->numero }}
                        @if($pessoaJuridica->complemento)
                            - {{ $pessoaJuridica->complemento }}
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>CEP:</strong><br>
                        {{ $pessoaJuridica->cep_formatado }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Bairro:</strong><br>
                        {{ $pessoaJuridica->bairro }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Cidade:</strong><br>
                        {{ $pessoaJuridica->cidade }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Estado:</strong><br>
                        {{ $pessoaJuridica->estado }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Representante Legal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nome:</strong><br>
                        {{ $pessoaJuridica->representante_legal }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>CPF:</strong><br>
                        {{ $pessoaJuridica->cpf_representante_formatado }}
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
                    @if($pessoaJuridica->user->ativo)
                        <span class="badge bg-success">Ativa</span>
                    @else
                        <span class="badge bg-danger">Inativa</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Tipo de Usuário:</strong><br>
                    <span class="badge bg-warning">Pessoa Jurídica</span>
                </div>
                <div class="mb-3">
                    <strong>Cadastrado em:</strong><br>
                    {{ $pessoaJuridica->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="mb-3">
                    <strong>Última atualização:</strong><br>
                    {{ $pessoaJuridica->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('pessoa-juridica.edit', $pessoaJuridica->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar Dados
                    </a>
                    <a href="{{ route('users.edit', $pessoaJuridica->user->id) }}" class="btn btn-info">
                        <i class="fas fa-user-cog me-1"></i>Editar Conta
                    </a>
                    <form action="{{ route('pessoa-juridica.destroy', $pessoaJuridica->id) }}" method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Tem certeza que deseja remover esta pessoa jurídica?')">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection














