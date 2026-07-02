@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reportes BI</div>
                <h2 class="page-title">KPIs Históricos</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
                <button onclick="window.print()" class="btn btn-info">Imprimir</button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filtros -->
        <div class="card mb-3 d-print-none">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label>Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-info w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPIs Actuales -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Cartera Total</div>
                        <div class="h1">${{ number_format($kpiActual->cartera_total, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Socios Activos</div>
                        <div class="h1">{{ $kpiActual->socios_activos }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Índice Morosidad</div>
                        <div class="h1 text-danger">{{ number_format($kpiActual->indice_morosidad, 2) }}%</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Saldo Ahorro</div>
                        <div class="h1">${{ number_format($kpiActual->saldo_ahorro, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolución de KPIs Principales</h3>
            </div>
            <div class="card-body">
                <canvas id="chartKPIs" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartKPIs'), {
    type: 'line',
    data: {
        labels: {!! json_encode($kpis->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))) !!},
        datasets: [
            {
                label: 'Cartera Total',
                data: {!! json_encode($kpis->pluck('cartera_total')) !!},
                borderColor: '#206bc4',
                yAxisID: 'y'
            },
            {
                label: 'Socios Activos',
                data: {!! json_encode($kpis->pluck('socios_activos')) !!},
                borderColor: '#47c45d',
                yAxisID: 'y1'
            },
            {
                label: 'Índice Morosidad (%)',
                data: {!! json_encode($kpis->pluck('indice_morosidad')) !!},
                borderColor: '#d63939',
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Monto ($)' } },
            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Cantidad / %' } }
        }
    }
});
</script>
@endpush
@endsection
