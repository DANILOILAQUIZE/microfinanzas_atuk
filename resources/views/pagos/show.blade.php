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
                    Detalle del Pago #{{ $pago->id }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><rect x="7" y="13" width="10" height="8" rx="2" /></svg>
                    Imprimir Recibo
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">RECIBO DE PAGO</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-6">
                                <h4>Información del Socio</h4>
                                <p class="mb-1">
                                    <strong>Nombre:</strong><br>
                                    {{ $pago->cuota->prestamo->socio->nombres }} {{ $pago->cuota->prestamo->socio->apellidos }}
                                </p>
                                <p class="mb-1">
                                    <strong>Cédula:</strong><br>
                                    {{ $pago->cuota->prestamo->socio->cedula }}
                                </p>
                                <p class="mb-0">
                                    <strong>Teléfono:</strong><br>
                                    {{ $pago->cuota->prestamo->socio->telefono }}
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <h4>Información del Pago</h4>
                                <p class="mb-1">
                                    <strong>Recibo #:</strong> {{ $pago->id }}<br>
                                    <strong>Fecha:</strong> {{ $pago->fecha_pago->format('d/m/Y H:i') }}<br>
                                    <strong>Cajero:</strong> {{ $pago->usuario->nombre }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Detalles del Préstamo</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Préstamo #:</th>
                                            <td>{{ $pago->cuota->prestamo_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipo de Préstamo:</th>
                                            <td>{{ $pago->cuota->prestamo->tipoPrestamo->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th>Monto Total del Préstamo:</th>
                                            <td>${{ number_format($pago->cuota->prestamo->monto_total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Saldo Anterior:</th>
                                            <td>${{ number_format($pago->cuota->prestamo->saldo + $pago->monto, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Saldo Actual:</th>
                                            <td class="fw-bold">${{ number_format($pago->cuota->prestamo->saldo, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h4>Detalle de la Cuota Pagada</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Cuota #:</th>
                                            <td>{{ $pago->cuota->numero_cuota }} de {{ $pago->cuota->prestamo->plazo }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Vencimiento:</th>
                                            <td>{{ $pago->cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Capital:</th>
                                            <td>${{ number_format($pago->cuota->capital, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Interés:</th>
                                            <td>${{ number_format($pago->cuota->interes, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mora:</th>
                                            <td>${{ number_format($pago->cuota->mora, 2) }}</td>
                                        </tr>
                                        <tr class="table-active">
                                            <th>TOTAL PAGADO:</th>
                                            <th class="text-success">${{ number_format($pago->monto, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th>Método de Pago:</th>
                                            <td>
                                                @if($pago->metodo_pago === 'EFECTIVO')
                                                    Efectivo
                                                @elseif($pago->metodo_pago === 'TRANSFERENCIA')
                                                    Transferencia Bancaria
                                                @elseif($pago->metodo_pago === 'CHEQUE')
                                                    Cheque
                                                @else
                                                    Tarjeta de Crédito/Débito
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-6">
                                <p class="mb-0">
                                    <strong>Firma del Cajero</strong><br><br>
                                    _______________________<br>
                                    {{ $pago->usuario->nombre }}
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-0">
                                    <strong>Firma del Socio</strong><br><br>
                                    _______________________<br>
                                    {{ $pago->cuota->prestamo->socio->nombres }} {{ $pago->cuota->prestamo->socio->apellidos }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 text-center text-muted">
                            <small>Este documento es un comprobante oficial de pago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .page-header, .btn, .navbar, aside {
            display: none !important;
        }
        .card {
            border: none;
            box-shadow: none;
        }
    }
</style>
@endsection
