@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reportes BI</div>
                <h2 class="page-title">Evolución de Cartera</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filtros de fecha -->
        <div class="card mb-3 d-print-none">
            <div class="card-body">
                <form method="GET" action="{{ route('reportes.cartera') }}">
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
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
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
                        <div class="subheader">Cartera Total</div>
                        <div class="h1 mb-0">${{ number_format($stats['cartera_total'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Préstamos Activos</div>
                        <div class="h1 mb-0">{{ $stats['prestamos_activos'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Préstamos Vencidos</div>
                        <div class="h1 mb-0 text-danger">{{ $stats['prestamos_vencidos'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Desembolsos Este Mes</div>
                        <div class="h1 mb-0">${{ number_format($stats['monto_desembolsado_mes'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Evolución de Cartera -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Evolución de Cartera</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEvolucion" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribución por Tipo -->
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribución por Tipo</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartDistribucion"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Socios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top 10 Socios por Saldo</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Socio</th>
                            <th>Cédula</th>
                            <th class="text-end">Saldo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSocios as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('socios.show', $item->socio_id) }}">
                                    {{ $item->socio->nombres }} {{ $item->socio->apellidos }}
                                </a>
                            </td>
                            <td>{{ $item->socio->cedula }}</td>
                            <td class="text-end"><strong>${{ number_format($item->total_saldo, 2) }}</strong></td>
                        </tr>
                        @endforeach
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
            label: 'Cartera Total',
            data: {!! json_encode($evolucion->pluck('cartera_total')) !!},
            borderColor: '#206bc4',
            backgroundColor: 'rgba(32, 107, 196, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Cartera Vigente',
            data: {!! json_encode($evolucion->pluck('cartera_vigente')) !!},
            borderColor: '#47c45d',
            tension: 0.4
        }, {
            label: 'Cartera Vencida',
            data: {!! json_encode($evolucion->pluck('cartera_vencida')) !!},
            borderColor: '#d63939',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
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

// Gráfico de Distribución
const ctxDistribucion = document.getElementById('chartDistribucion').getContext('2d');
new Chart(ctxDistribucion, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($distribucion->pluck('tipoPrestamo.nombre')) !!},
        datasets: [{
            data: {!! json_encode($distribucion->pluck('monto')) !!},
            backgroundColor: [
                '#206bc4',
                '#47c45d',
                '#f59f00',
                '#d63939',
                '#7c3aed'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
@endsection
