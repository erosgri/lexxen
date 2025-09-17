@extends('layouts.app')

@section('title', 'Nova Conta Bancária')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-credit-card me-2"></i>Nova Conta Bancária</h1>
    <a href="{{ route('contas-bancarias.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('contas-bancarias.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Cliente *</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" 
                            id="user_id" name="user_id" required>
                        <option value="">Selecione um cliente...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="agencia" class="form-label">Agência *</label>
                    <input type="text" class="form-control @error('agencia') is-invalid @enderror" 
                           id="agencia" name="agencia" value="{{ old('agencia') }}" required>
                    @error('agencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_conta" class="form-label">Tipo de Conta *</label>
                    <select class="form-select @error('tipo_conta') is-invalid @enderror" 
                            id="tipo_conta" name="tipo_conta" required>
                        <option value="">Selecione...</option>
                        <option value="corrente" {{ old('tipo_conta') == 'corrente' ? 'selected' : '' }}>Conta Corrente</option>
                        <option value="poupanca" {{ old('tipo_conta') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                        <option value="salario" {{ old('tipo_conta') == 'salario' ? 'selected' : '' }}>Salário</option>
                    </select>
                    @error('tipo_conta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="limite" class="form-label">Limite (R$)</label>
                    <input type="number" class="form-control @error('limite') is-invalid @enderror" 
                           id="limite" name="limite" value="{{ old('limite', 0) }}" step="0.01" min="0">
                    @error('limite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('contas-bancarias.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Criar Conta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



