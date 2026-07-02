@extends('layouts.app')

@section('title', 'Tipos de Préstamo')

@section('header')
    <h2 class="page-title">Tipos de Préstamo</h2>
    <div class="text-muted">Gestiona los tipos de productos crediticios</div>
@endsection

@section('actions')
    @if(hasPermission('gestionar_parametros'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearTipo">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nuevo Tipo de Préstamo
    </button>
    @endif
@endsection

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('tipos-prestamo.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Nombre o descripción..." 
                               value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="ACTIVO" {{ request('estado') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                            <option value="INACTIVO" {{ request('estado') == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Filtrar
                            </button>
                            <a href="{{ route('tipos-prestamo.index') }}" class="btn btn-outline-secondary">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de tipos de préstamo --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tasa de Interés</th>
                            <th>Montos</th>
                            <th>Plazos (meses)</th>
                            <th>Garantía</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tiposPrestamo as $tipo)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $tipo->nombre }}</div>
                                    @if($tipo->descripcion)
                                    <div class="text-muted small">{{ Str::limit($tipo->descripcion, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-muted">
                                <strong>{{ number_format($tipo->interes, 2) }}%</strong> anual
                            </td>
                            <td class="text-muted">
                                ${{ number_format($tipo->monto_minimo, 2) }} - ${{ number_format($tipo->monto_maximo, 2) }}
                            </td>
                            <td class="text-muted">
                                {{ $tipo->plazo_minimo }} - {{ $tipo->plazo_maximo }} meses
                            </td>
                            <td>
                                @if($tipo->requiere_garantia)
                                    <span class="badge bg-warning">Requiere</span>
                                @else
                                    <span class="badge bg-info">No requiere</span>
                                @endif
                            </td>
                            <td>
                                @if($tipo->estado == 'ACTIVO')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    <a href="{{ route('tipos-prestamo.show', $tipo) }}" class="btn-action btn-action-view" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    @if(hasPermission('gestionar_parametros'))
                                    <button type="button" class="btn-action btn-action-edit" title="Editar" onclick="editarTipo({{ $tipo->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn-action btn-action-delete" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $tipo->id }}, '{{ $tipo->nombre }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $tipo->id }}" action="{{ route('tipos-prestamo.destroy', $tipo) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay tipos de préstamo registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tiposPrestamo->hasPages())
            <div class="card-footer">
                {{ $tiposPrestamo->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

{{-- Botones invisibles para abrir modales desde JavaScript --}}
<button type="button" id="triggerModalEditarTipo" data-bs-toggle="modal" data-bs-target="#modalEditarTipo" style="display:none;"></button>

{{-- Modal Crear Tipo --}}
<div class="modal modal-blur fade" id="modalCrearTipo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('tipos-prestamo.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Tipo de Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tasa de Interés (%)</label>
                            <input type="number" step="0.01" name="interes" class="form-control" required min="0" max="100">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Mínimo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto_minimo" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Máximo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto_maximo" class="form-control" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Mínimo (meses)</label>
                            <input type="number" name="plazo_minimo" class="form-control" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Máximo (meses)</label>
                            <input type="number" name="plazo_maximo" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="requiere_garantia" value="1" class="form-check-input">
                            <span class="form-check-label">Requiere garantía</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">Crear Tipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Tipo --}}
<div class="modal modal-blur fade" id="modalEditarTipo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formEditarTipo" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tipo de Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tasa de Interés (%)</label>
                            <input type="number" step="0.01" name="interes" id="edit_interes" class="form-control" required min="0" max="100">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Mínimo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto_minimo" id="edit_monto_minimo" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Máximo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto_maximo" id="edit_monto_maximo" class="form-control" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Mínimo (meses)</label>
                            <input type="number" name="plazo_minimo" id="edit_plazo_minimo" class="form-control" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Máximo (meses)</label>
                            <input type="number" name="plazo_maximo" id="edit_plazo_maximo" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="requiere_garantia" id="edit_requiere_garantia" value="1" class="form-check-input">
                            <span class="form-check-label">Requiere garantía</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" id="edit_estado" class="form-select" required>
                            <option value="ACTIVO">Activo</option>
                            <option value="INACTIVO">Inactivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">Actualizar Tipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editarTipo(tipoId) {
    fetch(`/tipos-prestamo/${tipoId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            document.getElementById('formEditarTipo').action = `/tipos-prestamo/${tipoId}`;
            document.getElementById('edit_nombre').value = data.tipo.nombre || '';
            document.getElementById('edit_interes').value = data.tipo.interes || '';
            document.getElementById('edit_monto_minimo').value = data.tipo.monto_minimo || '';
            document.getElementById('edit_monto_maximo').value = data.tipo.monto_maximo || '';
            document.getElementById('edit_plazo_minimo').value = data.tipo.plazo_minimo || '';
            document.getElementById('edit_plazo_maximo').value = data.tipo.plazo_maximo || '';
            document.getElementById('edit_requiere_garantia').checked = data.tipo.requiere_garantia == 1;
            document.getElementById('edit_estado').value = data.tipo.estado || 'ACTIVO';
            document.getElementById('edit_descripcion').value = data.tipo.descripcion || '';
            
            // Mostrar modal usando botón trigger
            document.getElementById('triggerModalEditarTipo').click();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del tipo de préstamo');
        });
}

function confirmarEliminacion(id, nombre) {
    if (confirm('¿Está seguro de eliminar el tipo de préstamo "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
