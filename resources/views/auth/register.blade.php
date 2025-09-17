@extends('layouts.app')

@section('title', 'Registrar - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">{{ __('Registrar Nova Conta') }}</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Dados de Acesso -->
                    <h5>Dados de Acesso</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ __('Nome de Exibição') }}</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">{{ __('Senha') }}</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirmar Senha') }}</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                    <!-- Tipo de Pessoa -->
                    <h5 class="mt-4">Tipo de Conta</h5>
                    <hr>
                    <div class="mb-3">
                        <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                            <option value="">Selecione o tipo de conta...</option>
                            <option value="pessoa_fisica" {{ old('tipo_usuario') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                            <option value="pessoa_juridica" {{ old('tipo_usuario') == 'pessoa_juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                        </select>
                    </div>

                    <!-- Campos Pessoa Física -->
                    <div id="pessoa_fisica_fields" style="display: none;">
                        <h5 class="mt-4">Dados de Pessoa Física</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pf_nome_completo" class="form-label">Nome Completo</label>
                                <input id="pf_nome_completo" type="text" class="form-control" name="pf_nome_completo" value="{{ old('pf_nome_completo') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input id="cpf" type="text" class="form-control" name="cpf" value="{{ old('cpf') }}">
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rg" class="form-label">RG</label>
                                <input id="rg" type="text" class="form-control" name="rg" value="{{ old('rg') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input id="data_nascimento" type="date" class="form-control" name="data_nascimento" value="{{ old('data_nascimento') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos Pessoa Jurídica -->
                    <div id="pessoa_juridica_fields" style="display: none;">
                        <h5 class="mt-4">Dados de Pessoa Jurídica</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="razao_social" class="form-label">Razão Social</label>
                                <input id="razao_social" type="text" class="form-control" name="razao_social" value="{{ old('razao_social') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input id="cnpj" type="text" class="form-control" name="cnpj" value="{{ old('cnpj') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="representante_legal" class="form-label">Representante Legal</label>
                                <input id="representante_legal" type="text" class="form-control" name="representante_legal" value="{{ old('representante_legal') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cpf_representante" class="form-label">CPF do Representante</label>
                                <input id="cpf_representante" type="text" class="form-control" name="cpf_representante" value="{{ old('cpf_representante') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Endereço (Comum para PF e PJ) -->
                    <div id="endereco_fields" style="display: none;">
                        <h5 class="mt-4">Endereço</h5>
                        <hr>
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input id="cep" type="text" class="form-control" name="cep" value="{{ old('cep') }}">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input id="endereco" type="text" class="form-control" name="endereco" value="{{ old('endereco') }}">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input id="numero" type="text" class="form-control" name="numero" value="{{ old('numero') }}">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input id="complemento" type="text" class="form-control" name="complemento" value="{{ old('complemento') }}">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-4 mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input id="bairro" type="text" class="form-control" name="bairro" value="{{ old('bairro') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input id="cidade" type="text" class="form-control" name="cidade" value="{{ old('cidade') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input id="estado" type="text" class="form-control" name="estado" value="{{ old('estado') }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Finalizar Cadastro') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoUsuarioSelect = document.getElementById('tipo_usuario');
    const pfFields = document.getElementById('pessoa_fisica_fields');
    const pjFields = document.getElementById('pessoa_juridica_fields');
    const enderecoFields = document.getElementById('endereco_fields');

    function toggleFields() {
        const selectedValue = tipoUsuarioSelect.value;
        const showFields = selectedValue === 'pessoa_fisica' || selectedValue === 'pessoa_juridica';

        pfFields.style.display = selectedValue === 'pessoa_fisica' ? 'block' : 'none';
        pjFields.style.display = selectedValue === 'pessoa_juridica' ? 'block' : 'none';
        enderecoFields.style.display = showFields ? 'block' : 'none';
    }

    tipoUsuarioSelect.addEventListener('change', toggleFields);
    
    // Executa a função no carregamento da página para o caso de o formulário ser recarregado com erro
    toggleFields();

    // ViaCEP
    const cepInput = document.getElementById('cep');
    cepInput.addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length !== 8) {
            return;
        }

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                    document.getElementById('numero').focus(); // Foco no número
                }
            })
            .catch(error => console.error('Erro ao buscar CEP:', error));
    });
});
</script>
@endpush
