@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Préstamos
                </div>
                <h2 class="page-title">
                    Detalle del Préstamo #{{ $prestamo->id }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('prestamos.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
                @if($prestamo->estado_aprobacion === 'APROBADO' && in_array($prestamo->estado, ['ACTIVO', 'VENCIDO']))
                    @can('registrar_pagos')
                        <a href="{{ route('pagos.registrar', $prestamo->id) }}" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="7" y="9" width="14" height="10" rx="2" /><circle cx="14" cy="14" r="2" /><path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" /></svg>
                            Registrar Pago
                        </a>
                    @endcan
                @endif
                @if($prestamo->estado_aprobacion === 'PENDIENTE')
                    @can('aprobar_prestamos')
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAprobar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Aprobar
                        </button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRechazar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                            Rechazar
                        </button>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Información del Socio -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Socio</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Nombre Completo:</strong><br>
                            <a href="{{ route('socios.show', $prestamo->socio_id) }}">
                                {{ $prestamo->socio->nombres }} {{ $prestamo->socio->apellidos }}
                            </a>
                        </div>
                        <div class="mb-3">
                            <strong>Cédula/RUC:</strong><br>
                            {{ $prestamo->socio->cedula }}
                        </div>
                        <div class="mb-3">
                            <strong>Teléfono:</strong><br>
                            {{ $prestamo->socio->telefono }}
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong><br>
                            {{ $prestamo->socio->correo }}
                        </div>
                        <div class="mb-0">
                            <strong>Estado:</strong><br>
                            @if($prestamo->socio->estado === 'ACTIVO')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Préstamo -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos del Préstamo</h3>
                        <div class="card-actions">
                            @if($prestamo->estado_aprobacion === 'PENDIENTE')
                                <span class="badge bg-warning text-white">Pendiente</span>
                            @elseif($prestamo->estado_aprobacion === 'APROBADO')
                                <span class="badge bg-success">Aprobado</span>
                            @else
                                <span class="badge bg-danger">Rechazado</span>
                            @endif
                            
                            @if($prestamo->estado === 'ACTIVO')
                                <span class="badge bg-info ms-2">Activo</span>
                            @elseif($prestamo->estado === 'CANCELADO')
                                <span class="badge bg-success ms-2">Cancelado</span>
                            @elseif($prestamo->estado === 'VENCIDO')
                                <span class="badge bg-danger ms-2">Vencido</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Tipo de Préstamo:</strong><br>
                                {{ $prestamo->tipoPrestamo->nombre }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Fecha de Solicitud:</strong><br>
                                {{ $prestamo->fecha_solicitud ? $prestamo->fecha_solicitud->format('d/m/Y') : '-' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Monto Solicitado:</strong><br>
                                <span class="h3 text-primary">${{ number_format($prestamo->monto, 2) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Plazo:</strong><br>
                                {{ $prestamo->plazo_meses }} meses
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Tasa de Interés:</strong><br>
                                {{ $prestamo->tasa_interes }}% anual
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Interés Total:</strong><br>
                                ${{ number_format($prestamo->interes_total, 2) }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Monto Total a Pagar:</strong><br>
                                <span class="h4 text-success">${{ number_format($prestamo->monto_total, 2) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Cuota Mensual:</strong><br>
                                <span class="h4 text-info">${{ number_format($prestamo->monto_cuota, 2) }}</span>
                            </div>

                            @if($prestamo->estado_aprobacion === 'APROBADO')
                                <div class="col-md-6 mb-3">
                                    <strong>Fecha de Aprobación:</strong><br>
                                    {{ $prestamo->fecha_aprobacion ? $prestamo->fecha_aprobacion->format('d/m/Y') : '-' }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Aprobado por:</strong><br>
                                    {{ $prestamo->usuarioAprobador ? $prestamo->usuarioAprobador->nombre : '-' }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Fecha de Desembolso:</strong><br>
                                    {{ $prestamo->fecha_desembolso ? $prestamo->fecha_desembolso->format('d/m/Y') : '-' }}
                                </div>
                            @endif

                            @if($prestamo->estado_aprobacion === 'RECHAZADO')
                                <div class="col-12 mb-3">
                                    <strong>Motivo de Rechazo:</strong><br>
                                    <div class="alert alert-danger">
                                        {{ $prestamo->motivo_rechazo }}
                                    </div>
                                </div>
                            @endif

                            @if($prestamo->observaciones)
                                <div class="col-12 mb-3">
                                    <strong>Observaciones:</strong><br>
                                    <p class="text-muted">{{ $prestamo->observaciones }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($prestamo->estado_aprobacion === 'APROBADO' && $prestamo->cuotas->count() > 0)
                    <!-- Tabla de Cuotas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Plan de Pagos</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Capital</th>
                                        <th>Interés</th>
                                        <th>Mora</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha Pago</th>
                                        <th class="w-1">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamo->cuotas as $cuota)
                                        <tr class="{{ $cuota->estado === 'VENCIDA' ? 'table-danger' : '' }}">
                                            <td><strong>{{ $cuota->numero_cuota }}</strong></td>
                                            <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td>${{ number_format($cuota->capital, 2) }}</td>
                                            <td>${{ number_format($cuota->interes, 2) }}</td>
                                            <td>
                                                @if($cuota->mora > 0)
                                                    <span class="text-danger fw-bold">${{ number_format($cuota->mora, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>${{ number_format($cuota->monto + $cuota->mora, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($cuota->estado === 'PENDIENTE')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @elseif($cuota->estado === 'PAGADA')
                                                    <span class="badge bg-success">Pagada</span>
                                                @elseif($cuota->estado === 'VENCIDA')
                                                    @php
                                                        $diasVencidos = $cuota->fecha_vencimiento->diffInDays(now());
                                                    @endphp
                                                    <span class="badge bg-danger">Vencida ({{ $diasVencidos }} días)</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                @if(in_array($cuota->estado, ['PENDIENTE', 'VENCIDA']))
                                                    @can('registrar_pagos')
                                                        <a href="{{ route('pagos.registrar', $prestamo->id) }}?cuota={{ $cuota->id }}" class="btn btn-sm btn-success" title="Pagar esta cuota">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                        </a>
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">TOTALES:</td>
                                        <td>${{ number_format($prestamo->cuotas->sum('capital'), 2) }}</td>
                                        <td>${{ number_format($prestamo->cuotas->sum('interes'), 2) }}</td>
                                        <td class="text-danger">${{ number_format($prestamo->cuotas->sum('mora'), 2) }}</td>
                                        <td>${{ number_format($prestamo->cuotas->sum('monto') + $prestamo->cuotas->sum('mora'), 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Garantías del Préstamo -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Garantías</h3>
                        @if($prestamo->estado_aprobacion !== 'RECHAZADO')
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarGarantia">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    Agregar Garantía
                                </button>
                            </div>
                        @endif
                    </div>
                    @if($prestamo->garantias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                        <th>Fecha Registro</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamo->garantias as $garantia)
                                        <tr>
                                            <td>
                                                @if($garantia->tipo === 'VEHICULO')
                                                    <span class="badge bg-primary">Vehículo</span>
                                                @elseif($garantia->tipo === 'INMUEBLE')
                                                    <span class="badge bg-success">Inmueble</span>
                                                @elseif($garantia->tipo === 'MAQUINARIA')
                                                    <span class="badge bg-info">Maquinaria</span>
                                                @elseif($garantia->tipo === 'EQUIPOS')
                                                    <span class="badge bg-warning">Equipos</span>
                                                @else
                                                    <span class="badge bg-secondary">Otros</span>
                                                @endif
                                            </td>
                                            <td>{{ $garantia->descripcion }}</td>
                                            <td>${{ number_format($garantia->valor, 2) }}</td>
                                            <td>
                                                @if($garantia->estado === 'ACTIVA')
                                                    <span class="badge bg-success">Activa</span>
                                                @elseif($garantia->estado === 'LIBERADA')
                                                    <span class="badge bg-info">Liberada</span>
                                                @else
                                                    <span class="badge bg-danger">Ejecutada</span>
                                                @endif
                                            </td>
                                            <td>{{ $garantia->fecha_registro->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    @if($garantia->estado === 'ACTIVA' && $prestamo->estado === 'CANCELADO')
                                                        <form action="{{ route('garantias.liberar', $garantia->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Liberar esta garantía?')">
                                                                Liberar
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($prestamo->estado_aprobacion === 'PENDIENTE')
                                                        <button class="btn btn-sm btn-secondary" onclick="editarGarantia({{ $garantia->id }})">
                                                            Editar
                                                        </button>
                                                        <form action="{{ route('garantias.destroy', $garantia->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta garantía?')">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Total Garantías:</strong></td>
                                        <td colspan="4"><strong>${{ number_format($prestamo->garantias->sum('valor'), 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center text-muted py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /></svg>
                            <p>No se han registrado garantías para este préstamo</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Garantía -->
<div class="modal modal-blur fade" id="modalAgregarGarantia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('garantias.store') }}" method="POST">
                @csrf
                <input type="hidden" name="prestamo_id" value="{{ $prestamo->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Garantía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo de Garantía</label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="VEHICULO">Vehículo</option>
                                <option value="INMUEBLE">Inmueble</option>
                                <option value="MAQUINARIA">Maquinaria</option>
                                <option value="EQUIPOS">Equipos</option>
                                <option value="OTROS">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Valor de la Garantía</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="valor" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" required placeholder="Ej: Vehículo marca Toyota, modelo Corolla, año 2020, placa ABC-1234"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Fecha de Registro</label>
                        <input type="date" name="fecha_registro" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Información adicional sobre la garantía"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar Garantía</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Garantía -->
<div class="modal modal-blur fade" id="modalEditarGarantia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditarGarantia" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Garantía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tipo de Garantía</label>
                            <select name="tipo" id="edit_tipo" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="VEHICULO">Vehículo</option>
                                <option value="INMUEBLE">Inmueble</option>
                                <option value="MAQUINARIA">Maquinaria</option>
                                <option value="EQUIPOS">Equipos</option>
                                <option value="OTROS">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Valor de la Garantía</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="valor" id="edit_valor" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Fecha de Registro</label>
                        <input type="date" name="fecha_registro" id="edit_fecha_registro" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Garantía</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarGarantia(id) {
    fetch(`/garantias/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('formEditarGarantia').action = `/garantias/${id}`;
        document.getElementById('edit_tipo').value = data.tipo;
        document.getElementById('edit_valor').value = data.valor;
        document.getElementById('edit_descripcion').value = data.descripcion;
        document.getElementById('edit_fecha_registro').value = data.fecha_registro;
        document.getElementById('edit_observaciones').value = data.observaciones || '';
        
        new bootstrap.Modal(document.getElementById('modalEditarGarantia')).show();
    });
}
</script>

<!-- Modal Aprobar -->
<div class="modal modal-blur fade" id="modalAprobar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('prestamos.aprobar', $prestamo->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha de Desembolso</label>
                        <input type="date" name="fecha_desembolso" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <small class="form-hint">Fecha en la que se entregará el dinero al socio</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Nota:</strong> Al aprobar el préstamo se generarán automáticamente las cuotas del plan de pagos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rechazar -->
<div class="modal modal-blur fade" id="modalRechazar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('prestamos.rechazar', $prestamo->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Rechazar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Motivo del Rechazo</label>
                        <textarea name="motivo_rechazo" class="form-control" rows="4" required placeholder="Indique el motivo por el cual se rechaza este préstamo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
