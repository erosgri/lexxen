@extends('layouts.app')

@section('title', 'Realizar Transferência - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-exchange-alt me-2"></i>Realizar Transferência</h4>
                    <a href="{{ route('home') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Saldo Atual:</strong> {{ $conta->getSaldoFormatadoAttribute() }}
                    </div>

                    <form method="POST" action="{{ route('conta.transferencia.store', ['conta' => $conta->id]) }}">
                        @csrf
                        <h5>Dados da Conta de Destino</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agencia_destino" class="form-label">Agência</label>
                                <input type="text" class="form-control" id="agencia_destino" name="agencia_destino" 
                                       placeholder="0000" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="conta_destino" class="form-label">Número da Conta</label>
                                <input type="text" class="form-control" id="conta_destino" name="conta_destino" 
                                       placeholder="000000-0" required>
                            </div>
                        </div>

                        <h5 class="mt-3">Valor</h5>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor da Transferência (R$)</label>
                            <input type="number" class="form-control" id="valor" name="valor" 
                                   min="0.01" step="0.01" max="{{ $conta->saldo }}" 
                                   placeholder="0,00" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Confirmar Transferência
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

