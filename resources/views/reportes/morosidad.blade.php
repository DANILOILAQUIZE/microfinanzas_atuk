@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reportes BI</div>
                <h2 class="page-title">Análisis de Morosidad</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
                <button onclick="window.print()" class="btn btn-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filtros -->
        <div class="card mb-3 d-print-none">
            <div class="card-body">
                <form method="GET" action="{{ route('reportes.morosidad') }}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-danger w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Índice de Morosidad</div>
                        <div class="h1 mb-0 text-danger">{{ number_format($stats['indice_morosidad'], 2) }}%</div>
                        <div class="text-muted mt-1">
                            <small>{{ $stats['cuotas_vencidas'] }} de {{ $stats['cuotas_pendientes'] }} cuotas</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Cartera Vencida</div>
                        <div class="h1 mb-0">${{ number_format($stats['cartera_vencida'], 2) }}</div>
                        <div class="text-muted mt-1">
                            <small>{{ number_format(($stats['cartera_total'] > 0 ? ($stats['cartera_vencida'] / $stats['cartera_total']) * 100 : 0), 2) }}% del total</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Préstamos Vencidos</div>
                        <div class="h1 mb-0">{{ $stats['prestamos_vencidos'] }}</div>
                        <div class="text-muted mt-1">
                            <small>En estado VENCIDO</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Monto de Mora Total</div>
                        <div class="h1 mb-0 text-danger">${{ number_format($stats['monto_mora_total'], 2) }}</div>
                        <div class="text-muted mt-1">
                            <small>Mora acumulada</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Evolución -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Evolución de Morosidad</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEvolucion" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Rangos de Mora -->
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rangos de Mora (Cuotas)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartRangos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Socios Morosos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top 10 Socios con Mayor Morosidad</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Socio</th>
                            <th>Préstamos Vencidos</th>
                            <th class="text-end">Deuda Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sociosMorosos as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('socios.show', $item->socio_id) }}">
                                    {{ $item->socio->nombres }} {{ $item->socio->apellidos }}
                                </a>
                            </td>
                            <td>{{ $item->prestamos_vencidos }}</td>
                            <td class="text-end text-danger"><strong>${{ number_format($item->deuda_total, 2) }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay socios con morosidad</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Gráfico de Evolución
const ctxEvolucion = document.getElementById('chartEvolucion').getContext('2d');
new Chart(ctxEvolucion, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucion->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m/Y'))) !!},
        datasets: [{
            label: 'Cartera Vencida',
            data: {!! json_encode($evolucion->pluck('cartera_vencida')) !!},
            borderColor: '#d63939',
            backgroundColor: 'rgba(214, 57, 57, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => '$' + value.toLocaleString() }
            }
        }
    }
});

// Gráfico de Rangos
const ctxRangos = document.getElementById('chartRangos').getContext('2d');
new Chart(ctxRangos, {
    type: 'doughnut',
    data: {
        labels: ['1-30 días', '31-60 días', '61-90 días', '90+ días'],
        datasets: [{
            data: [
                {{ $rangosMora['1-30'] }},
                {{ $rangosMora['31-60'] }},
                {{ $rangosMora['61-90'] }},
                {{ $rangosMora['90+'] }}
            ],
            backgroundColor: ['#f59f00', '#fd7e14', '#d63939', '#a72828']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: true, position: 'bottom' } }
    }
});
</script>
@endpush
@endsection
