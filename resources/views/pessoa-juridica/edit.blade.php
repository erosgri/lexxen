@extends('layouts.app')

@section('title', 'Editar Pessoa Jurídica')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-building me-2"></i>Editar Pessoa Jurídica</h1>
    <a href="{{ route('pessoa-juridica.show', $pessoaJuridica->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pessoa-juridica.update', $pessoaJuridica->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="razao_social" class="form-label">Razão Social *</label>
                    <input type="text" class="form-control @error('razao_social') is-invalid @enderror" 
                           id="razao_social" name="razao_social" value="{{ old('razao_social', $pessoaJuridica->razao_social) }}" required>
                    @error('razao_social')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email (Usuário)</label>
                    <input type="email" class="form-control" value="{{ $pessoaJuridica->user->email }}" disabled>
                    <small class="form-text text-muted">Para alterar o email, edite a conta do usuário</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                    <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror" 
                           id="nome_fantasia" name="nome_fantasia" value="{{ old('nome_fantasia', $pessoaJuridica->nome_fantasia) }}">
                    @error('nome_fantasia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cnpj" class="form-label">CNPJ *</label>
                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                           id="cnpj" name="cnpj" value="{{ old('cnpj', $pessoaJuridica->cnpj) }}" maxlength="14" required>
                    @error('cnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
                    <input type="text" class="form-control @error('inscricao_estadual') is-invalid @enderror" 
                           id="inscricao_estadual" name="inscricao_estadual" value="{{ old('inscricao_estadual', $pessoaJuridica->inscricao_estadual) }}" maxlength="20">
                    @error('inscricao_estadual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="inscricao_municipal" class="form-label">Inscrição Municipal</label>
                    <input type="text" class="form-control @error('inscricao_municipal') is-invalid @enderror" 
                           id="inscricao_municipal" name="inscricao_municipal" value="{{ old('inscricao_municipal', $pessoaJuridica->inscricao_municipal) }}" maxlength="20">
                    @error('inscricao_municipal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                           id="cep" name="cep" value="{{ old('cep', $pessoaJuridica->cep) }}" maxlength="8" required>
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                           id="telefone" name="telefone" value="{{ old('telefone', $pessoaJuridica->telefone) }}" maxlength="15">
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="text" class="form-control @error('celular') is-invalid @enderror" 
                           id="celular" name="celular" value="{{ old('celular', $pessoaJuridica->celular) }}" maxlength="15">
                    @error('celular')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="endereco" class="form-label">Endereço *</label>
                    <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                           id="endereco" name="endereco" value="{{ old('endereco', $pessoaJuridica->endereco) }}" required>
                    @error('endereco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label for="numero" class="form-label">Número *</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                           id="numero" name="numero" value="{{ old('numero', $pessoaJuridica->numero) }}" maxlength="10" required>
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control @error('complemento') is-invalid @enderror" 
                           id="complemento" name="complemento" value="{{ old('complemento', $pessoaJuridica->complemento) }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="bairro" class="form-label">Bairro *</label>
                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" 
                           id="bairro" name="bairro" value="{{ old('bairro', $pessoaJuridica->bairro) }}" required>
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="cidade" class="form-label">Cidade *</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" 
                           id="cidade" name="cidade" value="{{ old('cidade', $pessoaJuridica->cidade) }}" required>
                    @error('cidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="estado" class="form-label">Estado *</label>
                    <select class="form-select @error('estado') is-invalid @enderror" 
                            id="estado" name="estado" required>
                        <option value="">UF</option>
                        <option value="AC" {{ old('estado', $pessoaJuridica->estado) == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="AL" {{ old('estado', $pessoaJuridica->estado) == 'AL' ? 'selected' : '' }}>AL</option>
                        <option value="AP" {{ old('estado', $pessoaJuridica->estado) == 'AP' ? 'selected' : '' }}>AP</option>
                        <option value="AM" {{ old('estado', $pessoaJuridica->estado) == 'AM' ? 'selected' : '' }}>AM</option>
                        <option value="BA" {{ old('estado', $pessoaJuridica->estado) == 'BA' ? 'selected' : '' }}>BA</option>
                        <option value="CE" {{ old('estado', $pessoaJuridica->estado) == 'CE' ? 'selected' : '' }}>CE</option>
                        <option value="DF" {{ old('estado', $pessoaJuridica->estado) == 'DF' ? 'selected' : '' }}>DF</option>
                        <option value="ES" {{ old('estado', $pessoaJuridica->estado) == 'ES' ? 'selected' : '' }}>ES</option>
                        <option value="GO" {{ old('estado', $pessoaJuridica->estado) == 'GO' ? 'selected' : '' }}>GO</option>
                        <option value="MA" {{ old('estado', $pessoaJuridica->estado) == 'MA' ? 'selected' : '' }}>MA</option>
                        <option value="MT" {{ old('estado', $pessoaJuridica->estado) == 'MT' ? 'selected' : '' }}>MT</option>
                        <option value="MS" {{ old('estado', $pessoaJuridica->estado) == 'MS' ? 'selected' : '' }}>MS</option>
                        <option value="MG" {{ old('estado', $pessoaJuridica->estado) == 'MG' ? 'selected' : '' }}>MG</option>
                        <option value="PA" {{ old('estado', $pessoaJuridica->estado) == 'PA' ? 'selected' : '' }}>PA</option>
                        <option value="PB" {{ old('estado', $pessoaJuridica->estado) == 'PB' ? 'selected' : '' }}>PB</option>
                        <option value="PR" {{ old('estado', $pessoaJuridica->estado) == 'PR' ? 'selected' : '' }}>PR</option>
                        <option value="PE" {{ old('estado', $pessoaJuridica->estado) == 'PE' ? 'selected' : '' }}>PE</option>
                        <option value="PI" {{ old('estado', $pessoaJuridica->estado) == 'PI' ? 'selected' : '' }}>PI</option>
                        <option value="RJ" {{ old('estado', $pessoaJuridica->estado) == 'RJ' ? 'selected' : '' }}>RJ</option>
                        <option value="RN" {{ old('estado', $pessoaJuridica->estado) == 'RN' ? 'selected' : '' }}>RN</option>
                        <option value="RS" {{ old('estado', $pessoaJuridica->estado) == 'RS' ? 'selected' : '' }}>RS</option>
                        <option value="RO" {{ old('estado', $pessoaJuridica->estado) == 'RO' ? 'selected' : '' }}>RO</option>
                        <option value="RR" {{ old('estado', $pessoaJuridica->estado) == 'RR' ? 'selected' : '' }}>RR</option>
                        <option value="SC" {{ old('estado', $pessoaJuridica->estado) == 'SC' ? 'selected' : '' }}>SC</option>
                        <option value="SP" {{ old('estado', $pessoaJuridica->estado) == 'SP' ? 'selected' : '' }}>SP</option>
                        <option value="SE" {{ old('estado', $pessoaJuridica->estado) == 'SE' ? 'selected' : '' }}>SE</option>
                        <option value="TO" {{ old('estado', $pessoaJuridica->estado) == 'TO' ? 'selected' : '' }}>TO</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <hr>
            <h5 class="mb-3">Representante Legal</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="representante_legal" class="form-label">Nome do Representante *</label>
                    <input type="text" class="form-control @error('representante_legal') is-invalid @enderror" 
                           id="representante_legal" name="representante_legal" value="{{ old('representante_legal', $pessoaJuridica->representante_legal) }}" required>
                    @error('representante_legal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cpf_representante" class="form-label">CPF do Representante *</label>
                    <input type="text" class="form-control @error('cpf_representante') is-invalid @enderror" 
                           id="cpf_representante" name="cpf_representante" value="{{ old('cpf_representante', $pessoaJuridica->cpf_representante) }}" maxlength="11" required>
                    @error('cpf_representante')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('pessoa-juridica.show', $pessoaJuridica->id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Máscara para CNPJ
    document.getElementById('cnpj').addEventListener('input', function(e) {
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

    // Máscara para CPF do representante
    document.getElementById('cpf_representante').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });
</script>
@endsection









