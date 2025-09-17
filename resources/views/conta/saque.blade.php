@extends('layouts.app')

@section('title', 'Realizar Saque - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-money-bill-wave me-2"></i>Realizar Saque</h4>
                    <a href="{{ route('home') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Saldo Atual:</strong> {{ $conta->getSaldoFormatadoAttribute() }}
                    </div>

                    <form method="POST" action="{{ route('conta.saque.store', ['conta' => $conta->id]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor do Saque (R$)</label>
                            <input type="number" class="form-control" id="valor" name="valor" 
                                   min="0.01" step="0.01" max="{{ $conta->saldo }}" 
                                   placeholder="0,00" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (Opcional)</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" 
                                   placeholder="Ex: Saque para despesas">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Confirmar Saque
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

