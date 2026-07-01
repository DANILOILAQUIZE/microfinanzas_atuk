@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Business Intelligence</div>
                <h2 class="page-title">Reportes y Análisis</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Reporte de Cartera -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">Evolución de Cartera</h3>
                                <div class="text-secondary">Análisis de cartera activa</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            Seguimiento de montos desembolsados, cartera activa, distribución por tipo de préstamo y principales clientes.
                        </div>
                        <a href="{{ route('reportes.cartera') }}" class="btn btn-primary w-100">
                            Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de Morosidad -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">Análisis de Morosidad</h3>
                                <div class="text-secondary">Índices y tendencias de mora</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            Evolución del índice de morosidad, rangos de días en mora, socios con mayor morosidad y estadísticas detalladas.
                        </div>
                        <a href="{{ route('reportes.morosidad') }}" class="btn btn-danger w-100">
                            Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de Rentabilidad -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3v18h18" /><path d="M20 18v3" /><path d="M16 16v5" /><path d="M12 13v8" /><path d="M8 16v5" /><path d="M3 11l6 -6l4 4l7 -7" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">Rentabilidad y Pagos</h3>
                                <div class="text-secondary">Ingresos y recuperación</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            Ingresos por capital, intereses y mora. Análisis de pagos mensuales y tipos de préstamo más rentables.
                        </div>
                        <a href="{{ route('reportes.rentabilidad') }}" class="btn btn-success w-100">
                            Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de KPIs -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">KPIs Históricos</h3>
                                <div class="text-secondary">Indicadores clave</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            Seguimiento histórico de indicadores clave: cartera, socios, morosidad, ahorro y evolución en el tiempo.
                        </div>
                        <a href="{{ route('reportes.kpis') }}" class="btn btn-info w-100">
                            Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reporte de Socios -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-azure" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">Análisis de Socios</h3>
                                <div class="text-secondary">Comportamiento de clientes</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            Estadísticas de socios activos, distribución por estado, socios más activos y tendencias de crecimiento.
                        </div>
                        <a href="{{ route('reportes.socios') }}" class="btn btn-azure w-100">
                            Ver Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-secondary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l0 .01" /></svg>
                            </div>
                            <div>
                                <h3 class="card-title mb-1">Acerca de Reportes BI</h3>
                                <div class="text-secondary">Sistema de análisis</div>
                            </div>
                        </div>
                        <div class="text-secondary mb-3">
                            <ul class="mb-0">
                                <li>Datos actualizados en tiempo real</li>
                                <li>Históricos desde Data Warehouse</li>
                                <li>Gráficos interactivos</li>
                                <li>Exportación a PDF disponible</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Información del Sistema BI</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Fuentes de Datos</h4>
                        <ul>
                            <li><strong>Data Warehouse:</strong> Datos históricos consolidados (snapshots diarios)</li>
                            <li><strong>Base Operacional:</strong> Datos en tiempo real de operaciones actuales</li>
                            <li><strong>KPIs Calculados:</strong> Indicadores procesados automáticamente</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Actualización de Datos</h4>
                        <ul>
                            <li><strong>Data Warehouse:</strong> Actualización diaria automática (00:30 AM)</li>
                            <li><strong>Reportes en Tiempo Real:</strong> Datos actualizados instantáneamente</li>
                            <li><strong>Alertas y Notificaciones:</strong> Generación automática programada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
