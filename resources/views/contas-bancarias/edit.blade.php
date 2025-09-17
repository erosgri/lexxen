@extends('layouts.app')

@section('title', 'Editar Conta Bancária')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-credit-card me-2"></i>Editar Conta Bancária</h1>
    <a href="{{ route('contas-bancarias.show', $contaBancaria->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('contas-bancarias.update', $contaBancaria->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Cliente</label>
                    <input type="text" class="form-control" value="{{ $contaBancaria->user->name }} ({{ $contaBancaria->user->email }})" disabled>
                    <small class="form-text text-muted">Cliente não pode ser alterado</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="agencia" class="form-label">Agência *</label>
                    <input type="text" class="form-control @error('agencia') is-invalid @enderror" 
                           id="agencia" name="agencia" value="{{ old('agencia', $contaBancaria->agencia) }}" required>
                    @error('agencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="tipo_conta" class="form-label">Tipo de Conta *</label>
                    <select class="form-select @error('tipo_conta') is-invalid @enderror" 
                            id="tipo_conta" name="tipo_conta" required>
                        <option value="">Selecione...</option>
                        <option value="corrente" {{ old('tipo_conta', $contaBancaria->tipo_conta) == 'corrente' ? 'selected' : '' }}>Conta Corrente</option>
                        <option value="poupanca" {{ old('tipo_conta', $contaBancaria->tipo_conta) == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                        <option value="salario" {{ old('tipo_conta', $contaBancaria->tipo_conta) == 'salario' ? 'selected' : '' }}>Salário</option>
                    </select>
                    @error('tipo_conta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="limite" class="form-label">Limite (R$)</label>
                    <input type="number" class="form-control @error('limite') is-invalid @enderror" 
                           id="limite" name="limite" value="{{ old('limite', $contaBancaria->limite) }}" step="0.01" min="0">
                    @error('limite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="ativa" {{ old('status', $contaBancaria->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ old('status', $contaBancaria->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="bloqueada" {{ old('status', $contaBancaria->status) == 'bloqueada' ? 'selected' : '' }}>Bloqueada</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Número da Conta</label>
                    <input type="text" class="form-control" value="{{ $contaBancaria->numero_conta }}" disabled>
                    <small class="form-text text-muted">Número da conta não pode ser alterado</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Saldo Atual</label>
                    <input type="text" class="form-control" value="{{ $contaBancaria->saldo_formatado }}" disabled>
                    <small class="form-text text-muted">Saldo não pode ser alterado por aqui</small>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('contas-bancarias.show', $contaBancaria->id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



