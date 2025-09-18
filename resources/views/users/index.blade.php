@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users me-2"></i>Usuários</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Novo Usuário
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Status Aprovação</th>
                        <th>Contas</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->tipo_usuario === 'pessoa_fisica')
                                <span class="badge bg-info">Pessoa Física</span>
                            @else
                                <span class="badge bg-warning">Pessoa Jurídica</span>
                            @endif
                        </td>
                        <td>
                            @switch($user->status_aprovacao)
                                @case('aprovado')
                                    <span class="badge bg-success">Aprovado</span>
                                    @break
                                @case('aguardando')
                                    <span class="badge bg-warning">Aguardando</span>
                                    @break
                                @case('reprovado')
                                    <span class="badge bg-danger">Reprovado</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Indefinido</span>
                            @endswitch
                        </td>
                        <td>
                           <span class="badge bg-dark">{{ $user->contas_bancarias_count }}</span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group" aria-label="Ações do Usuário">
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar Usuário">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->isAguardandoAprovacao())
                                    <form action="{{ route('users.approve', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Aprovar Usuário">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reproveModal-{{ $user->id }}" data-bs-toggle="tooltip" title="Reprovar Usuário">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @elseif($user->isAprovado())
                                    <form action="{{ route('users.block', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja bloquear este usuário?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Bloquear Usuário">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @elseif($user->status_aprovacao === 'bloqueado')
                                    <form action="{{ route('users.unblock', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja desbloquear este usuário?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Desbloquear Usuário">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal de Reprovação para cada usuário -->
                    @if($user->isAguardandoAprovacao())
                    <div class="modal fade" id="reproveModal-{{ $user->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Reprovar Usuário: {{ $user->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form action="{{ route('users.reprove', $user->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="motivo_reprovacao-{{ $user->id }}" class="form-label">Motivo da Reprovação</label>
                                    <textarea class="form-control" id="motivo_reprovacao-{{ $user->id }}" name="motivo_reprovacao" rows="3" required minlength="10"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Confirmar Reprovação</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum usuário encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
