@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Operaciones
                </div>
                <h2 class="page-title">
                    Historial de Pagos
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros de Búsqueda</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pagos.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="metodo_pago" class="form-select">
                                <option value="">Todos</option>
                                <option value="EFECTIVO" {{ request('metodo_pago') == 'EFECTIVO' ? 'selected' : '' }}>Efectivo</option>
                                <option value="TRANSFERENCIA" {{ request('metodo_pago') == 'TRANSFERENCIA' ? 'selected' : '' }}>Transferencia</option>
                                <option value="CHEQUE" {{ request('metodo_pago') == 'CHEQUE' ? 'selected' : '' }}>Cheque</option>
                                <option value="TARJETA" {{ request('metodo_pago') == 'TARJETA' ? 'selected' : '' }}>Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Buscar Socio</label>
                            <input type="text" name="buscar" class="form-control" placeholder="Nombre o cédula" value="{{ request('buscar') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
                                Buscar
                            </button>
                            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">
                                Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Listado de Pagos</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Socio</th>
                            <th>Préstamo #</th>
                            <th>Cuota #</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Cajero</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->fecha_pago->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('socios.show', $pago->cuota->prestamo->socio_id) }}">
                                        {{ $pago->cuota->prestamo->socio->nombres }} {{ $pago->cuota->prestamo->socio->apellidos }}
                                    </a>
                                    <div class="text-muted">{{ $pago->cuota->prestamo->socio->cedula }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('prestamos.show', $pago->cuota->prestamo_id) }}">
                                        #{{ $pago->cuota->prestamo_id }}
                                    </a>
                                </td>
                                <td>{{ $pago->cuota->numero_cuota }} / {{ $pago->cuota->prestamo->plazo }}</td>
                                <td>
                                    <strong class="text-success">${{ number_format($pago->monto, 2) }}</strong>
                                </td>
                                <td>
                                    @if($pago->metodo_pago === 'EFECTIVO')
                                        <span class="badge bg-success">Efectivo</span>
                                    @elseif($pago->metodo_pago === 'TRANSFERENCIA')
                                        <span class="badge bg-info">Transferencia</span>
                                    @elseif($pago->metodo_pago === 'CHEQUE')
                                        <span class="badge bg-warning">Cheque</span>
                                    @else
                                        <span class="badge bg-primary">Tarjeta</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $pago->usuario->nombre }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('pagos.show', $pago->id) }}" class="btn btn-sm btn-secondary">
                                            Ver
                                        </a>
                                        @if($pago->fecha_pago->isToday())
                                            @can('registrar_pagos')
                                                <form action="{{ route('pagos.anular', $pago->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de anular este pago?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Anular</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No se encontraron pagos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pagos->hasPages())
                <div class="card-footer">
                    {{ $pagos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
