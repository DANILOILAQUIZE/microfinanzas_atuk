@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Bienvenido</div>
                <h2 class="page-title">{{ auth()->user()->nombre ?? 'Usuario' }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                    {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- KPI Cards --}}
        <div class="row row-deck row-cards mb-3">
            {{-- Cartera Total --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Cartera Total</div>
                            <div class="ms-auto lh-1">
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Últimos 7 días</a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item active" href="#">Últimos 7 días</a>
                                        <a class="dropdown-item" href="#">Últimos 30 días</a>
                                        <a class="dropdown-item" href="#">Últimos 3 meses</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">${{ number_format($kpis['carteraTotal'], 0) }}</div>
                            <div class="me-auto">
                                <span class="text-green d-inline-flex align-items-center lh-1">
                                    {{ $kpis['totalPrestamos'] }} préstamos
                                </span>
                            </div>
                        </div>
                    </div>
                    <div id="chart-cartera-mini" class="chart-sm"></div>
                </div>
            </div>

            {{-- Socios Activos --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Socios Activos</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-green">{{ $kpis['cuentasAhorro'] }} cuentas</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-3 me-2">{{ $kpis['sociosActivos'] }}</div>
                            <div class="me-auto">
                                <span class="badge bg-success-lt">Activos</span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
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
                            <div class="ms-auto lh-1">
                                @if($kpis['indiceMorosidad'] < 5)
                                    <span class="badge bg-success">Bajo</span>
                                @elseif($kpis['indiceMorosidad'] < 10)
                                    <span class="badge bg-warning">Moderado</span>
                                @else
                                    <span class="badge bg-danger">Alto</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $kpis['indiceMorosidad'] }}%</div>
                            <div class="me-auto">
                                <span class="text-muted">{{ $kpis['cuotasVencidas'] }} cuotas</span>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar {{ $kpis['indiceMorosidad'] < 5 ? 'bg-success' : ($kpis['indiceMorosidad'] < 10 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ min($kpis['indiceMorosidad'], 100) }}%" 
                                 role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Saldo en Ahorro --}}
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Saldo en Ahorro</div>
                            <div class="ms-auto lh-1">
                                <span class="badge bg-blue">{{ $kpis['movimientosHoy'] }} mov. hoy</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-3 me-2">${{ number_format($kpis['saldoAhorro'], 0) }}</div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue" style="width: 100%" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPIs Secundarios --}}
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 14l2 2l4 -4" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $kpis['prestamosAprobadosMes'] }}</div>
                                <div class="text-muted">Aprobados este mes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $kpis['prestamosPendientes'] }}</div>
                                <div class="text-muted">Pendientes aprobación</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $kpis['pagosHoy'] }}</div>
                                <div class="text-muted">Pagos hoy (${{ number_format($kpis['montoPagosHoy'], 0) }})</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-danger text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">${{ number_format($kpis['montoMoraTotal'], 0) }}</div>
                                <div class="text-muted">Mora acumulada</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráficas --}}
        <div class="row row-deck row-cards mb-3">
            {{-- Evolución de Cartera --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Evolución de la Cartera</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartCartera" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Distribución por Tipo --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribución por Tipo de Préstamo</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTipos" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Morosidad Mensual --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Índice de Morosidad Mensual</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartMorosidad" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Crecimiento de Ahorro --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Crecimiento de Ahorro</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartAhorro" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tablas de Resumen --}}
        <div class="row row-deck row-cards">
            {{-- Top Socios --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 10 Socios por Saldo en Ahorro</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Nº Cuenta</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSocios as $item)
                                <tr>
                                    <td>{{ $item['socio'] }}</td>
                                    <td><small class="text-muted">{{ $item['numero_cuenta'] }}</small></td>
                                    <td class="text-end"><strong>${{ number_format($item['saldo'], 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No hay datos disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Cuotas Próximas a Vencer --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cuotas Próximas a Vencer (7 días)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Cuota</th>
                                    <th>Vencimiento</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cuotasProximasVencer as $item)
                                <tr>
                                    <td>{{ $item['socio'] }}</td>
                                    <td><small class="text-muted">Cuota #{{ $item['numero_cuota'] }}</small></td>
                                    <td>
                                        {{ $item['fecha_vencimiento'] }}
                                        <br><small class="text-{{ $item['dias_restantes'] <= 2 ? 'danger' : 'warning' }}">
                                            {{ $item['dias_restantes'] == 0 ? 'Hoy' : ($item['dias_restantes'] == 1 ? 'Mañana' : 'En ' . $item['dias_restantes'] . ' días') }}
                                        </small>
                                    </td>
                                    <td class="text-end"><strong>${{ number_format($item['monto'], 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay cuotas próximas a vencer</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Préstamos Recientes --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Préstamos Recientes</h3>
                        <div class="card-actions">
                            <a href="{{ route('prestamos.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prestamosRecientes as $item)
                                <tr>
                                    <td>{{ $item['socio'] }}</td>
                                    <td><small class="text-muted">{{ $item['tipo'] }}</small></td>
                                    <td><strong>${{ number_format($item['monto'], 0) }}</strong></td>
                                    <td>
                                        @if($item['estado_aprobacion'] === 'PENDIENTE')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($item['estado_aprobacion'] === 'APROBADO')
                                            <span class="badge bg-success">{{ $item['estado'] }}</span>
                                        @else
                                            <span class="badge bg-danger">Rechazado</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay préstamos registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Movimientos Recientes --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Movimientos de Ahorro Recientes</h3>
                        <div class="card-actions">
                            <a href="{{ route('movimientos-ahorro.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movimientosRecientes as $item)
                                <tr>
                                    <td>{{ $item['socio'] }}</td>
                                    <td>
                                        @if($item['tipo'] === 'DEPOSITO')
                                            <span class="badge bg-success">Depósito</span>
                                        @else
                                            <span class="badge bg-danger">Retiro</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="{{ $item['tipo'] === 'DEPOSITO' ? 'text-success' : 'text-danger' }}">
                                            {{ $item['tipo'] === 'DEPOSITO' ? '+' : '-' }}${{ number_format($item['monto'], 2) }}
                                        </strong>
                                    </td>
                                    <td><small class="text-muted">{{ $item['fecha'] }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay movimientos registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Configuración de colores
    const colors = {
        primary: '#0d2d5e',
        success: '#2fb344',
        danger: '#d63939',
        warning: '#f76707',
        info: '#4299e1',
        blue: '#206bc4',
    };

    // Datos desde el servidor
    const evolucionCartera = @json($evolucionCartera);
    const distribucionTipos = @json($distribucionTipos);
    const morosidadMensual = @json($morosidadMensual);
    const crecimientoAhorro = @json($crecimientoAhorro);

    // Gráfica 1: Evolución de Cartera (Línea)
    new Chart(document.getElementById('chartCartera'), {
        type: 'line',
        data: {
            labels: evolucionCartera.map(d => d.mes),
            datasets: [{
                label: 'Cartera Total',
                data: evolucionCartera.map(d => d.cartera),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true
            }, {
                label: 'Saldo Pendiente',
                data: evolucionCartera.map(d => d.saldo),
                borderColor: colors.warning,
                backgroundColor: colors.warning + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfica 2: Distribución por Tipo (Donut)
    new Chart(document.getElementById('chartTipos'), {
        type: 'doughnut',
        data: {
            labels: distribucionTipos.map(d => d.tipo),
            datasets: [{
                data: distribucionTipos.map(d => d.monto),
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.danger,
                    colors.info,
                    colors.blue
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': $' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfica 3: Morosidad Mensual (Barras)
    new Chart(document.getElementById('chartMorosidad'), {
        type: 'bar',
        data: {
            labels: morosidadMensual.map(d => d.mes),
            datasets: [{
                label: 'Índice de Morosidad (%)',
                data: morosidadMensual.map(d => d.porcentaje),
                backgroundColor: colors.danger,
                borderColor: colors.danger,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Gráfica 4: Crecimiento de Ahorro (Área)
    new Chart(document.getElementById('chartAhorro'), {
        type: 'line',
        data: {
            labels: crecimientoAhorro.map(d => d.mes),
            datasets: [{
                label: 'Saldo Total en Ahorro',
                data: crecimientoAhorro.map(d => d.saldo),
                borderColor: colors.success,
                backgroundColor: colors.success + '30',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
