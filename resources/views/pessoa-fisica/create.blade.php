@extends('layouts.app')

@section('title', 'Nova Pessoa Física')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-plus me-2"></i>Nova Pessoa Física</h1>
    <a href="{{ route('pessoa-fisica.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pessoa-fisica.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Usuário *</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" 
                            id="user_id" name="user_id" required>
                        <option value="">Selecione um usuário...</option>
                        @foreach($users as $user)
                            @if($user->tipo_usuario === 'pessoa_fisica' && !$user->pessoaFisica)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->email }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nome_completo" class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control @error('nome_completo') is-invalid @enderror" 
                           id="nome_completo" name="nome_completo" value="{{ old('nome_completo') }}" required>
                    @error('nome_completo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cpf" class="form-label">CPF *</label>
                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                           id="cpf" name="cpf" value="{{ old('cpf') }}" maxlength="11" required>
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="rg" class="form-label">RG</label>
                    <input type="text" class="form-control @error('rg') is-invalid @enderror" 
                           id="rg" name="rg" value="{{ old('rg') }}" maxlength="20">
                    @error('rg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                    <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" 
                           id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" required>
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
                        <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Feminino</option>
                        <option value="O" {{ old('sexo') == 'O' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('sexo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                           id="telefone" name="telefone" value="{{ old('telefone') }}" maxlength="15">
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="text" class="form-control @error('celular') is-invalid @enderror" 
                           id="celular" name="celular" value="{{ old('celular') }}" maxlength="15">
                    @error('celular')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                           id="cep" name="cep" value="{{ old('cep') }}" maxlength="8" required>
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="endereco" class="form-label">Endereço *</label>
                    <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                           id="endereco" name="endereco" value="{{ old('endereco') }}" required>
                    @error('endereco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                           id="numero" name="numero" value="{{ old('numero') }}" maxlength="10" required>
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
                           id="complemento" name="complemento" value="{{ old('complemento') }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="bairro" class="form-label">Bairro *</label>
                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
                           id="bairro" name="bairro" value="{{ old('bairro') }}" required>
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cidade" class="form-label">Cidade *</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                           id="cidade" name="cidade" value="{{ old('cidade') }}" required>
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado *</label>
                    <select class="form-select @error('estado') is-invalid @enderror" 
                            id="estado" name="estado" required>
                        <option value="">Selecione...</option>
                        <option value="AC" {{ old('estado') == 'AC' ? 'selected' : '' }}>Acre</option>
                        <option value="AL" {{ old('estado') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                        <option value="AP" {{ old('estado') == 'AP' ? 'selected' : '' }}>Amapá</option>
                        <option value="AM" {{ old('estado') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                        <option value="BA" {{ old('estado') == 'BA' ? 'selected' : '' }}>Bahia</option>
                        <option value="CE" {{ old('estado') == 'CE' ? 'selected' : '' }}>Ceará</option>
                        <option value="DF" {{ old('estado') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                        <option value="ES" {{ old('estado') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                        <option value="GO" {{ old('estado') == 'GO' ? 'selected' : '' }}>Goiás</option>
                        <option value="MA" {{ old('estado') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                        <option value="MT" {{ old('estado') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                        <option value="MS" {{ old('estado') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                        <option value="MG" {{ old('estado') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                        <option value="PA" {{ old('estado') == 'PA' ? 'selected' : '' }}>Pará</option>
                        <option value="PB" {{ old('estado') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                        <option value="PR" {{ old('estado') == 'PR' ? 'selected' : '' }}>Paraná</option>
                        <option value="PE" {{ old('estado') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                        <option value="PI" {{ old('estado') == 'PI' ? 'selected' : '' }}>Piauí</option>
                        <option value="RJ" {{ old('estado') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                        <option value="RN" {{ old('estado') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                        <option value="RS" {{ old('estado') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                        <option value="RO" {{ old('estado') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                        <option value="RR" {{ old('estado') == 'RR' ? 'selected' : '' }}>Roraima</option>
                        <option value="SC" {{ old('estado') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                        <option value="SP" {{ old('estado') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                        <option value="SE" {{ old('estado') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                        <option value="TO" {{ old('estado') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('pessoa-fisica.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar
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


