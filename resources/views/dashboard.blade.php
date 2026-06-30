@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="page-pretitle">Bienvenido, {{ auth()->user()->nombre ?? 'Usuario' }}</div>
    <h2 class="page-title">Dashboard</h2>
@endsection

@section('content')

{{-- KPI Cards --}}
<div class="row row-deck row-cards mb-4">

    {{-- Cartera Total --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Cartera Total</div>
                </div>
                <div class="h1 mb-3">$0.00</div>
                <div class="d-flex mb-2">
                    <div>Monto total de préstamos activos</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: 100%" role="progressbar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Socios Activos --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Socios Activos</div>
                </div>
                <div class="h1 mb-3">0</div>
                <div class="d-flex mb-2">
                    <div>Total de socios registrados</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-green" style="width: 100%" role="progressbar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Índice de Morosidad --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Índice de Morosidad</div>
                </div>
                <div class="h1 mb-3">0.00%</div>
                <div class="d-flex mb-2">
                    <div>Porcentaje de cartera en mora</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-yellow" style="width: 0%" role="progressbar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cuotas Vencidas --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Cuotas Vencidas</div>
                </div>
                <div class="h1 mb-3">0</div>
                <div class="d-flex mb-2">
                    <div>Cuotas pendientes de pago</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-red" style="width: 0%" role="progressbar"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Gráficas --}}
<div class="row row-deck row-cards mb-4">

    {{-- Gráfica de Cartera --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolución de la Cartera</h3>
            </div>
            <div class="card-body">
                <div id="chart-cartera" style="height: 280px;">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 3v18h18"/>
                                <path d="M20 18v3"/>
                                <path d="M16 16v5"/>
                                <path d="M12 13v8"/>
                                <path d="M8 15v6"/>
                            </svg>
                            <p>Los datos del gráfico estarán disponibles cuando se registren préstamos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribución por Tipo de Préstamo --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cartera por Tipo</h3>
            </div>
            <div class="card-body">
                <div id="chart-tipos" style="height: 280px;">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a.9 .9 0 0 0 -1 -.8"/>
                                <path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5"/>
                            </svg>
                            <p>Sin datos disponibles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Tablas de resumen --}}
<div class="row row-deck row-cards">

    {{-- Últimos Préstamos --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos Préstamos</h3>
                <div class="card-options">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        Ver todos
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay préstamos registrados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Alertas de Riesgo --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Alertas de Riesgo</h3>
                <div class="card-options">
                    <a href="#" class="btn btn-sm btn-outline-danger">
                        Ver todas
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="text-center text-muted py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10"/>
                    </svg>
                    <p>Sin alertas activas</p>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
