@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reportes BI</div>
                <h2 class="page-title">Rentabilidad y Pagos</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
                <button onclick="window.print()" class="btn btn-success">Imprimir</button>
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
                            <button type="submit" class="btn btn-success w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Ingresos Período</div>
                        <div class="h1 text-success">${{ number_format($stats['ingresos_periodo'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Pagos Realizados</div>
                        <div class="h1">{{ $stats['pagos_realizados'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Promedio por Pago</div>
                        <div class="h1">${{ number_format($stats['promedio_pago'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Recuperación Este Mes</div>
                        <div class="h1">${{ number_format($stats['recuperacion_mes'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pagos por Mes</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartPagos" height="80"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ingresos por Concepto</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartConceptos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rentabilidad por Tipo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rentabilidad por Tipo de Préstamo</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Monto Total</th>
                            <th class="text-end">Interés Generado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rentabilidadPorTipo as $item)
                        <tr>
                            <td>{{ $item->tipoPrestamo->nombre }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td>${{ number_format($item->monto_total, 2) }}</td>
                            <td class="text-end text-success"><strong>${{ number_format($item->interes_total, 2) }}</strong></td>
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
new Chart(document.getElementById('chartPagos'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($pagosPorMes->pluck('mes')) !!},
        datasets: [{
            label: 'Monto Total',
            data: {!! json_encode($pagosPorMes->pluck('total')) !!},
            backgroundColor: '#47c45d'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartConceptos'), {
    type: 'doughnut',
    data: {
        labels: ['Capital', 'Interés', 'Mora'],
        datasets: [{
            data: [
                {{ $ingresosPorConcepto['capital'] }},
                {{ $ingresosPorConcepto['interes'] }},
                {{ $ingresosPorConcepto['mora'] }}
            ],
            backgroundColor: ['#206bc4', '#47c45d', '#d63939']
        }]
    }
});
</script>
@endpush
@endsection
