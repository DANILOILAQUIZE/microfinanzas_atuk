@extends('layouts.app')

@section('title', 'Parámetros del Sistema')

@section('header')
    <h2 class="page-title">Parámetros del Sistema</h2>
    <div class="text-muted">Configura los parámetros y valores del sistema</div>
@endsection

@section('actions')
    @if(hasPermission('gestionar_parametros'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearParametro">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nuevo Parámetro
    </button>
    @endif
@endsection

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('parametros.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Nombre, clave o descripción..." 
                               value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Grupo</label>
                        <select name="grupo" class="form-select">
                            <option value="">Todos los grupos</option>
                            @foreach($grupos as $grupo)
                                @if($grupo)
                                <option value="{{ $grupo }}" {{ request('grupo') == $grupo ? 'selected' : '' }}>
                                    {{ ucfirst($grupo) }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Filtrar
                            </button>
                            <a href="{{ route('parametros.index') }}" class="btn btn-outline-secondary">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de parámetros --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Parámetro</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parametros as $parametro)
                        <tr>
                            <td>
                                @if($parametro->grupo)
                                    <span class="badge bg-azure-lt">{{ ucfirst($parametro->grupo) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $parametro->nombre }}</div>
                                    <div class="text-muted small">{{ $parametro->clave }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-blue">{{ $parametro->valor }}</span>
                            </td>
                            <td>
                                @switch($parametro->tipo)
                                    @case('numero')
                                        <span class="badge bg-cyan-lt">Número</span>
                                        @break
                                    @case('porcentaje')
                                        <span class="badge bg-green-lt">Porcentaje</span>
                                        @break
                                    @case('booleano')
                                        <span class="badge bg-purple-lt">Booleano</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary-lt">Texto</span>
                                @endswitch
                            </td>
                            <td class="text-muted">
                                {{ Str::limit($parametro->descripcion, 50) ?? '-' }}
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    @if(hasPermission('gestionar_parametros'))
                                    <button type="button" class="btn-action btn-action-edit" title="Editar" onclick="editarParametro({{ $parametro->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-action btn-action-delete" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $parametro->id }}, '{{ $parametro->nombre }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $parametro->id }}" action="{{ route('parametros.destroy', $parametro) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay parámetros registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($parametros->hasPages())
            <div class="card-footer">
                {{ $parametros->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Crear Parámetro --}}
<div class="modal modal-blur fade" id="modalCrearParametro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('parametros.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Parámetro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Clave</label>
                            <input type="text" name="clave" class="form-control" placeholder="ej: tasa_mora_mensual" required>
                            <small class="form-hint">Identificador único (sin espacios)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="ej: Tasa de Mora Mensual" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Valor</label>
                            <input type="text" name="valor" class="form-control" placeholder="ej: 2.5" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="texto">Texto</option>
                                <option value="numero">Número</option>
                                <option value="porcentaje">Porcentaje</option>
                                <option value="booleano">Booleano (true/false)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Grupo</label>
                        <select name="grupo" class="form-select">
                            <option value="">Sin grupo</option>
                            <option value="mora">Mora</option>
                            <option value="sistema">Sistema</option>
                            <option value="transacciones">Transacciones</option>
                            <option value="notificaciones">Notificaciones</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción del parámetro..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Crear Parámetro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Botón invisible para abrir modal de editar desde JavaScript --}}
<button type="button" id="triggerModalEditarParametro" data-bs-toggle="modal" data-bs-target="#modalEditarParametro" style="display:none;"></button>

{{-- Modal Editar Parámetro --}}
<div class="modal modal-blur fade" id="modalEditarParametro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formEditarParametro" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Parámetro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Clave</label>
                            <input type="text" name="clave" id="edit_clave" class="form-control" required>
                            <small class="form-hint">Identificador único (sin espacios)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Valor</label>
                            <input type="text" name="valor" id="edit_valor" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo</label>
                            <select name="tipo" id="edit_tipo" class="form-select" required>
                                <option value="texto">Texto</option>
                                <option value="numero">Número</option>
                                <option value="porcentaje">Porcentaje</option>
                                <option value="booleano">Booleano (true/false)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Grupo</label>
                        <select name="grupo" id="edit_grupo" class="form-select">
                            <option value="">Sin grupo</option>
                            <option value="mora">Mora</option>
                            <option value="sistema">Sistema</option>
                            <option value="transacciones">Transacciones</option>
                            <option value="notificaciones">Notificaciones</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                            <path d="M14 4l0 4l-6 0l0 -4"/>
                        </svg>
                        Actualizar Parámetro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarParametro(parametroId) {
    fetch(`/parametros/${parametroId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            document.getElementById('formEditarParametro').action = `/parametros/${parametroId}`;
            document.getElementById('edit_clave').value = data.parametro.clave || '';
            document.getElementById('edit_nombre').value = data.parametro.nombre || '';
            document.getElementById('edit_valor').value = data.parametro.valor || '';
            document.getElementById('edit_tipo').value = data.parametro.tipo || 'texto';
            document.getElementById('edit_grupo').value = data.parametro.grupo || '';
            document.getElementById('edit_descripcion').value = data.parametro.descripcion || '';
            
            // Mostrar modal usando botón trigger
            document.getElementById('triggerModalEditarParametro').click();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del parámetro');
        });
}

function confirmarEliminacion(id, nombre) {
    if (confirm('¿Está seguro de eliminar el parámetro "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
