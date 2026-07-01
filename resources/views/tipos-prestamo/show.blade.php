@extends('layouts.app')

@section('title', 'Detalle del Tipo de Préstamo')

@section('header')
    <h2 class="page-title">Detalle del Tipo de Préstamo</h2>
    <div class="text-muted">Información completa del producto crediticio</div>
@endsection

@section('actions')
    @if(hasPermission('gestionar_parametros'))
    <a href="{{ route('tipos-prestamo.edit', $tiposPrestamo) }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
        </svg>
        Editar
    </a>
    @endif
@endsection

@section('content')

<div class="row">
    {{-- Información del Tipo de Préstamo --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <div>{{ $tiposPrestamo->nombre }}</div>
                </div>

                @if($tiposPrestamo->descripcion)
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <div>{{ $tiposPrestamo->descripcion }}</div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Tasa de Interés</label>
                    <div class="text-success fs-3">{{ number_format($tiposPrestamo->interes, 2) }}%</div>
                    <small class="text-muted">Anual</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <div>
                        @if($tiposPrestamo->estado == 'ACTIVO')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Garantía</label>
                    <div>
                        @if($tiposPrestamo->requiere_garantia)
                            <span class="badge bg-warning">Requiere garantía</span>
                        @else
                            <span class="badge bg-info">No requiere garantía</span>
                        @endif
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold">Fecha de Registro</label>
                    <div>{{ $tiposPrestamo->created_at ? $tiposPrestamo->created_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Límites y Restricciones --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Límites y Restricciones</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Monto Mínimo</label>
                        <div class="fs-3">${{ number_format($tiposPrestamo->monto_minimo, 2) }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Monto Máximo</label>
                        <div class="fs-3">${{ number_format($tiposPrestamo->monto_maximo, 2) }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <label class="form-label fw-bold">Plazo Mínimo</label>
                        <div class="fs-3">{{ $tiposPrestamo->plazo_minimo }}</div>
                        <small class="text-muted">meses</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Plazo Máximo</label>
                        <div class="fs-3">{{ $tiposPrestamo->plazo_maximo }}</div>
                        <small class="text-muted">meses</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Préstamos Recientes --}}
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Préstamos Recientes con este Tipo</h3>
            </div>
            <div class="card-body">
                @if($tiposPrestamo->prestamos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Socio</th>
                                <th>Monto</th>
                                <th>Plazo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tiposPrestamo->prestamos as $prestamo)
                            <tr>
                                <td>{{ $prestamo->socio->nombre ?? '-' }} {{ $prestamo->socio->apellido ?? '' }}</td>
                                <td>${{ number_format($prestamo->monto, 2) }}</td>
                                <td>{{ $prestamo->plazo }} meses</td>
                                <td>
                                    <span class="badge bg-{{ $prestamo->estado == 'APROBADO' ? 'success' : ($prestamo->estado == 'PENDIENTE' ? 'warning' : 'secondary') }}">
                                        {{ $prestamo->estado }}
                                    </span>
                                </td>
                                <td>{{ $prestamo->created_at ? $prestamo->created_at->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-muted mt-2">
                    Total de préstamos: <strong>{{ $tiposPrestamo->prestamos()->count() }}</strong>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    No hay préstamos registrados con este tipo
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('tipos-prestamo.index') }}" class="btn btn-secondary">
                    Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
