@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Alertas de Riesgo</div>
                <h2 class="page-title">Detalle de Alerta</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('alertas.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información de la Alerta</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Nivel de Riesgo:</strong>
                            </div>
                            <div class="col-md-8">
                                @if($alerta->nivel == 'CRITICO')
                                    <span class="badge bg-danger fs-3">Crítico</span>
                                @elseif($alerta->nivel == 'ALTO')
                                    <span class="badge bg-warning fs-3">Alto</span>
                                @elseif($alerta->nivel == 'MEDIO')
                                    <span class="badge bg-info fs-3">Medio</span>
                                @else
                                    <span class="badge bg-secondary fs-3">Bajo</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Tipo de Alerta:</strong>
                            </div>
                            <div class="col-md-8">
                                <span class="badge bg-azure">{{ str_replace('_', ' ', $alerta->tipo_alerta) }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Fecha de Alerta:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $alerta->fecha_alerta->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Estado:</strong>
                            </div>
                            <div class="col-md-8">
                                @if($alerta->leida)
                                    <span class="badge bg-success">Leída</span>
                                @else
                                    <span class="badge bg-secondary">No leída</span>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Mensaje de Alerta:</strong>
                                <div class="alert alert-{{ $alerta->nivel == 'CRITICO' ? 'danger' : ($alerta->nivel == 'ALTO' ? 'warning' : 'info') }} mt-2">
                                    {{ $alerta->mensaje }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($alerta->prestamo)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Información del Préstamo</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Código:</strong></div>
                            <div class="col-md-8">{{ $alerta->prestamo->codigo }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Tipo:</strong></div>
                            <div class="col-md-8">{{ $alerta->prestamo->tipoPrestamo->nombre }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Monto Original:</strong></div>
                            <div class="col-md-8">${{ number_format($alerta->prestamo->monto, 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Saldo Pendiente:</strong></div>
                            <div class="col-md-8"><strong class="text-primary">${{ number_format($alerta->prestamo->saldo, 2) }}</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Estado:</strong></div>
                            <div class="col-md-8">
                                @if($alerta->prestamo->estado == 'ACTIVO')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($alerta->prestamo->estado == 'VENCIDO')
                                    <span class="badge bg-danger">Vencido</span>
                                @elseif($alerta->prestamo->estado == 'CANCELADO')
                                    <span class="badge bg-secondary">Cancelado</span>
                                @else
                                    <span class="badge bg-warning">{{ $alerta->prestamo->estado }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('prestamos.show', $alerta->prestamo_id) }}" class="btn btn-primary">
                                    Ver Préstamo Completo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                @if($alerta->socio)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información del Socio</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Nombre:</strong><br>
                            {{ $alerta->socio->nombres }} {{ $alerta->socio->apellidos }}
                        </div>
                        <div class="mb-2">
                            <strong>Cédula:</strong><br>
                            {{ $alerta->socio->cedula }}
                        </div>
                        <div class="mb-2">
                            <strong>Teléfono:</strong><br>
                            {{ $alerta->socio->telefono }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong><br>
                            {{ $alerta->socio->correo }}
                        </div>
                        <div class="mb-2">
                            <strong>Estado:</strong><br>
                            @if($alerta->socio->estado == 'ACTIVO')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </div>
                        <hr>
                        <a href="{{ route('socios.show', $alerta->socio_id) }}" class="btn btn-primary w-100">
                            Ver Perfil Completo
                        </a>
                    </div>
                </div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Acciones</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('alertas.destroy', $alerta->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta alerta?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                Eliminar Alerta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
