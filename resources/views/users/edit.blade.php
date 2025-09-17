@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-edit me-2"></i>Editar Usuário</h1>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Deixe em branco para manter a senha atual">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário *</label>
                    <select class="form-select @error('tipo_usuario') is-invalid @enderror" 
                            id="tipo_usuario" name="tipo_usuario" required>
                        <option value="">Selecione...</option>
                        <option value="pessoa_fisica" {{ old('tipo_usuario', $user->tipo_usuario) == 'pessoa_fisica' ? 'selected' : '' }}>
                            Pessoa Física
                        </option>
                        <option value="pessoa_juridica" {{ old('tipo_usuario', $user->tipo_usuario) == 'pessoa_juridica' ? 'selected' : '' }}>
                            Pessoa Jurídica
                        </option>
                    </select>
                    @error('tipo_usuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ativo" class="form-label">Status</label>
                    <select class="form-select @error('ativo') is-invalid @enderror" 
                            id="ativo" name="ativo">
                        <option value="1" {{ old('ativo', $user->ativo) == '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('ativo', $user->ativo) == '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('ativo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
