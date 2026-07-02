@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Pagos
                </div>
                <h2 class="page-title">
                    Registrar Pago - Préstamo #{{ $prestamo->id }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Préstamo</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Socio:</strong><br>
                            {{ $prestamo->socio->nombres }} {{ $prestamo->socio->apellidos }}
                        </div>
                        <div class="mb-3">
                            <strong>Monto Total:</strong><br>
                            ${{ number_format($prestamo->monto_total, 2) }}
                        </div>
                        <div class="mb-3">
                            <strong>Saldo Pendiente:</strong><br>
                            <span class="h3 text-danger">${{ number_format($prestamo->saldo, 2) }}</span>
                        </div>
                        <div class="mb-0">
                            <strong>Cuota Mensual:</strong><br>
                            <span class="h4 text-info">${{ number_format($prestamo->monto_cuota, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Pago de Cuota</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pagos.store') }}" method="POST" id="formPago">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label required">Seleccione la Cuota</label>
                                <select name="cuota_id" id="cuota_id" class="form-select" required>
                                    <option value="">Seleccione una cuota...</option>
                                    @foreach($prestamo->cuotas->whereIn('estado', ['PENDIENTE', 'VENCIDA']) as $cuota)
                                        <option value="{{ $cuota->id }}" 
                                                data-capital="{{ $cuota->capital }}"
                                                data-interes="{{ $cuota->interes }}"
                                                data-mora="{{ $cuota->mora }}"
                                                data-monto="{{ $cuota->monto }}"
                                                data-total="{{ $cuota->monto + $cuota->mora }}"
                                                data-vencimiento="{{ $cuota->fecha_vencimiento->format('d/m/Y') }}"
                                                data-estado="{{ $cuota->estado }}">
                                            Cuota #{{ $cuota->numero_cuota }} - 
                                            Vence: {{ $cuota->fecha_vencimiento->format('d/m/Y') }} - 
                                            ${{ number_format($cuota->monto, 2) }}
                                            @if($cuota->mora > 0)
                                                + Mora: ${{ number_format($cuota->mora, 2) }}
                                            @endif
                                            @if($cuota->estado === 'VENCIDA')
                                                <span class="text-danger">(VENCIDA)</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($prestamo->cuotas->whereIn('estado', ['PENDIENTE', 'VENCIDA'])->count() === 0)
                                    <div class="text-muted mt-2">
                                        No hay cuotas pendientes para este préstamo
                                    </div>
                                @endif
                            </div>

                            <div id="detallesCuota" style="display: none;" class="alert alert-info mb-3">
                                <h4 class="alert-title">Detalles del Pago</h4>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Fecha vencimiento:</strong><br>
                                        <span id="detalle_vencimiento"></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Estado:</strong><br>
                                        <span id="detalle_estado"></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Capital:</strong><br>
                                        $<span id="detalle_capital">0.00</span>
                                    </div>
                                    <div class="col-4">
                                        <strong>Interés:</strong><br>
                                        $<span id="detalle_interes">0.00</span>
                                    </div>
                                    <div class="col-4">
                                        <strong>Mora:</strong><br>
                                        <span class="text-danger">$<span id="detalle_mora">0.00</span></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <strong>TOTAL A PAGAR:</strong><br>
                                        <span class="h3 text-success">$<span id="detalle_total">0.00</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Monto a Pagar</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="monto" id="monto" class="form-control bg-success-lt" step="0.01" min="0" value="{{ old('monto') }}" required readonly>
                                        </div>
                                        <small class="form-hint">Monto total incluyendo mora (si aplica). Calculado automáticamente.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Fecha de Pago</label>
                                        <input type="date" name="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Método de Pago</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metodo_pago" value="EFECTIVO" class="form-selectgroup-input" required>
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="7" y="9" width="14" height="10" rx="2" /><circle cx="14" cy="14" r="2" /><path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" /></svg>
                                            Efectivo
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metodo_pago" value="TRANSFERENCIA" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="7" y1="10" x2="14" y2="10" /><line x1="7" y1="14" x2="10" y2="14" /><rect x="5" y="5" width="14" height="14" rx="2" /></svg>
                                            Transferencia
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metodo_pago" value="CHEQUE" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" /><line x1="7" y1="15" x2="7.01" y2="15" /><line x1="11" y1="15" x2="13" y2="15" /></svg>
                                            Cheque
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metodo_pago" value="TARJETA" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="3" /><line x1="3" y1="10" x2="21" y2="10" /><line x1="7" y1="15" x2="7.01" y2="15" /><line x1="11" y1="15" x2="13" y2="15" /></svg>
                                            Tarjeta
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    Registrar Pago
                                </button>
                                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de cuotas pendientes -->
                @if($prestamo->cuotas->whereIn('estado', ['PENDIENTE', 'VENCIDA'])->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Cuotas Pendientes</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vencimiento</th>
                                        <th>Capital</th>
                                        <th>Interés</th>
                                        <th>Mora</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamo->cuotas->whereIn('estado', ['PENDIENTE', 'VENCIDA']) as $cuota)
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
                                            <td><strong>${{ number_format($cuota->monto + $cuota->mora, 2) }}</strong></td>
                                            <td>
                                                @if($cuota->estado === 'VENCIDA')
                                                    @php
                                                        $diasVencidos = $cuota->fecha_vencimiento->diffInDays(now());
                                                    @endphp
                                                    <span class="badge bg-danger">Vencida ({{ $diasVencidos }} días)</span>
                                                @else
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('cuota_id').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        
        if (selected.value) {
            // Obtener datos de la cuota
            var capital = parseFloat(selected.getAttribute('data-capital'));
            var interes = parseFloat(selected.getAttribute('data-interes'));
            var mora = parseFloat(selected.getAttribute('data-mora'));
            var monto = parseFloat(selected.getAttribute('data-monto'));
            var vencimiento = selected.getAttribute('data-vencimiento');
            var estado = selected.getAttribute('data-estado');
            
            // Calcular total con precisión
            var total = Math.round((monto + mora) * 100) / 100;
            
            // Actualizar el monto a pagar
            document.getElementById('monto').value = total.toFixed(2);
            
            // Mostrar detalles
            document.getElementById('detalle_capital').textContent = capital.toFixed(2);
            document.getElementById('detalle_interes').textContent = interes.toFixed(2);
            document.getElementById('detalle_mora').textContent = mora.toFixed(2);
            document.getElementById('detalle_total').textContent = total.toFixed(2);
            document.getElementById('detalle_vencimiento').textContent = vencimiento;
            
            // Mostrar estado con badge
            if (estado === 'VENCIDA') {
                document.getElementById('detalle_estado').innerHTML = '<span class="badge bg-danger">VENCIDA</span>';
            } else {
                document.getElementById('detalle_estado').innerHTML = '<span class="badge bg-warning">PENDIENTE</span>';
            }
            
            // Mostrar el panel de detalles
            document.getElementById('detallesCuota').style.display = 'block';
        } else {
            // Ocultar detalles si no hay selección
            document.getElementById('detallesCuota').style.display = 'none';
            document.getElementById('monto').value = '';
        }
    });
</script>
@endsection
