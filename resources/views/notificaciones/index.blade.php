@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Comunicaciones</div>
                <h2 class="page-title">Notificaciones</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <form action="{{ route('notificaciones.enviar-manualmente') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
                            Generar y Enviar
                        </button>
                    </form>
                    <form action="{{ route('notificaciones.marcar-todas-leidas') }}" method="POST" class="d-inline">
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
                            <div class="subheader">Total</div>
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
                            <div class="subheader">Pendientes Envío</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['pendientes_envio'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Hoy</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['hoy'] }}</div>
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
                <h3 class="card-title">Listado de Notificaciones</h3>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('notificaciones.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <select name="tipo" class="form-select">
                                <option value="">Todos los tipos</option>
                                <option value="CUOTA_PROXIMA" {{ request('tipo') == 'CUOTA_PROXIMA' ? 'selected' : '' }}>Cuota Próxima</option>
                                <option value="CUOTA_VENCIDA" {{ request('tipo') == 'CUOTA_VENCIDA' ? 'selected' : '' }}>Cuota Vencida</option>
                                <option value="PRESTAMO_APROBADO" {{ request('tipo') == 'PRESTAMO_APROBADO' ? 'selected' : '' }}>Préstamo Aprobado</option>
                                <option value="PAGO_CONFIRMADO" {{ request('tipo') == 'PAGO_CONFIRMADO' ? 'selected' : '' }}>Pago Confirmado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="canal" class="form-select">
                                <option value="">Todos los canales</option>
                                <option value="SISTEMA" {{ request('canal') == 'SISTEMA' ? 'selected' : '' }}>Sistema</option>
                                <option value="EMAIL" {{ request('canal') == 'EMAIL' ? 'selected' : '' }}>Email</option>
                                <option value="SMS" {{ request('canal') == 'SMS' ? 'selected' : '' }}>SMS</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="leida" class="form-select">
                                <option value="">Todas</option>
                                <option value="0" {{ request('leida') === '0' ? 'selected' : '' }}>No leídas</option>
                                <option value="1" {{ request('leida') === '1' ? 'selected' : '' }}>Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="enviada" class="form-select">
                                <option value="">Todos los envíos</option>
                                <option value="0" {{ request('enviada') === '0' ? 'selected' : '' }}>No enviadas</option>
                                <option value="1" {{ request('enviada') === '1' ? 'selected' : '' }}>Enviadas</option>
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

                <!-- Tabla de notificaciones -->
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Destinatario</th>
                                <th>Tipo</th>
                                <th>Canal</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notificaciones as $notificacion)
                            <tr class="{{ !$notificacion->leida ? 'table-active' : '' }}">
                                <td>
                                    <div>{{ $notificacion->fecha_notificacion->format('d/m/Y') }}</div>
                                    <small class="text-secondary">{{ $notificacion->fecha_notificacion->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($notificacion->socio)
                                        <a href="{{ route('socios.show', $notificacion->socio_id) }}">
                                            {{ $notificacion->socio->nombres }} {{ $notificacion->socio->apellidos }}
                                        </a>
                                    @elseif($notificacion->usuario)
                                        {{ $notificacion->usuario->nombre }} {{ $notificacion->usuario->apellido }}
                                    @else
                                        <em class="text-muted">Sistema</em>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ str_replace('_', ' ', $notificacion->tipo) }}</small>
                                </td>
                                <td>
                                    @if($notificacion->canal == 'EMAIL')
                                        <span class="badge bg-info">Email</span>
                                    @elseif($notificacion->canal == 'SMS')
                                        <span class="badge bg-warning">SMS</span>
                                    @else
                                        <span class="badge bg-secondary">Sistema</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="max-width: 300px;">
                                        <strong>{{ $notificacion->titulo }}</strong>
                                        <br>
                                        <small class="text-secondary">{{ \Str::limit($notificacion->mensaje, 80) }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($notificacion->leida)
                                        <span class="badge bg-success">Leída</span>
                                    @else
                                        <span class="badge bg-secondary">No leída</span>
                                    @endif
                                    @if($notificacion->canal != 'SISTEMA')
                                        <br>
                                        @if($notificacion->enviada)
                                            <span class="badge bg-success mt-1">Enviada</span>
                                        @else
                                            <span class="badge bg-warning mt-1">Pendiente</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('notificaciones.show', $notificacion->id) }}" class="btn btn-sm btn-primary" title="Ver detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        </a>
                                        @if(!$notificacion->leida)
                                        <button type="button" class="btn btn-sm btn-success marcar-leida" data-id="{{ $notificacion->id }}" title="Marcar como leída">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger eliminar-notificacion" data-id="{{ $notificacion->id }}" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No se encontraron notificaciones
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($notificaciones->hasPages())
            <div class="card-footer">
                {{ $notificaciones->links() }}
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
        
        fetch(`/notificaciones/${id}/marcar-leida`, {
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

// Eliminar notificación
document.querySelectorAll('.eliminar-notificacion').forEach(btn => {
    btn.addEventListener('click', function() {
        if(!confirm('¿Está seguro de eliminar esta notificación?')) return;
        
        const id = this.dataset.id;
        
        fetch(`/notificaciones/${id}`, {
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
