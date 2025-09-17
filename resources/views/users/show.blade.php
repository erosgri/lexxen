@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user me-2"></i>Detalhes do Usuário</h1>
    <div>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Editar
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                        <strong>Nome:</strong><br>
                        {{ $user->name }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong><br>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Tipo de Usuário:</strong><br>
                        @if($user->tipo_usuario === 'pessoa_fisica')
                            <span class="badge bg-info">Pessoa Física</span>
                        @else
                            <span class="badge bg-warning">Pessoa Jurídica</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Status de Aprovação:</strong><br>
                        @switch($user->status_aprovacao)
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
                    <div class="col-md-4 mb-3">
                        <strong>Status da Conta:</strong><br>
                        @if($user->ativo)
                            <span class="badge bg-success">Ativa</span>
                        @else
                            <span class="badge bg-danger">Inativa</span>
                        @endif
                    </div>
                </div>

                @if($user->isReprovado() && $user->motivo_reprovacao)
                <div class="alert alert-danger">
                    <strong>Motivo da Reprovação:</strong><br>
                    {{ $user->motivo_reprovacao }}
                </div>
                @endif
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Cadastrado em:</strong><br>
                        {{ $user->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Última atualização:</strong><br>
                        {{ $user->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
        
        @if($user->pessoaFisica)
        <div class="card mt-4">
                <div class="card-header">
                <h5 class="card-title mb-0">Dados de Pessoa Física</h5>
                </div>
                <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nome Completo:</strong><br>
                        {{ $user->pessoaFisica->nome_completo }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>CPF:</strong><br>
                        {{ $user->pessoaFisica->cpf_formatado }}
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong>RG:</strong><br>
                        {{ $user->pessoaFisica->rg ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Data de Nascimento:</strong><br>
                        {{ $user->pessoaFisica->data_nascimento->format('d/m/Y') }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Sexo:</strong><br>
                        @if($user->pessoaFisica->sexo)
                            @switch($user->pessoaFisica->sexo)
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
                        {{ $user->pessoaFisica->data_nascimento->age }} anos
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Telefone:</strong><br>
                        {{ $user->pessoaFisica->telefone ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Celular:</strong><br>
                        {{ $user->pessoaFisica->celular ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>CEP:</strong><br>
                        {{ $user->pessoaFisica->cep_formatado }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <strong>Endereço:</strong><br>
                        {{ $user->pessoaFisica->endereco }}, {{ $user->pessoaFisica->numero }}
                        @if($user->pessoaFisica->complemento)
                            - {{ $user->pessoaFisica->complemento }}
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Bairro:</strong><br>
                        {{ $user->pessoaFisica->bairro }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Cidade:</strong><br>
                        {{ $user->pessoaFisica->cidade }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Estado:</strong><br>
                        {{ $user->pessoaFisica->estado }}
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('pessoa-fisica.show', $user->pessoaFisica->id) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>Ver Detalhes Completos
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($user->pessoaJuridica)
        <div class="card mt-4">
                <div class="card-header">
                <h5 class="card-title mb-0">Dados de Pessoa Jurídica</h5>
                </div>
                <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Razão Social:</strong><br>
                        {{ $user->pessoaJuridica->razao_social }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Nome Fantasia:</strong><br>
                        {{ $user->pessoaJuridica->nome_fantasia ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>CNPJ:</strong><br>
                        {{ $user->pessoaJuridica->cnpj_formatado }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Inscrição Estadual:</strong><br>
                        {{ $user->pessoaJuridica->inscricao_estadual ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Inscrição Municipal:</strong><br>
                        {{ $user->pessoaJuridica->inscricao_municipal ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Telefone:</strong><br>
                        {{ $user->pessoaJuridica->telefone ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Celular:</strong><br>
                        {{ $user->pessoaJuridica->celular ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>CEP:</strong><br>
                        {{ $user->pessoaJuridica->cep_formatado }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <strong>Endereço:</strong><br>
                        {{ $user->pessoaJuridica->endereco }}, {{ $user->pessoaJuridica->numero }}
                        @if($user->pessoaJuridica->complemento)
                            - {{ $user->pessoaJuridica->complemento }}
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Bairro:</strong><br>
                        {{ $user->pessoaJuridica->bairro }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Cidade:</strong><br>
                        {{ $user->pessoaJuridica->cidade }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Estado:</strong><br>
                        {{ $user->pessoaJuridica->estado }}
                    </div>
                </div>
                <hr>
                <h6>Representante Legal</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nome:</strong><br>
                        {{ $user->pessoaJuridica->representante_legal }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>CPF:</strong><br>
                        {{ $user->pessoaJuridica->cpf_representante_formatado }}
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('pessoa-juridica.show', $user->pessoaJuridica->id) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>Ver Detalhes Completos
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
            <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar Usuário
                    </a>
                    @if($user->tipo_usuario === 'pessoa_fisica' && !$user->pessoaFisica)
                        <a href="{{ route('pessoa-fisica.create') }}?user_id={{ $user->id }}" class="btn btn-info">
                            <i class="fas fa-user-plus me-1"></i>Completar Dados PF
                        </a>
                    @elseif($user->tipo_usuario === 'pessoa_juridica' && !$user->pessoaJuridica)
                        <a href="{{ route('pessoa-juridica.create') }}?user_id={{ $user->id }}" class="btn btn-info">
                            <i class="fas fa-building me-1"></i>Completar Dados PJ
                        </a>
                    @endif
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </form>
                </div>
                </div>
            </div>
    </div>
</div>
@endsection