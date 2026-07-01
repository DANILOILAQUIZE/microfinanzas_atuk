@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Nuevo Movimiento de Ahorro</h2>
                <div class="text-muted mt-1">Registrar depósito o retiro</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('movimientos-ahorro.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
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

        <form method="POST" action="{{ route('movimientos-ahorro.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información del Movimiento</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Cuenta</label>
                                    <select name="cuenta_id" id="cuenta_id" class="form-select @error('cuenta_id') is-invalid @enderror" required {{ $cuenta ? 'disabled' : '' }}>
                                        <option value="">Seleccione una cuenta...</option>
                                        @if($cuenta)
                                            <option value="{{ $cuenta->id }}" selected>{{ $cuenta->numero_cuenta }} - {{ $cuenta->socio->nombres }} {{ $cuenta->socio->apellidos }}</option>
                                        @endif
                                    </select>
                                    @if($cuenta)
                                        <input type="hidden" name="cuenta_id" value="{{ $cuenta->id }}">
                                    @endif
                                    @error('cuenta_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Tipo de Movimiento</label>
                                    <div class="form-selectgroup">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="tipo_movimiento" value="DEPOSITO" class="form-selectgroup-input" id="tipo_deposito" required checked>
                                            <span class="form-selectgroup-label">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M18 13l-6 6" /><path d="M6 13l6 6" /></svg>
                                                Depósito
                                            </span>
                                        </label>
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="tipo_movimiento" value="RETIRO" class="form-selectgroup-input" id="tipo_retiro" required>
                                            <span class="form-selectgroup-label">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M18 11l-6 -6" /><path d="M6 11l6 -6" /></svg>
                                                Retiro
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Monto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="monto" id="monto" class="form-control @error('monto') is-invalid @enderror" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('monto') }}">
                                    </div>
                                    @error('monto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint" id="hint_monto"></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Método de Transacción</label>
                                    <select name="metodo_transaccion" class="form-select @error('metodo_transaccion') is-invalid @enderror" required>
                                        <option value="EFECTIVO" selected>Efectivo</option>
                                        <option value="TRANSFERENCIA">Transferencia</option>
                                        <option value="CHEQUE">Cheque</option>
                                        <option value="TARJETA">Tarjeta</option>
                                    </select>
                                    @error('metodo_transaccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" name="referencia" class="form-control @error('referencia') is-invalid @enderror" maxlength="50" placeholder="Número de cheque, comprobante..." value="{{ old('referencia') }}">
                                    @error('referencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2" maxlength="500">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="2" maxlength="500">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Registrar Movimiento
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card" id="info_cuenta_card" style="display: {{ $cuenta ? 'block' : 'none' }};">
                        <div class="card-header">
                            <h3 class="card-title">Información de la Cuenta</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Número de Cuenta</label>
                                <div><strong id="info_numero_cuenta">{{ $cuenta->numero_cuenta ?? '-' }}</strong></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Socio</label>
                                <div id="info_socio">{{ $cuenta ? $cuenta->socio->nombres . ' ' . $cuenta->socio->apellidos : '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Saldo Total</label>
                                <div class="h3 mb-0" id="info_saldo">${{ $cuenta ? number_format($cuenta->saldo, 2) : '0.00' }}</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted">Saldo Disponible</label>
                                <div class="h3 mb-0 text-success" id="info_saldo_disponible">${{ $cuenta ? number_format($cuenta->saldo_disponible, 2) : '0.00' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Información Importante</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                    Monto mínimo: $0.01
                                </li>
                                <li class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                    Monto máximo retiro: $5,000.00
                                </li>
                                <li class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                    Saldo mínimo: $10.00
                                </li>
                                <li class="mb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                    Solo se pueden anular movimientos del día
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if(!$cuenta)
    // Cargar cuentas activas al cargar la página
    fetch('{{ route('movimientos-ahorro.cuentas-activas') }}')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('cuenta_id');
            data.forEach(cuenta => {
                const option = document.createElement('option');
                option.value = cuenta.id;
                option.textContent = `${cuenta.numero_cuenta} - ${cuenta.socio.nombres} ${cuenta.socio.apellidos}`;
                option.dataset.saldo = cuenta.saldo;
                option.dataset.saldoDisponible = cuenta.saldo_disponible;
                option.dataset.numeroCuenta = cuenta.numero_cuenta;
                option.dataset.socio = `${cuenta.socio.nombres} ${cuenta.socio.apellidos}`;
                select.appendChild(option);
            });
        });

    // Mostrar información de cuenta al seleccionar
    document.getElementById('cuenta_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('info_numero_cuenta').textContent = option.dataset.numeroCuenta;
            document.getElementById('info_socio').textContent = option.dataset.socio;
            document.getElementById('info_saldo').textContent = '$' + parseFloat(option.dataset.saldo).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('info_saldo_disponible').textContent = '$' + parseFloat(option.dataset.saldoDisponible).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('info_cuenta_card').style.display = 'block';
        } else {
            document.getElementById('info_cuenta_card').style.display = 'none';
        }
    });
    @endif

    // Validar monto según tipo de movimiento
    const tipoDeposito = document.getElementById('tipo_deposito');
    const tipoRetiro = document.getElementById('tipo_retiro');
    const montoInput = document.getElementById('monto');
    const hintMonto = document.getElementById('hint_monto');

    function actualizarHintMonto() {
        const cuentaSelect = document.getElementById('cuenta_id');
        const option = cuentaSelect.options[cuentaSelect.selectedIndex];
        
        if (tipoRetiro.checked && option.value) {
            const saldoDisponible = parseFloat(option.dataset.saldoDisponible || 0);
            hintMonto.textContent = `Saldo disponible: $${saldoDisponible.toFixed(2)}`;
            hintMonto.className = 'form-hint text-warning';
        } else {
            hintMonto.textContent = '';
        }
    }

    tipoDeposito.addEventListener('change', actualizarHintMonto);
    tipoRetiro.addEventListener('change', actualizarHintMonto);
    document.getElementById('cuenta_id').addEventListener('change', actualizarHintMonto);
</script>
@endpush
