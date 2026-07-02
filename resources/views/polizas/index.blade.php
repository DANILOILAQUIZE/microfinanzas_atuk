@extends('layouts.app')

@section('title', 'Pólizas Contables')

@section('header')
    <h2 class="page-title">Pólizas Contables</h2>
    <div class="text-muted">Registro de pólizas contables del sistema (Debe = Haber)</div>
@endsection

@section('actions')
    <a href="{{ route('polizas.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nueva Póliza
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('polizas.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="INGRESO" {{ request('tipo') == 'INGRESO' ? 'selected' : '' }}>Ingreso</option>
                            <option value="EGRESO" {{ request('tipo') == 'EGRESO' ? 'selected' : '' }}>Egreso</option>
                            <option value="DIARIO" {{ request('tipo') == 'DIARIO' ? 'selected' : '' }}>Diario</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            <a href="{{ route('polizas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de pólizas --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>N° Póliza</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Registrado por</th>
                            <th>Total</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polizas as $poliza)
                        <tr>
                            <td class="text-muted">
                                <div class="fw-bold">#{{ str_pad($poliza->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="text-muted">
                                {{ $poliza->fecha->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($poliza->tipo == 'INGRESO')
                                    <span class="badge bg-success">Ingreso</span>
                                @elseif($poliza->tipo == 'EGRESO')
                                    <span class="badge bg-danger">Egreso</span>
                                @else
                                    <span class="badge bg-blue">Diario</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    {{ $poliza->concepto }}
                                </div>
                            </td>
                            <td class="text-muted">
                                {{ $poliza->usuario->nombre ?? 'N/A' }} {{ $poliza->usuario->apellido ?? '' }}
                            </td>
                            <td class="fw-bold">
                                ${{ number_format($poliza->detalles->sum('debe'), 2) }}
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    <a href="{{ route('polizas.show', $poliza) }}" class="btn-action btn-action-view" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('polizas.edit', $poliza) }}" class="btn-action btn-action-edit" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="btn-action btn-action-delete" title="Eliminar" 
                                            onclick="confirmarEliminacion({{ $poliza->id }}, '{{ $poliza->concepto }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $poliza->id }}" action="{{ route('polizas.destroy', $poliza) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay pólizas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($polizas->hasPages())
            <div class="card-footer">
                {{ $polizas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmarEliminacion(id, concepto) {
    if (confirm('¿Está seguro de eliminar la póliza "' + concepto + '"?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
