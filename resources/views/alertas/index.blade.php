@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Gestión de Riesgos</div>
                <h2 class="page-title">Alertas de Riesgo</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <form action="{{ route('alertas.generar-manualmente') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            Generar Alertas
                        </button>
                    </form>
                    <form action="{{ route('alertas.marcar-todas-leidas') }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Marcar Todas Leídas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Estadísticas -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Alertas</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">No Leídas</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['no_leidas'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Críticas</div>
                        </div>
                        <div class="h1 mb-0 text-danger">{{ $stats['criticas'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Altas</div>
                        </div>
                        <div class="h1 mb-0 text-warning">{{ $stats['altas'] }}</div>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Alertas</h3>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('alertas.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="nivel" class="form-select">
                                <option value="">Todos los niveles</option>
                                <option value="CRITICO" {{ request('nivel') == 'CRITICO' ? 'selected' : '' }}>Crítico</option>
                                <option value="ALTO" {{ request('nivel') == 'ALTO' ? 'selected' : '' }}>Alto</option>
                                <option value="MEDIO" {{ request('nivel') == 'MEDIO' ? 'selected' : '' }}>Medio</option>
                                <option value="BAJO" {{ request('nivel') == 'BAJO' ? 'selected' : '' }}>Bajo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="tipo_alerta" class="form-select">
                                <option value="">Todos los tipos</option>
                                <option value="MORA_TEMPRANA" {{ request('tipo_alerta') == 'MORA_TEMPRANA' ? 'selected' : '' }}>Mora Temprana</option>
                                <option value="CONCENTRACION_CREDITO" {{ request('tipo_alerta') == 'CONCENTRACION_CREDITO' ? 'selected' : '' }}>Concentración de Crédito</option>
                                <option value="CAPACIDAD_PAGO" {{ request('tipo_alerta') == 'CAPACIDAD_PAGO' ? 'selected' : '' }}>Capacidad de Pago</option>
                                <option value="MOROSIDAD_RECURRENTE" {{ request('tipo_alerta') == 'MOROSIDAD_RECURRENTE' ? 'selected' : '' }}>Morosidad Recurrente</option>
                                <option value="INDICE_MOROSIDAD_ALTO" {{ request('tipo_alerta') == 'INDICE_MOROSIDAD_ALTO' ? 'selected' : '' }}>Índice Morosidad Alto</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="leida" class="form-select">
                                <option value="">Todas</option>
                                <option value="0" {{ request('leida') === '0' ? 'selected' : '' }}>No leídas</option>
                                <option value="1" {{ request('leida') === '1' ? 'selected' : '' }}>Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="buscar" class="form-control" placeholder="Buscar socio..." value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de alertas -->
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nivel</th>
                                <th>Tipo</th>
                                <th>Socio</th>
                                <th>Mensaje</th>
                                <th>Estado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertas as $alerta)
                            <tr class="{{ !$alerta->leida ? 'table-active' : '' }}">
                                <td>
                                    <div>{{ $alerta->fecha_alerta->format('d/m/Y') }}</div>
                                    <small class="text-secondary">{{ $alerta->fecha_alerta->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($alerta->nivel == 'CRITICO')
                                        <span class="badge bg-danger">Crítico</span>
                                    @elseif($alerta->nivel == 'ALTO')
                                        <span class="badge bg-warning">Alto</span>
                                    @elseif($alerta->nivel == 'MEDIO')
                                        <span class="badge bg-info">Medio</span>
                                    @else
                                        <span class="badge bg-secondary">Bajo</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ str_replace('_', ' ', $alerta->tipo_alerta) }}</small>
                                </td>
                                <td>
                                    @if($alerta->socio)
                                        <a href="{{ route('socios.show', $alerta->socio_id) }}">
                                            {{ $alerta->socio->nombres }} {{ $alerta->socio->apellidos }}
                                        </a>
                                    @else
                                        <em class="text-muted">Alerta global</em>
                                    @endif
                                </td>
                                <td>
                                    <div style="max-width: 400px;">{{ $alerta->mensaje }}</div>
                                </td>
                                <td>
                                    @if($alerta->leida)
                                        <span class="badge bg-success">Leída</span>
                                    @else
                                        <span class="badge bg-secondary">No leída</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('alertas.show', $alerta->id) }}" class="btn btn-sm btn-primary" title="Ver detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        </a>
                                        @if(!$alerta->leida)
                                        <button type="button" class="btn btn-sm btn-success marcar-leida" data-id="{{ $alerta->id }}" title="Marcar como leída">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger eliminar-alerta" data-id="{{ $alerta->id }}" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No se encontraron alertas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($alertas->hasPages())
            <div class="card-footer">
                {{ $alertas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Marcar como leída
document.querySelectorAll('.marcar-leida').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        fetch(`/alertas/${id}/marcar-leida`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    });
});

// Eliminar alerta
document.querySelectorAll('.eliminar-alerta').forEach(btn => {
    btn.addEventListener('click', function() {
        if(!confirm('¿Está seguro de eliminar esta alerta?')) return;
        
        const id = this.dataset.id;
        
        fetch(`/alertas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    });
});
</script>
@endpush
@endsection
