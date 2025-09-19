<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- API Token -->
    @auth
        @php
            $token = Auth::user()->createToken('auth_token')->plainTextToken;
        @endphp
        <meta name="api-token" content="{{ $token }}">
    @endauth

    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">
        <i class="fas fa-university me-2"></i>{{ config('app.name') }}
    </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    
                    @if (Auth::user() && Auth::user()->tipo_usuario === 'admin')
                    <!-- Menu Pessoa Física -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pessoaFisicaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i>Pessoa Física
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="pessoaFisicaDropdown">
                            <li><a class="dropdown-item" href="{{ route('pessoa-fisica.index') }}">
                                <i class="fas fa-list me-2"></i>Todas as Pessoas Físicas
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('pessoa-fisica.index', ['tipo_conta' => 'corrente']) }}">
                                <i class="fas fa-credit-card me-2"></i>Com Conta Corrente
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('pessoa-fisica.index', ['tipo_conta' => 'poupanca']) }}">
                                <i class="fas fa-piggy-bank me-2"></i>Com Conta Poupança
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['status' => 'aguardando', 'tipo' => 'pessoa_fisica']) }}">
                                <i class="fas fa-clock me-2"></i>Aguardando Aprovação
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['status' => 'reprovado', 'tipo' => 'pessoa_fisica']) }}">
                                <i class="fas fa-times-circle me-2"></i>Reprovados
                            </a></li>
                        </ul>
                    </li>

                    <!-- Menu Pessoa Jurídica -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pessoaJuridicaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-building me-1"></i>Pessoa Jurídica
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="pessoaJuridicaDropdown">
                            <li><a class="dropdown-item" href="{{ route('pessoa-juridica.index') }}">
                                <i class="fas fa-list me-2"></i>Todas as Pessoas Jurídicas
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('pessoa-juridica.index', ['tipo_conta' => 'empresarial']) }}">
                                <i class="fas fa-building me-2"></i>Com Conta Empresarial
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['status' => 'aguardando', 'tipo' => 'pessoa_juridica']) }}">
                                <i class="fas fa-clock me-2"></i>Aguardando Aprovação
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['status' => 'reprovado', 'tipo' => 'pessoa_juridica']) }}">
                                <i class="fas fa-times-circle me-2"></i>Reprovados
                            </a></li>
                        </ul>
                    </li>

                    <!-- Menu Contas Bancárias -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="contasDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-credit-card me-1"></i>Contas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('contas-bancarias.index') }}">
                                <i class="fas fa-list me-2"></i>Todas as Contas
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('contas-bancarias.index') }}?tipo_conta=corrente">
                                <i class="fas fa-credit-card me-2"></i>Conta Corrente
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('contas-bancarias.index') }}?tipo_conta=poupanca">
                                <i class="fas fa-piggy-bank me-2"></i>Conta Poupança
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('contas-bancarias.index') }}?tipo_conta=empresarial">
                                <i class="fas fa-building me-2"></i>Conta Empresarial
                            </a></li>
                        </ul>
                    </li>

                    <!-- Menu Administrativo -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs me-1"></i>Administração
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="fas fa-users me-2"></i>Todos os Usuários
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}?status=aprovado">
                                <i class="fas fa-check-circle me-2"></i>Usuários Aprovados
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}?status=aguardando">
                                <i class="fas fa-clock me-2"></i>Aguardando Aprovação
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('users.index') }}?status=reprovado">
                                <i class="fas fa-times-circle me-2"></i>Usuários Reprovados
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="alert('Comando: php artisan usuarios:aprovar aprovar --status=aguardando')">
                                <i class="fas fa-check me-2"></i>Aprovar em Lote
                            </a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="text-muted mb-0">&copy; 2025 {{ config('app.name') }}. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/imask"></script> <!-- Biblioteca de Máscara -->
    <script>
        // Inicializa todos os tooltips da página
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
