@extends('layouts.app')

@section('title', 'Préstamos')

@section('header')
    <h2 class="page-title">Préstamos</h2>
    <div class="text-muted">Gestiona las solicitudes y préstamos activos</div>
@endsection

@section('actions')
    @if(hasPermission('crear_prestamos'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearPrestamo">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 5l0 14"/>
            <path d="M5 12l14 0"/>
        </svg>
        Nueva Solicitud
    </button>
    @endif
    
    <a href="{{ route('prestamos.index', ['estado' => 'VENCIDO']) }}" class="btn btn-danger">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 9v4"/>
            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
            <path d="M12 16h.01"/>
        </svg>
        Ver Préstamos en Mora
        @if(isset($totalVencidos) && $totalVencidos > 0)
            <span class="badge bg-white text-danger ms-1">{{ $totalVencidos }}</span>
        @endif
    </a>
    
    @if(hasPermission('aprobar_prestamos'))
    <form action="{{ route('prestamos.detectar-mora') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-warning" onclick="return confirm('¿Ejecutar detección de mora manualmente? Esto actualizará el estado de todas las cuotas vencidas.')">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
            Detectar Mora
        </button>
    </form>
    @endif
@endsection

@section('content')

{{-- Alerta de Préstamos en Mora --}}
@if(isset($totalVencidos) && $totalVencidos > 0 && !request()->filled('estado'))
<div class="alert alert-danger alert-dismissible mb-3" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 9v4"/>
                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                <path d="M12 16h.01"/>
            </svg>
        </div>
        <div>
            <h4 class="alert-title">¡Atención! Préstamos con Mora</h4>
            <div class="text-muted">
                Hay <strong>{{ $totalVencidos }} préstamos con cuotas vencidas</strong> que requieren seguimiento.
                <a href="{{ route('prestamos.index', ['estado' => 'VENCIDO']) }}" class="alert-link">Ver préstamos en mora</a>
            </div>
        </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('prestamos.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar Socio</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre o cédula..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado Aprobación</label>
                        <select name="estado_aprobacion" class="form-select">
                            <option value="">Todos</option>
                            <option value="PENDIENTE" {{ request('estado_aprobacion') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                            <option value="APROBADO" {{ request('estado_aprobacion') == 'APROBADO' ? 'selected' : '' }}>Aprobado</option>
                            <option value="RECHAZADO" {{ request('estado_aprobacion') == 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado Préstamo</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                            <option value="ACTIVO" {{ request('estado') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                            <option value="VENCIDO" {{ request('estado') == 'VENCIDO' ? 'selected' : '' }}>Vencido</option>
                            <option value="CANCELADO" {{ request('estado') == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                            <option value="RECHAZADO" {{ request('estado') == 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo_prestamo_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($tiposPrestamo as $tipo)
                                <option value="{{ $tipo->id }}" {{ request('tipo_prestamo_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            <a href="{{ route('prestamos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de préstamos --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Socio</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Plazo</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->fecha_solicitud ? $prestamo->fecha_solicitud->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="fw-bold">{{ $prestamo->socio->nombres ?? '-' }} {{ $prestamo->socio->apellidos ?? '' }}</div>
                                <div class="text-muted small">{{ $prestamo->socio->cedula ?? '' }}</div>
                            </td>
                            <td>{{ $prestamo->tipoPrestamo->nombre ?? '-' }}</td>
                            <td>
                                <div class="fw-bold">${{ number_format($prestamo->monto, 2) }}</div>
                                <div class="text-muted small">Total: ${{ number_format($prestamo->monto_total, 2) }}</div>
                            </td>
                            <td>{{ $prestamo->plazo }} meses</td>
                            <td>
                                <div class="fw-bold text-{{ $prestamo->saldo > 0 ? 'primary' : 'success' }}">${{ number_format($prestamo->saldo, 2) }}</div>
                                @if($prestamo->saldo <= 0)
                                    <div class="text-success small">✓ Pagado</div>
                                @else
                                    {{-- Calcular cuotas vencidas --}}
                                    @php
                                        $cuotasVencidas = $prestamo->cuotas->where('estado', 'VENCIDA')->count();
                                        $moraTotal = $prestamo->cuotas->where('estado', 'VENCIDA')->sum('mora');
                                    @endphp
                                    @if($cuotasVencidas > 0)
                                        <div class="text-danger small">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 9v4"/>
                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                                <path d="M12 16h.01"/>
                                            </svg>
                                            {{ $cuotasVencidas }} {{ $cuotasVencidas == 1 ? 'cuota vencida' : 'cuotas vencidas' }}
                                        </div>
                                        @if($moraTotal > 0)
                                            <div class="text-danger small fw-bold">Mora: ${{ number_format($moraTotal, 2) }}</div>
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($prestamo->estado_aprobacion == 'PENDIENTE')
                                    <span class="badge bg-warning">Pendiente Aprobación</span>
                                @elseif($prestamo->estado_aprobacion == 'RECHAZADO')
                                    <span class="badge bg-danger">Rechazado</span>
                                @else
                                    {{-- Préstamo aprobado, mostrar estado actual --}}
                                    @php
                                        $cuotasVencidas = $prestamo->cuotas->where('estado', 'VENCIDA')->count();
                                    @endphp
                                    
                                    @if($prestamo->estado == 'CANCELADO')
                                        <span class="badge bg-success">✓ Cancelado</span>
                                    @elseif($cuotasVencidas > 0 || $prestamo->estado == 'VENCIDO')
                                        <span class="badge bg-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" style="vertical-align: -2px;">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 9v4"/>
                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                                <path d="M12 16h.01"/>
                                            </svg>
                                            VENCIDO - {{ $cuotasVencidas }} cuota(s)
                                        </span>
                                    @elseif($prestamo->estado == 'ACTIVO')
                                        <span class="badge bg-blue">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $prestamo->estado }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-action-group">
                                    <a href="{{ route('prestamos.show', $prestamo) }}" class="btn-action btn-action-view" title="Ver detalles">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    
                                    @if($prestamo->estado_aprobacion == 'PENDIENTE')
                                        @if(hasPermission('editar_prestamos'))
                                        <button type="button" class="btn-action btn-action-edit" title="Editar" onclick="editarPrestamo({{ $prestamo->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                <path d="M16 5l3 3"/>
                                            </svg>
                                        </button>
                                        @endif
                                        
                                        @if(hasPermission('aprobar_prestamos'))
                                        <button type="button" class="btn-action btn-action-success" title="Aprobar" onclick="mostrarModalAprobar({{ $prestamo->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-action btn-action-delete" title="Rechazar" onclick="mostrarModalRechazar({{ $prestamo->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M18 6l-12 12"/>
                                                <path d="M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay préstamos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($prestamos->hasPages())
            <div class="card-footer">{{ $prestamos->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- MODALES SE AGREGAN EN LA PRÓXIMA RESPUESTA POR LÍMITE DE TOKENS --}}

@endsection

{{-- Botones invisibles para abrir modales desde JavaScript --}}
<button type="button" id="triggerModalEditar" data-bs-toggle="modal" data-bs-target="#modalEditarPrestamo" style="display:none;"></button>
<button type="button" id="triggerModalAprobar" data-bs-toggle="modal" data-bs-target="#modalAprobarPrestamo" style="display:none;"></button>
<button type="button" id="triggerModalRechazar" data-bs-toggle="modal" data-bs-target="#modalRechazarPrestamo" style="display:none;"></button>

{{-- Modal Crear Préstamo --}}
<div class="modal modal-blur fade" id="modalCrearPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('prestamos.store') }}" method="POST" id="formCrearPrestamo">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Solicitud de Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Socio</label>
                            <select name="socio_id" class="form-select" required>
                                <option value="">Seleccione un socio</option>
                                @foreach($socios as $socio)
                                    <option value="{{ $socio->id }}">{{ $socio->nombre }} {{ $socio->apellido }} - {{ $socio->cedula }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo de Préstamo</label>
                            <select name="tipo_prestamo_id" id="tipo_prestamo_crear" class="form-select" required onchange="cargarInfoTipo(this.value, 'crear')">
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposPrestamo as $tipo)
                                    <option value="{{ $tipo->id }}" 
                                        data-interes="{{ $tipo->interes }}"
                                        data-monto-min="{{ $tipo->monto_minimo }}"
                                        data-monto-max="{{ $tipo->monto_maximo }}"
                                        data-plazo-min="{{ $tipo->plazo_minimo }}"
                                        data-plazo-max="{{ $tipo->plazo_maximo }}">
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div id="info_tipo_crear" class="alert alert-info d-none mb-3"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Solicitado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto" id="monto_crear" class="form-control" required onchange="calcularPrestamo('crear')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo (meses)</label>
                            <input type="number" name="plazo" id="plazo_crear" class="form-control" required onchange="calcularPrestamo('crear')">
                        </div>
                    </div>
                    
                    <div id="calculo_crear" class="card bg-light d-none mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Cálculo del Préstamo</h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted">Tasa de Interés:</div>
                                    <div class="fw-bold" id="interes_mostrar_crear">0%</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted">Interés Total:</div>
                                    <div class="fw-bold" id="interes_total_crear">$0.00</div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="text-muted">Monto Total a Pagar:</div>
                                    <div class="fs-3 text-primary" id="monto_total_crear">$0.00</div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="text-muted">Cuota Mensual:</div>
                                    <div class="fs-3 text-success" id="cuota_mensual_crear">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">Registrar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Préstamo --}}
<div class="modal modal-blur fade" id="modalEditarPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formEditarPrestamo" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Socio</label>
                            <select name="socio_id" id="edit_socio_id" class="form-select" required>
                                @foreach($socios as $socio)
                                    <option value="{{ $socio->id }}">{{ $socio->nombre }} {{ $socio->apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo de Préstamo</label>
                            <select name="tipo_prestamo_id" id="edit_tipo_prestamo_id" class="form-select" required onchange="cargarInfoTipo(this.value, 'editar')">
                                @foreach($tiposPrestamo as $tipo)
                                    <option value="{{ $tipo->id }}"
                                        data-interes="{{ $tipo->interes }}"
                                        data-monto-min="{{ $tipo->monto_minimo }}"
                                        data-monto-max="{{ $tipo->monto_maximo }}"
                                        data-plazo-min="{{ $tipo->plazo_minimo }}"
                                        data-plazo-max="{{ $tipo->plazo_maximo }}">
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div id="info_tipo_editar" class="alert alert-info d-none mb-3"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto" id="edit_monto" class="form-control" required onchange="calcularPrestamo('editar')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo (meses)</label>
                            <input type="number" name="plazo" id="edit_plazo" class="form-control" required onchange="calcularPrestamo('editar')">
                        </div>
                    </div>
                    
                    <div id="calculo_editar" class="card bg-light d-none mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Cálculo del Préstamo</h4>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted">Tasa de Interés:</div>
                                    <div class="fw-bold" id="interes_mostrar_editar">0%</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted">Interés Total:</div>
                                    <div class="fw-bold" id="interes_total_editar">$0.00</div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="text-muted">Monto Total a Pagar:</div>
                                    <div class="fs-3 text-primary" id="monto_total_editar">$0.00</div>
                                </div>
                                <div class="col-6 mt-2">
                                    <div class="text-muted">Cuota Mensual:</div>
                                    <div class="fs-3 text-success" id="cuota_mensual_editar">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-auto">Actualizar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Aprobar Préstamo --}}
<div class="modal modal-blur fade" id="modalAprobarPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formAprobarPrestamo" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Aprobar Préstamo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Fecha de Desembolso</label>
                        <input type="date" name="fecha_desembolso" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                        <small class="form-hint">Fecha en que se entregará el dinero al socio</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Método de Desembolso</label>
                        <select name="metodo_desembolso" id="metodo_desembolso_aprobar" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TRANSFERENCIA">Transferencia a cuenta externa</option>
                            <option value="DEPOSITO_AHORRO">Depósito a cuenta de ahorro del socio</option>
                            <option value="CHEQUE">Cheque</option>
                        </select>
                        <small class="form-hint">Cómo se entregará el dinero al socio</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Fecha de Primer Pago</label>
                                <input type="date" name="fecha_primer_pago" id="fecha_primer_pago_aprobar" class="form-control" min="{{ date('Y-m-d') }}" required>
                                <small class="form-hint">Fecha en que vence la primera cuota</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Día de Vencimiento Mensual</label>
                                <select name="dia_vencimiento" id="dia_vencimiento_aprobar" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @for($dia = 1; $dia <= 28; $dia++)
                                    <option value="{{ $dia }}" {{ $dia == 20 ? 'selected' : '' }}>Día {{ $dia }}</option>
                                    @endfor
                                </select>
                                <small class="form-hint">Día del mes en que vence cada cuota (recomendado: 20)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                    <polyline points="11 12 12 12 12 16 13 16"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="alert-title">Sistema de Pagos y Mora</h4>
                                <div class="text-muted">
                                    <strong>Período de pago:</strong> Del día 1 al día seleccionado de cada mes<br>
                                    <strong>Mora:</strong> Si el socio paga después del día de vencimiento, entra automáticamente en mora<br>
                                    <strong>Desembolso:</strong> El préstamo se entrega según el método seleccionado. El ahorro del socio permanece disponible.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success ms-auto">Aprobar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Rechazar Préstamo --}}
<div class="modal modal-blur fade" id="modalRechazarPrestamo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formRechazarPrestamo" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Rechazar Préstamo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Motivo del Rechazo</label>
                        <textarea name="motivo_rechazo" class="form-control" rows="4" placeholder="Explique el motivo del rechazo..." required minlength="10"></textarea>
                        <small class="form-hint">Mínimo 10 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger ms-auto">Rechazar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cargar información del tipo de préstamo
function cargarInfoTipo(tipoId, modo) {
    const select = modo === 'crear' ? document.getElementById('tipo_prestamo_crear') : document.getElementById('edit_tipo_prestamo_id');
    const option = select.options[select.selectedIndex];
    
    if (!tipoId || tipoId === '') {
        document.getElementById('info_tipo_' + modo).classList.add('d-none');
        document.getElementById('calculo_' + modo).classList.add('d-none');
        return;
    }
    
    const interes = option.dataset.interes;
    const montoMin = parseFloat(option.dataset.montoMin);
    const montoMax = parseFloat(option.dataset.montoMax);
    const plazoMin = option.dataset.plazoMin;
    const plazoMax = option.dataset.plazoMax;
    
    const infoDiv = document.getElementById('info_tipo_' + modo);
    infoDiv.innerHTML = `
        <strong>Límites:</strong><br>
        Monto: $${montoMin.toFixed(2)} - $${montoMax.toFixed(2)}<br>
        Plazo: ${plazoMin} - ${plazoMax} meses<br>
        Tasa: ${interes}% anual
    `;
    infoDiv.classList.remove('d-none');
    
    calcularPrestamo(modo);
}

// Calcular préstamo (interés simple)
function calcularPrestamo(modo) {
    const tipoSelect = modo === 'crear' ? document.getElementById('tipo_prestamo_crear') : document.getElementById('edit_tipo_prestamo_id');
    const monto = parseFloat(document.getElementById(modo === 'crear' ? 'monto_crear' : 'edit_monto').value);
    const plazo = parseInt(document.getElementById(modo === 'crear' ? 'plazo_crear' : 'edit_plazo').value);
    
    if (!tipoSelect.value || !monto || !plazo || monto <= 0 || plazo <= 0) {
        document.getElementById('calculo_' + modo).classList.add('d-none');
        return;
    }
    
    const option = tipoSelect.options[tipoSelect.selectedIndex];
    const interes = parseFloat(option.dataset.interes);
    
    // Cálculo de interés simple anual
    const interesTotal = (monto * (interes / 100) * plazo) / 12;
    const montoTotal = monto + interesTotal;
    const cuotaMensual = montoTotal / plazo;
    
    document.getElementById('interes_mostrar_' + modo).textContent = interes.toFixed(2) + '%';
    document.getElementById('interes_total_' + modo).textContent = '$' + interesTotal.toFixed(2);
    document.getElementById('monto_total_' + modo).textContent = '$' + montoTotal.toFixed(2);
    document.getElementById('cuota_mensual_' + modo).textContent = '$' + cuotaMensual.toFixed(2);
    
    document.getElementById('calculo_' + modo).classList.remove('d-none');
}

// Editar préstamo
function editarPrestamo(prestamoId) {
    fetch(`/prestamos/${prestamoId}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.prestamo) {
            document.getElementById('formEditarPrestamo').action = `/prestamos/${prestamoId}`;
            document.getElementById('edit_socio_id').value = data.prestamo.socio_id;
            document.getElementById('edit_tipo_prestamo_id').value = data.prestamo.tipo_prestamo_id;
            document.getElementById('edit_monto').value = data.prestamo.monto;
            document.getElementById('edit_plazo').value = data.prestamo.plazo;
            document.getElementById('edit_observaciones').value = data.prestamo.observaciones || '';
            
            cargarInfoTipo(data.prestamo.tipo_prestamo_id, 'editar');
            
            // Abrir modal usando botón trigger
            document.getElementById('triggerModalEditar').click();
        } else {
            alert(data.error || 'Error: Datos del préstamo no encontrados');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('Error al cargar los datos del préstamo: ' + error.message);
    });
}

// Mostrar modal aprobar
function mostrarModalAprobar(prestamoId) {
    console.log('Abriendo modal aprobar para préstamo:', prestamoId);
    
    const form = document.getElementById('formAprobarPrestamo');
    form.action = `/prestamos/${prestamoId}/aprobar`;
    
    // Limpiar campos
    const fechaInput = form.querySelector('[name="fecha_desembolso"]');
    const obsInput = form.querySelector('[name="observaciones"]');
    
    if (fechaInput) fechaInput.value = '{{ date("Y-m-d") }}';
    if (obsInput) obsInput.value = '';
    
    // Sugerir fechas por defecto para los pagos
    const fechaPrimerPagoInput = document.getElementById('fecha_primer_pago_aprobar');
    const diaVencimientoSelect = document.getElementById('dia_vencimiento_aprobar');
    
    if (fechaPrimerPagoInput && diaVencimientoSelect) {
        // Sugerir primer pago 30 días después del desembolso
        const fechaDesembolso = new Date('{{ date("Y-m-d") }}');
        const fechaPrimerPagoSugerida = new Date(fechaDesembolso);
        fechaPrimerPagoSugerida.setDate(fechaPrimerPagoSugerida.getDate() + 30);
        fechaPrimerPagoInput.value = fechaPrimerPagoSugerida.toISOString().split('T')[0];
        
        // El día de vencimiento ya está preseleccionado en 20
    }
    
    // Abrir modal usando botón trigger
    document.getElementById('triggerModalAprobar').click();
}

// Mostrar modal rechazar
function mostrarModalRechazar(prestamoId) {
    console.log('Abriendo modal rechazar para préstamo:', prestamoId);
    
    const form = document.getElementById('formRechazarPrestamo');
    form.action = `/prestamos/${prestamoId}/rechazar`;
    
    // Limpiar campo
    const motivoInput = form.querySelector('[name="motivo_rechazo"]');
    if (motivoInput) motivoInput.value = '';
    
    // Abrir modal usando botón trigger
    document.getElementById('triggerModalRechazar').click();
}

// Fix aria-hidden warning: blur focus from close buttons before modal hides
document.addEventListener('DOMContentLoaded', function() {
    // Handle all modals
    const modales = ['modalCrearPrestamo', 'modalEditarPrestamo', 'modalAprobarPrestamo', 'modalRechazarPrestamo'];
    
    modales.forEach(modalId => {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.addEventListener('hide.bs.modal', function (e) {
                // Remove focus from any focused element inside the modal
                if (document.activeElement && this.contains(document.activeElement)) {
                    document.activeElement.blur();
                }
            });
        }
    });
    
    // Optional: Automatically hide success/error alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});

// Validación de fechas en modal de aprobación
document.addEventListener('DOMContentLoaded', function() {
    const fechaDesembolsoInput = document.querySelector('#modalAprobarPrestamo [name="fecha_desembolso"]');
    const fechaPrimerPagoInput = document.getElementById('fecha_primer_pago_aprobar');
    
    if (fechaDesembolsoInput && fechaPrimerPagoInput) {
        // Cuando cambia la fecha de desembolso, actualizar el mínimo del primer pago
        fechaDesembolsoInput.addEventListener('change', function() {
            fechaPrimerPagoInput.setAttribute('min', this.value);
            // Sugerir primer pago 30 días después
            const fechaDesembolso = new Date(this.value);
            const fechaPrimerPagoSugerida = new Date(fechaDesembolso);
            fechaPrimerPagoSugerida.setDate(fechaPrimerPagoSugerida.getDate() + 30);
            fechaPrimerPagoInput.value = fechaPrimerPagoSugerida.toISOString().split('T')[0];
        });
    }
});
</script>
@endpush
