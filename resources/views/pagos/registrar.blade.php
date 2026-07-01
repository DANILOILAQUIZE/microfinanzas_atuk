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
                                    @foreach($prestamo->cuotas as $cuota)
                                        <option value="{{ $cuota->id }}" 
                                                data-monto="{{ $cuota->monto }}"
                                                data-vencimiento="{{ $cuota->fecha_vencimiento->format('d/m/Y') }}">
                                            Cuota #{{ $cuota->numero_cuota }} - 
                                            Vence: {{ $cuota->fecha_vencimiento->format('d/m/Y') }} - 
                                            ${{ number_format($cuota->monto, 2) }}
                                            @if($cuota->fecha_vencimiento->isPast())
                                                <span class="text-danger">(VENCIDA)</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($prestamo->cuotas->count() === 0)
                                    <div class="text-muted mt-2">
                                        No hay cuotas pendientes para este préstamo
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Monto a Pagar</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="monto" id="monto" class="form-control" step="0.01" min="0" value="{{ old('monto') }}" required readonly>
                                        </div>
                                        <small class="form-hint">El monto debe ser exactamente el valor de la cuota</small>
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
                @if($prestamo->cuotas->count() > 0)
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
                                        <th>Monto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamo->cuotas as $cuota)
                                        <tr>
                                            <td>{{ $cuota->numero_cuota }}</td>
                                            <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td>${{ number_format($cuota->monto, 2) }}</td>
                                            <td>
                                                @if($cuota->fecha_vencimiento->isPast())
                                                    <span class="badge bg-danger">Vencida</span>
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
        var monto = selected.getAttribute('data-monto');
        document.getElementById('monto').value = monto;
    });
</script>
@endsection
