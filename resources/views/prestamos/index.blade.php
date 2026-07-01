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
                    <div class="col-md-3">
                        <label class="form-label">Estado Aprobación</label>
                        <select name="estado_aprobacion" class="form-select">
                            <option value="">Todos</option>
                            <option value="PENDIENTE" {{ request('estado_aprobacion') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                            <option value="APROBADO" {{ request('estado_aprobacion') == 'APROBADO' ? 'selected' : '' }}>Aprobado</option>
                            <option value="RECHAZADO" {{ request('estado_aprobacion') == 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
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
                    <div class="col-md-3">
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
                            <th>Aprobación</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->fecha_solicitud ? $prestamo->fecha_solicitud->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="fw-bold">{{ $prestamo->socio->nombre ?? '-' }} {{ $prestamo->socio->apellido ?? '' }}</div>
                                <div class="text-muted small">{{ $prestamo->socio->cedula ?? '' }}</div>
                            </td>
                            <td>{{ $prestamo->tipoPrestamo->nombre ?? '-' }}</td>
                            <td>
                                <div class="fw-bold">${{ number_format($prestamo->monto, 2) }}</div>
                                <div class="text-muted small">Total: ${{ number_format($prestamo->monto_total, 2) }}</div>
                            </td>
                            <td>{{ $prestamo->plazo }} meses</td>
                            <td>
                                @switch($prestamo->estado_aprobacion)
                                    @case('PENDIENTE')
                                        <span class="badge bg-warning">Pendiente</span>
                                        @break
                                    @case('APROBADO')
                                        <span class="badge bg-success">Aprobado</span>
                                        @break
                                    @case('RECHAZADO')
                                        <span class="badge bg-danger">Rechazado</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($prestamo->estado)
                                    @case('PENDIENTE')
                                        <span class="badge bg-yellow">Pendiente</span>
                                        @break
                                    @case('APROBADO')
                                        <span class="badge bg-blue">Activo</span>
                                        @break
                                    @case('FINALIZADO')
                                        <span class="badge bg-green">Finalizado</span>
                                        @break
                                    @case('MORA')
                                        <span class="badge bg-red">En Mora</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('prestamos.show', $prestamo) }}" class="btn btn-sm btn-icon" title="Ver">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </a>
                                    
                                    @if($prestamo->estado_aprobacion == 'PENDIENTE')
                                        @if(hasPermission('editar_prestamos'))
                                        <button type="button" class="btn btn-sm btn-icon" title="Editar" onclick="editarPrestamo({{ $prestamo->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                <path d="M16 5l3 3"/>
                                            </svg>
                                        </button>
                                        @endif
                                        
                                        @if(hasPermission('aprobar_prestamos'))
                                        <button type="button" class="btn btn-sm btn-icon btn-success" title="Aprobar" onclick="mostrarModalAprobar({{ $prestamo->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-danger" title="Rechazar" onclick="mostrarModalRechazar({{ $prestamo->id }})">
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
    fetch(`/prestamos/${prestamoId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('formEditarPrestamo').action = `/prestamos/${prestamoId}`;
            document.getElementById('edit_socio_id').value = data.prestamo.socio_id;
            document.getElementById('edit_tipo_prestamo_id').value = data.prestamo.tipo_prestamo_id;
            document.getElementById('edit_monto').value = data.prestamo.monto;
            document.getElementById('edit_plazo').value = data.prestamo.plazo;
            document.getElementById('edit_observaciones').value = data.prestamo.observaciones || '';
            
            cargarInfoTipo(data.prestamo.tipo_prestamo_id, 'editar');
            
            new bootstrap.Modal(document.getElementById('modalEditarPrestamo')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del préstamo');
        });
}

// Mostrar modal aprobar
function mostrarModalAprobar(prestamoId) {
    document.getElementById('formAprobarPrestamo').action = `/prestamos/${prestamoId}/aprobar`;
    new bootstrap.Modal(document.getElementById('modalAprobarPrestamo')).show();
}

// Mostrar modal rechazar
function mostrarModalRechazar(prestamoId) {
    document.getElementById('formRechazarPrestamo').action = `/prestamos/${prestamoId}/rechazar`;
    new bootstrap.Modal(document.getElementById('modalRechazarPrestamo')).show();
}
</script>
@endpush
