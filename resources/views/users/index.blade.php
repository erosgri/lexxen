@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users me-2"></i>Usuários</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Novo Usuário
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2"></i>Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status de Aprovação</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="aguardando" {{ request('status') == 'aguardando' ? 'selected' : '' }}>Aguardando</option>
                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="reprovado" {{ request('status') == 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                        <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tipo" class="form-label">Tipo de Usuário</label>
                    <select name="tipo" id="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="pessoa_fisica" {{ request('tipo') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                        <option value="pessoa_juridica" {{ request('tipo') == 'pessoa_juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Aprovação em Lote -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-users-cog me-2"></i>Aprovação em Lote</h5>
    </div>
    <div class="card-body">
        <form id="batch-approval-form" method="POST" action="{{ route('users.batch-approve') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="batch-action" class="form-label">Ação</label>
                    <select name="action" id="batch-action" class="form-select" required>
                        <option value="">Selecione uma ação</option>
                        <option value="approve">Aprovar Selecionados</option>
                        <option value="reject">Reprovar Selecionados</option>
                        <option value="block">Bloquear Selecionados</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="batch-reason" class="form-label">Motivo (para reprovação/bloqueio)</label>
                    <textarea name="reason" id="batch-reason" class="form-control" rows="2" placeholder="Digite o motivo da reprovação ou bloqueio"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="batch-submit" disabled>
                        <i class="fas fa-check me-1"></i>Executar Ação
                    </button>
                    <span class="ms-3 text-muted" id="selected-count">0 usuários selecionados</span>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
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
                        <td>
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                        </td>
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
                        <td colspan="8" class="text-center">Nenhum usuário encontrado</td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const batchSubmit = document.getElementById('batch-submit');
    const selectedCount = document.getElementById('selected-count');
    const batchAction = document.getElementById('batch-action');
    const batchReason = document.getElementById('batch-reason');
    
    // Selecionar/deselecionar todos
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
        updateSubmitButton();
    });
    
    // Atualizar contador quando checkboxes individuais mudarem
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSubmitButton();
            
            // Atualizar checkbox "selecionar todos"
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
        });
    });
    
    // Atualizar botão de submit quando ação mudar
    batchAction.addEventListener('change', function() {
        updateSubmitButton();
        
        // Mostrar/ocultar campo de motivo
        if (this.value === 'reject' || this.value === 'block') {
            batchReason.required = true;
            batchReason.parentElement.style.display = 'block';
        } else {
            batchReason.required = false;
            batchReason.parentElement.style.display = 'none';
        }
    });
    
    
    // Auto-submit do filtro quando status ou tipo mudar (comentado temporariamente)
    // document.getElementById('status').addEventListener('change', function() {
    //     this.form.submit();
    // });
    
    // document.getElementById('tipo').addEventListener('change', function() {
    //     this.form.submit();
    // });
    
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCount.textContent = `${checkedCount} usuário(s) selecionado(s)`;
    }
    
    function updateSubmitButton() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        const hasAction = batchAction.value !== '';
        const hasReason = batchAction.value === 'approve' || (batchAction.value !== 'approve' && batchReason.value.trim() !== '');
        
        batchSubmit.disabled = !(checkedCount > 0 && hasAction && hasReason);
    }
    
    // Validação do formulário
    document.getElementById('batch-approval-form').addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        const action = batchAction.value;
        const reason = batchReason.value.trim();
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Selecione pelo menos um usuário.');
            return;
        }
        
        if (action === '') {
            e.preventDefault();
            alert('Selecione uma ação.');
            return;
        }
        
        if ((action === 'reject' || action === 'block') && reason === '') {
            e.preventDefault();
            alert('Motivo é obrigatório para reprovação e bloqueio.');
            return;
        }
        
        const actionText = action === 'approve' ? 'aprovar' : action === 'reject' ? 'reprovar' : 'bloquear';
        if (!confirm(`Tem certeza que deseja ${actionText} ${checkedCount} usuário(s)?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
