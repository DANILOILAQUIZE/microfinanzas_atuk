@extends('layouts.app')

@section('title', 'Tipos de Préstamo')

@section('header')
    <h2 class="page-title">Tipos de Préstamo</h2>
    <div class="text-muted">Gestiona los tipos de productos crediticios</div>
@endsection

@section('actions')
    @if(hasPermission('gestionar_parametros'))
    <a href="{{ route('tipos-prestamo.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nuevo Tipo de Préstamo
    </a>
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
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('tipos-prestamo.show', $tipo) }}" class="btn btn-sm btn-icon" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    @if(hasPermission('gestionar_parametros'))
                                    <a href="{{ route('tipos-prestamo.edit', $tipo) }}" class="btn btn-sm btn-icon" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $tipo->id }}, '{{ $tipo->nombre }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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

@push('scripts')
<script>
function confirmarEliminacion(id, nombre) {
    if (confirm('¿Está seguro de eliminar el tipo de préstamo "' + nombre + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
