@extends('layouts.app')

@section('title', 'Abrir Nova Carteira - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus-circle me-2"></i>Abrir Nova Carteira</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('conta.abrir.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="tipo_conta" class="form-label">Escolha o tipo da nova carteira:</label>
                            <select name="tipo_conta" id="tipo_conta" class="form-select" required>
                                <option value="">Selecione...</option>
                                <optgroup label="Carteiras Pessoa Física">
                                    <option value="corrente_pf">Carteira Corrente PF</option>
                                    <option value="poupanca_pf">Carteira Poupança PF</option>
                                </optgroup>
                                <optgroup label="Carteiras Pessoa Jurídica">
                                    <option value="corrente_pj">Carteira Corrente PJ</option>
                                    <option value="poupanca_pj">Carteira Poupança PJ</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle me-1"></i> A nova carteira usará os seus dados cadastrais existentes e será aberta com saldo inicial de R$ 0,00. Você pode escolher entre carteiras para Pessoa Física (PF) ou Pessoa Jurídica (PJ).</p>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Confirmar Abertura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


