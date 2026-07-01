@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Comprobante de Movimiento</h2>
                <div class="text-muted mt-1">{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Imprimir
                </button>
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
        <div class="card">
            <div class="card-body">
                <!-- Encabezado -->
                <div class="text-center mb-4">
                    <h1 class="mb-1">COOPERATIVA ATUK</h1>
                    <p class="text-muted mb-3">Comprobante de {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? 'Depósito' : 'Retiro' }}</p>
                    <div>
                        <span class="badge badge-lg {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? 'bg-success' : 'bg-danger' }}">
                            {{ $movimiento->tipo_movimiento }}
                        </span>
                    </div>
                </div>

                <hr>

                <!-- Información del Socio -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4 class="mb-3">Información del Socio</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nombre:</td>
                                <td><strong>{{ $movimiento->cuenta->socio->nombres }} {{ $movimiento->cuenta->socio->apellidos }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">DUI:</td>
                                <td>{{ $movimiento->cuenta->socio->dui }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Teléfono:</td>
                                <td>{{ $movimiento->cuenta->socio->telefono }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3">Información de la Cuenta</h4>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nº Cuenta:</td>
                                <td><strong>{{ $movimiento->cuenta->numero_cuenta }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Fecha/Hora:</td>
                                <td>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Método:</td>
                                <td>{{ $movimiento->metodo_transaccion }}</td>
                            </tr>
                            @if($movimiento->referencia)
                            <tr>
                                <td class="text-muted">Referencia:</td>
                                <td>{{ $movimiento->referencia }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Detalle del Movimiento -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="mb-3">Detalle del Movimiento</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-muted" width="30%">Tipo de Movimiento:</td>
                                <td>
                                    <span class="badge {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $movimiento->tipo_movimiento }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Monto:</td>
                                <td>
                                    <h3 class="mb-0 {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? 'text-success' : 'text-danger' }}">
                                        {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? '+' : '-' }}${{ number_format($movimiento->monto, 2) }}
                                    </h3>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Saldo Anterior:</td>
                                <td><strong>${{ number_format($movimiento->saldo_anterior, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Saldo Posterior:</td>
                                <td><strong class="text-primary">${{ number_format($movimiento->saldo_posterior, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($movimiento->descripcion || $movimiento->observaciones)
                <hr>
                <div class="row mb-4">
                    <div class="col-12">
                        @if($movimiento->descripcion)
                        <div class="mb-3">
                            <label class="form-label text-muted">Descripción:</label>
                            <p>{{ $movimiento->descripcion }}</p>
                        </div>
                        @endif
                        @if($movimiento->observaciones)
                        <div class="mb-0">
                            <label class="form-label text-muted">Observaciones:</label>
                            <p>{{ $movimiento->observaciones }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <hr>

                <!-- Firmas -->
                <div class="row text-center mt-5 pt-4">
                    <div class="col-md-6">
                        <div class="border-top pt-2 d-inline-block" style="min-width: 200px;">
                            <strong>{{ $movimiento->usuario->nombre }}</strong><br>
                            <small class="text-muted">Usuario que registró</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border-top pt-2 d-inline-block" style="min-width: 200px;">
                            <strong>{{ $movimiento->cuenta->socio->nombres }} {{ $movimiento->cuenta->socio->apellidos }}</strong><br>
                            <small class="text-muted">Firma del Socio</small>
                        </div>
                    </div>
                </div>

                <!-- Nota al pie -->
                <div class="mt-5 pt-4 text-center text-muted">
                    <small>
                        Este comprobante es válido como constancia de {{ $movimiento->tipo_movimiento === 'DEPOSITO' ? 'depósito' : 'retiro' }}.<br>
                        Generado el {{ now()->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .page-header, .btn, .navbar, .page-wrapper > .container-xl > .page-header {
        display: none !important;
    }
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endsection
