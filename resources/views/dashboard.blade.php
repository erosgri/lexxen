@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard - {{ config('app.name') }}</h1>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalUsers }}</h4>
                        <p class="card-text">Total de Usuários</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('users.index') }}" class="text-white text-decoration-none">
                    Ver todos <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $usersAprovados }}</h4>
                        <p class="card-text">Usuários Aprovados</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">
                    {{ $totalUsers > 0 ? round(($usersAprovados / $totalUsers) * 100, 1) : 0 }}% do total
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $usersAguardando }}</h4>
                        <p class="card-text">Aguardando Aprovação</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">
                    {{ $totalUsers > 0 ? round(($usersAguardando / $totalUsers) * 100, 1) : 0 }}% do total
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalContasBancarias }}</h4>
                        <p class="card-text">Contas Bancárias</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('contas-bancarias.index') }}" class="text-white text-decoration-none">
                    Ver todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Segunda linha de cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalPessoaFisica }}</h4>
                        <p class="card-text">Pessoas Físicas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('pessoa-fisica.index') }}" class="text-white text-decoration-none">
                    Ver todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $totalPessoaJuridica }}</h4>
                        <p class="card-text">Pessoas Jurídicas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('pessoa-juridica.index') }}" class="text-white text-decoration-none">
                    Ver todas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $contasBloqueadas }}</h4>
                        <p class="card-text">Contas Bloqueadas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-ban fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">
                    {{ $totalContasBancarias > 0 ? round(($contasBloqueadas / $totalContasBancarias) * 100, 1) : 0 }}% do total
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $usersReprovadosBloqueados }}</h4>
                        <p class="card-text">Reprovados e Bloqueados</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-slash fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">
                    {{ $totalUsers > 0 ? round(($usersReprovadosBloqueados / $totalUsers) * 100, 1) : 0 }}% do total
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-user-plus me-2"></i>
                            Novo Usuário
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('pessoa-fisica.create') }}" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-user me-2"></i>
                            Nova Pessoa Física
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('pessoa-juridica.create') }}" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-building me-2"></i>
                            Nova Pessoa Jurídica
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumo dos Dados -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Distribuição de Usuários
                </h5>
            </div>
            <div class="card-body">
                <canvas id="userDistributionChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Status de Aprovação dos Usuários
                </h5>
            </div>
            <div class="card-body">
                <canvas id="userStatusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Distribuição de Usuários
    const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
    new Chart(userDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pessoas Físicas', 'Pessoas Jurídicas'],
            datasets: [{
                data: [{{ $totalPessoaFisica }}, {{ $totalPessoaJuridica }}],
                backgroundColor: [
                    '#17a2b8',
                    '#ffc107'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Status de Aprovação dos Usuários
    const userStatusCtx = document.getElementById('userStatusChart').getContext('2d');
    new Chart(userStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Aprovados', 'Aguardando', 'Reprovados/Bloqueados'],
            datasets: [{
                label: 'Usuários',
                data: [{{ $usersAprovados }}, {{ $usersAguardando }}, {{ $usersReprovadosBloqueados }}],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection

