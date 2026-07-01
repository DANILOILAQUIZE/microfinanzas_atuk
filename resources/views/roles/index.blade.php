@extends('layouts.app')

@section('title', 'Roles y Permisos')

@section('header')
    <h2 class="page-title">Roles y Permisos</h2>
    <div class="text-muted">Gestiona los roles del sistema y sus permisos</div>
@endsection

@section('actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nuevo Rol
    </button>
@endsection

@section('content')

<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th>Descripción</th>
                            <th>Usuarios</th>
                            <th>Permisos</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $rol)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $rol->nombre }}</div>
                            </td>
                            <td class="text-muted">
                                {{ $rol->descripcion ?? '-' }}
                            </td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    {{ $rol->usuarios_count }} {{ $rol->usuarios_count == 1 ? 'usuario' : 'usuarios' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-green-lt">
                                    {{ $rol->permisos->count() }} permisos
                                </span>
                            </td>
                            <td>
                                @if($rol->estado == 'ACTIVO')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('roles.show', $rol) }}" class="btn btn-sm btn-icon" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon" title="Editar" onclick="editarRol({{ $rol->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $rol->id }}, '{{ $rol->nombre }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $rol->id }}" action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay roles registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear Rol --}}
<div class="modal modal-blur fade" id="modalCrearRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nombre del Rol</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" placeholder="Ej: Gerente" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                               value="{{ old('descripcion') }}" placeholder="Descripción del rol">
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Permisos</label>
                            <div>
                                <a href="#" onclick="seleccionarTodosCrear(); return false;" class="btn btn-sm btn-link">Todos</a>
                                <a href="#" onclick="deseleccionarTodosCrear(); return false;" class="btn btn-sm btn-link">Ninguno</a>
                            </div>
                        </div>
                        
                        <div style="max-height: 400px; overflow-y: auto;">
                            @foreach($permisos as $modulo => $permisosModulo)
                            <div class="mb-3">
                                <h5 class="text-muted">{{ $modulo }}</h5>
                                <div class="row">
                                    @foreach($permisosModulo as $permiso)
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}" 
                                                   class="form-check-input permiso-checkbox-crear"
                                                   {{ in_array($permiso->id, old('permisos', [])) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $permiso->nombre }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Rol --}}
<div class="modal modal-blur fade" id="modalEditarRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="formEditarRol" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nombre del Rol</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" name="descripcion" id="edit_descripcion" class="form-control">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Permisos</label>
                            <div>
                                <a href="#" onclick="seleccionarTodosEditar(); return false;" class="btn btn-sm btn-link">Todos</a>
                                <a href="#" onclick="deseleccionarTodosEditar(); return false;" class="btn btn-sm btn-link">Ninguno</a>
                            </div>
                        </div>
                        
                        <div id="permisos-editar-container" style="max-height: 400px; overflow-y: auto;">
                            {{-- Se cargará dinámicamente con JavaScript --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                            <path d="M14 4l0 4l-6 0l0 -4"/>
                        </svg>
                        Actualizar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Funciones para modal de crear
function seleccionarTodosCrear() {
    document.querySelectorAll('.permiso-checkbox-crear').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deseleccionarTodosCrear() {
    document.querySelectorAll('.permiso-checkbox-crear').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Funciones para modal de editar
function seleccionarTodosEditar() {
    document.querySelectorAll('.permiso-checkbox-editar').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deseleccionarTodosEditar() {
    document.querySelectorAll('.permiso-checkbox-editar').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Función para cargar datos del rol en el modal de edición
function editarRol(rolId) {
    fetch(`/roles/${rolId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Actualizar action del formulario
            document.getElementById('formEditarRol').action = `/roles/${rolId}`;
            
            // Llenar campos
            document.getElementById('edit_nombre').value = data.rol.nombre;
            document.getElementById('edit_descripcion').value = data.rol.descripcion || '';
            
            // Construir HTML de permisos
            let permisosHTML = '';
            Object.keys(data.permisos).forEach(modulo => {
                permisosHTML += `
                    <div class="mb-3">
                        <h5 class="text-muted">${modulo}</h5>
                        <div class="row">
                `;
                
                data.permisos[modulo].forEach(permiso => {
                    const checked = data.permisosAsignados.includes(permiso.id) ? 'checked' : '';
                    permisosHTML += `
                        <div class="col-md-6">
                            <label class="form-check">
                                <input type="checkbox" name="permisos[]" value="${permiso.id}" 
                                       class="form-check-input permiso-checkbox-editar" ${checked}>
                                <span class="form-check-label">${permiso.nombre}</span>
                            </label>
                        </div>
                    `;
                });
                
                permisosHTML += `
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('permisos-editar-container').innerHTML = permisosHTML;
            
            // Mostrar modal
            new bootstrap.Modal(document.getElementById('modalEditarRol')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del rol');
        });
}

function confirmarEliminacion(id, nombre) {
    if (confirm('¿Está seguro de eliminar el rol "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
