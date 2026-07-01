@extends('layouts.app')

@section('title', 'Socios')

@section('header')
    <h2 class="page-title">Socios</h2>
    <div class="text-muted">Gestiona los socios de la caja de ahorro</div>
@endsection

@section('actions')
    @if(hasPermission('crear_socios'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearSocio">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nuevo Socio
    </button>
    @endif
@endsection

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('socios.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Nombre, cédula o email..." 
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
                            <a href="{{ route('socios.index') }}" class="btn btn-outline-secondary">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de socios --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Socio</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($socios as $socio)
                        <tr>
                            <td class="text-muted">
                                {{ $socio->cedula }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2 bg-blue-lt">
                                        {{ strtoupper(substr($socio->nombre, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-bold">{{ $socio->nombre }} {{ $socio->apellido }}</div>
                                        <div class="text-muted small">{{ $socio->ciudad ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">
                                {{ $socio->telefono }}
                            </td>
                            <td class="text-muted">
                                {{ $socio->email ?? '-' }}
                            </td>
                            <td>
                                @if($socio->estado == 'ACTIVO')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-muted">
                                {{ $socio->created_at ? $socio->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('socios.show', $socio) }}" class="btn btn-sm btn-icon" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    @if(hasPermission('editar_socios'))
                                    <button type="button" class="btn btn-sm btn-icon" title="Editar" onclick="editarSocio({{ $socio->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </button>
                                    @endif
                                    @if(hasPermission('eliminar_socios'))
                                    <button type="button" class="btn btn-sm btn-icon" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $socio->id }}, '{{ $socio->nombre }} {{ $socio->apellido }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $socio->id }}" action="{{ route('socios.destroy', $socio) }}" method="POST" class="d-none">
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
                                No hay socios registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($socios->hasPages())
            <div class="card-footer">
                {{ $socios->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

{{-- Modal Crear Socio --}}
<div class="modal modal-blur fade" id="modalCrearSocio" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form action="{{ route('socios.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Socio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Columna 1: Datos Personales --}}
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">Datos Personales</h5>
                            
                            <div class="mb-3">
                                <label class="form-label required">Cédula</label>
                                <input type="text" name="cedula" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nombres</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Apellidos</label>
                                    <input type="text" name="apellido" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Género</label>
                                    <select name="genero" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ocupación</label>
                                <input type="text" name="ocupacion" class="form-control">
                            </div>
                        </div>
                        
                        {{-- Columna 2: Datos de Contacto --}}
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">Datos de Contacto</h5>
                            
                            <div class="mb-3">
                                <label class="form-label required">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control">
                            </div>
                        </div>
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
                        Crear Socio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Socio --}}
<div class="modal modal-blur fade" id="modalEditarSocio" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="formEditarSocio" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Socio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Columna 1: Datos Personales --}}
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">Datos Personales</h5>
                            
                            <div class="mb-3">
                                <label class="form-label required">Cédula</label>
                                <input type="text" name="cedula" id="edit_cedula" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nombres</label>
                                    <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Apellidos</label>
                                    <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Fecha de Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Género</label>
                                    <select name="genero" id="edit_genero" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ocupación</label>
                                <input type="text" name="ocupacion" id="edit_ocupacion" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Estado</label>
                                <select name="estado" id="edit_estado" class="form-select" required>
                                    <option value="ACTIVO">Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Columna 2: Datos de Contacto --}}
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">Datos de Contacto</h5>
                            
                            <div class="mb-3">
                                <label class="form-label required">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Dirección</label>
                                <textarea name="direccion" id="edit_direccion" class="form-control" rows="2" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="edit_ciudad" class="form-control">
                            </div>
                        </div>
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
                        Actualizar Socio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editarSocio(socioId) {
    fetch(`/socios/${socioId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formEditarSocio').action = `/socios/${socioId}`;
            document.getElementById('edit_cedula').value = data.socio.cedula || '';
            document.getElementById('edit_nombre').value = data.socio.nombres || '';
            document.getElementById('edit_apellido').value = data.socio.apellidos || '';
            document.getElementById('edit_fecha_nacimiento').value = data.socio.fecha_nacimiento || '';
            document.getElementById('edit_genero').value = data.socio.genero || '';
            document.getElementById('edit_telefono').value = data.socio.telefono || '';
            document.getElementById('edit_email').value = data.socio.correo || '';
            document.getElementById('edit_direccion').value = data.socio.direccion || '';
            document.getElementById('edit_ciudad').value = data.socio.ciudad || '';
            document.getElementById('edit_ocupacion').value = data.socio.ocupacion || '';
            document.getElementById('edit_estado').value = data.socio.estado || 'ACTIVO';
            
            new bootstrap.Modal(document.getElementById('modalEditarSocio')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del socio');
        });
}

function confirmarEliminacion(id, nombre) {
    if (confirm('¿Está seguro de eliminar al socio "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
