@extends('layouts.app')

@section('title', 'Editar Pessoa Física')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-edit me-2"></i>Editar Pessoa Física</h1>
    <a href="{{ route('pessoa-fisica.show', $pessoaFisica->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pessoa-fisica.update', $pessoaFisica->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome_completo" class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control @error('nome_completo') is-invalid @enderror" 
                           id="nome_completo" name="nome_completo" value="{{ old('nome_completo', $pessoaFisica->nome_completo) }}" required>
                    @error('nome_completo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email (Usuário)</label>
                    <input type="email" class="form-control" value="{{ $pessoaFisica->user->email }}" disabled>
                    <small class="form-text text-muted">Para alterar o email, edite a conta do usuário</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cpf" class="form-label">CPF *</label>
                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                           id="cpf" name="cpf" value="{{ old('cpf', $pessoaFisica->cpf) }}" maxlength="11" required>
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="rg" class="form-label">RG</label>
                    <input type="text" class="form-control @error('rg') is-invalid @enderror" 
                           id="rg" name="rg" value="{{ old('rg', $pessoaFisica->rg) }}" maxlength="20">
                    @error('rg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                    <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" 
                           id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $pessoaFisica->data_nascimento->format('Y-m-d')) }}" required>
                    @error('data_nascimento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select class="form-select @error('sexo') is-invalid @enderror" 
                            id="sexo" name="sexo">
                        <option value="">Selecione...</option>
                        <option value="M" {{ old('sexo', $pessoaFisica->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $pessoaFisica->sexo) == 'F' ? 'selected' : '' }}>Feminino</option>
                        <option value="O" {{ old('sexo', $pessoaFisica->sexo) == 'O' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('sexo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                           id="telefone" name="telefone" value="{{ old('telefone', $pessoaFisica->telefone) }}" maxlength="15">
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="text" class="form-control @error('celular') is-invalid @enderror" 
                           id="celular" name="celular" value="{{ old('celular', $pessoaFisica->celular) }}" maxlength="15">
                    @error('celular')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                           id="cep" name="cep" value="{{ old('cep', $pessoaFisica->cep) }}" maxlength="8" required>
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="endereco" class="form-label">Endereço *</label>
                    <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                           id="endereco" name="endereco" value="{{ old('endereco', $pessoaFisica->endereco) }}" required>
                    @error('endereco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                           id="numero" name="numero" value="{{ old('numero', $pessoaFisica->numero) }}" maxlength="10" required>
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
                           id="complemento" name="complemento" value="{{ old('complemento', $pessoaFisica->complemento) }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="bairro" class="form-label">Bairro *</label>
                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
                           id="bairro" name="bairro" value="{{ old('bairro', $pessoaFisica->bairro) }}" required>
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cidade" class="form-label">Cidade *</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                           id="cidade" name="cidade" value="{{ old('cidade', $pessoaFisica->cidade) }}" required>
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado *</label>
                    <select class="form-select @error('estado') is-invalid @enderror" 
                            id="estado" name="estado" required>
                        <option value="">Selecione...</option>
                        <option value="AC" {{ old('estado', $pessoaFisica->estado) == 'AC' ? 'selected' : '' }}>Acre</option>
                        <option value="AL" {{ old('estado', $pessoaFisica->estado) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                        <option value="AP" {{ old('estado', $pessoaFisica->estado) == 'AP' ? 'selected' : '' }}>Amapá</option>
                        <option value="AM" {{ old('estado', $pessoaFisica->estado) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                        <option value="BA" {{ old('estado', $pessoaFisica->estado) == 'BA' ? 'selected' : '' }}>Bahia</option>
                        <option value="CE" {{ old('estado', $pessoaFisica->estado) == 'CE' ? 'selected' : '' }}>Ceará</option>
                        <option value="DF" {{ old('estado', $pessoaFisica->estado) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                        <option value="ES" {{ old('estado', $pessoaFisica->estado) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                        <option value="GO" {{ old('estado', $pessoaFisica->estado) == 'GO' ? 'selected' : '' }}>Goiás</option>
                        <option value="MA" {{ old('estado', $pessoaFisica->estado) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                        <option value="MT" {{ old('estado', $pessoaFisica->estado) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                        <option value="MS" {{ old('estado', $pessoaFisica->estado) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                        <option value="MG" {{ old('estado', $pessoaFisica->estado) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                        <option value="PA" {{ old('estado', $pessoaFisica->estado) == 'PA' ? 'selected' : '' }}>Pará</option>
                        <option value="PB" {{ old('estado', $pessoaFisica->estado) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                        <option value="PR" {{ old('estado', $pessoaFisica->estado) == 'PR' ? 'selected' : '' }}>Paraná</option>
                        <option value="PE" {{ old('estado', $pessoaFisica->estado) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                        <option value="PI" {{ old('estado', $pessoaFisica->estado) == 'PI' ? 'selected' : '' }}>Piauí</option>
                        <option value="RJ" {{ old('estado', $pessoaFisica->estado) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                        <option value="RN" {{ old('estado', $pessoaFisica->estado) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                        <option value="RS" {{ old('estado', $pessoaFisica->estado) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                        <option value="RO" {{ old('estado', $pessoaFisica->estado) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                        <option value="RR" {{ old('estado', $pessoaFisica->estado) == 'RR' ? 'selected' : '' }}>Roraima</option>
                        <option value="SC" {{ old('estado', $pessoaFisica->estado) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                        <option value="SP" {{ old('estado', $pessoaFisica->estado) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                        <option value="SE" {{ old('estado', $pessoaFisica->estado) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                        <option value="TO" {{ old('estado', $pessoaFisica->estado) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('pessoa-fisica.show', $pessoaFisica->id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });

    // Máscara para CEP
    document.getElementById('cep').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });

    // Máscara para telefone
    document.getElementById('telefone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });

    // Máscara para celular
    document.getElementById('celular').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });
</script>
@endsection


