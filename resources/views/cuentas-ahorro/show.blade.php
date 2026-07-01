@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Detalle de Cuenta de Ahorro</h2>
                <div class="text-muted mt-1">{{ $cuenta->numero_cuenta }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('cuentas-ahorro.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Información del Socio -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Socio</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nombre Completo</label>
                            <div><strong>{{ $cuenta->socio->nombres }} {{ $cuenta->socio->apellidos }}</strong></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">DUI</label>
                            <div>{{ $cuenta->socio->dui }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Teléfono</label>
                            <div>{{ $cuenta->socio->telefono }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Correo</label>
                            <div>{{ $cuenta->socio->correo }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted">Dirección</label>
                            <div>{{ $cuenta->socio->direccion }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Cuenta -->
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Información de la Cuenta</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Número de Cuenta</label>
                                <div><strong>{{ $cuenta->numero_cuenta }}</strong></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Fecha de Apertura</label>
                                <div>{{ $cuenta->fecha_apertura->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Depósito Inicial</label>
                                <div><strong class="text-primary">${{ number_format($cuenta->deposito_inicial, 2) }}</strong></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <div>
                                    @if($cuenta->estado === 'ACTIVA')
                                        <span class="badge bg-success">Activa</span>
                                    @elseif($cuenta->estado === 'BLOQUEADA')
                                        <span class="badge bg-warning">Bloqueada</span>
                                    @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                    @endif
                                </div>
                            </div>
                            @if($cuenta->observaciones)
                            <div class="col-12">
                                <label class="form-label text-muted">Observaciones</label>
                                <div>{{ $cuenta->observaciones }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Saldos -->
                <div class="row row-cards mb-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Saldo Total</div>
                                </div>
                                <div class="h1 mb-0">${{ number_format($cuenta->saldo, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Saldo Disponible</div>
                                </div>
                                <div class="h1 mb-0 text-success">${{ number_format($cuenta->saldo_disponible, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Saldo Bloqueado</div>
                                </div>
                                <div class="h1 mb-0 text-warning">${{ number_format($cuenta->saldo_bloqueado, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted">Total Depósitos</label>
                                    <div class="h3 text-success">${{ number_format($totalDepositos, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted">Total Retiros</label>
                                    <div class="h3 text-danger">${{ number_format($totalRetiros, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label text-muted">Total Movimientos</label>
                                    <div class="h3">{{ $cantidadMovimientos }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Movimientos -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Últimos 10 Movimientos</h3>
                        @if($cuenta->estado === 'ACTIVA')
                        <div class="card-actions">
                            <a href="{{ route('movimientos-ahorro.crear', $cuenta) }}" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                Nuevo Movimiento
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Saldo Anterior</th>
                                    <th>Saldo Posterior</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cuenta->movimientosAhorro as $movimiento)
                                <tr>
                                    <td>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($movimiento->tipo_movimiento === 'DEPOSITO')
                                            <span class="badge bg-success">Depósito</span>
                                        @else
                                            <span class="badge bg-danger">Retiro</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movimiento->tipo_movimiento === 'DEPOSITO')
                                            <span class="text-success">+${{ number_format($movimiento->monto, 2) }}</span>
                                        @else
                                            <span class="text-danger">-${{ number_format($movimiento->monto, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($movimiento->saldo_anterior, 2) }}</td>
                                    <td><strong>${{ number_format($movimiento->saldo_posterior, 2) }}</strong></td>
                                    <td>{{ $movimiento->observaciones ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay movimientos registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($cantidadMovimientos > 10)
                    <div class="card-footer">
                        <a href="{{ route('movimientos-ahorro.index', ['cuenta' => $cuenta->id]) }}" class="btn btn-link">
                            Ver todos los movimientos ({{ $cantidadMovimientos }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
