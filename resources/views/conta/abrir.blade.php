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
                                <option value="corrente">Conta Corrente</option>
                                <option value="poupanca">Conta Poupança</option>
                                <option value="empresarial">Conta Empresarial</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle me-1"></i> A nova carteira usará os seus dados cadastrais existentes e será aberta com saldo inicial de R$ 0,00. Você pode escolher entre Conta Corrente, Conta Poupança ou Conta Empresarial.</p>
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


