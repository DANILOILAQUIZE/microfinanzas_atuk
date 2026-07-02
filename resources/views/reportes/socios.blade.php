@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reportes BI</div>
                <h2 class="page-title">Análisis de Socios</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Volver</a>
                <button onclick="window.print()" class="btn btn-azure">Imprimir</button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Estadísticas -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Socios</div>
                        <div class="h1">{{ $stats['total_socios'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Socios Activos</div>
                        <div class="h1 text-success">{{ $stats['socios_activos'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Con Préstamos</div>
                        <div class="h1">{{ $stats['socios_con_prestamos'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Con Ahorro</div>
                        <div class="h1">{{ $stats['socios_con_ahorro'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gráfico Estado -->
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribución por Estado</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartEstado"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico Crecimiento -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Nuevos Socios por Mes</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartCrecimiento" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Socios Más Activos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top 10 Socios Más Activos</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th>Socio</th>
                            <th>Cédula</th>
                            <th>Estado</th>
                            <th class="text-end">Préstamos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sociosActivos as $index => $socio)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('socios.show', $socio->id) }}">
                                    {{ $socio->nombres }} {{ $socio->apellidos }}
                                </a>
                            </td>
                            <td>{{ $socio->cedula }}</td>
                            <td>
                                <span class="badge bg-{{ $socio->estado == 'ACTIVO' ? 'success' : 'danger' }}">
                                    {{ $socio->estado }}
                                </span>
                            </td>
                            <td class="text-end"><strong>{{ $socio->prestamos_count }}</strong></td>
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
new Chart(document.getElementById('chartEstado'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($sociosPorEstado->pluck('estado')) !!},
        datasets: [{
            data: {!! json_encode($sociosPorEstado->pluck('cantidad')) !!},
            backgroundColor: ['#47c45d', '#d63939', '#f59f00']
        }]
    }
});

new Chart(document.getElementById('chartCrecimiento'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($nuevosPorMes->pluck('mes')) !!},
        datasets: [{
            label: 'Nuevos Socios',
            data: {!! json_encode($nuevosPorMes->pluck('cantidad')) !!},
            backgroundColor: '#206bc4'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
