@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Movimientos de Ahorro</h2>
                <div class="text-muted mt-1">Historial de depósitos y retiros</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('movimientos-ahorro.crear') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Nuevo Movimiento
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('movimientos-ahorro.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo_movimiento" class="form-select">
                            <option value="">Todos</option>
                            <option value="DEPOSITO" {{ request('tipo_movimiento') == 'DEPOSITO' ? 'selected' : '' }}>Depósito</option>
                            <option value="RETIRO" {{ request('tipo_movimiento') == 'RETIRO' ? 'selected' : '' }}>Retiro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cuenta</label>
                        <select name="cuenta_id" class="form-select">
                            <option value="">Todas las cuentas</option>
                            @foreach($cuentas as $cuenta)
                                <option value="{{ $cuenta->id }}" {{ request('cuenta_id') == $cuenta->id ? 'selected' : '' }}>
                                    {{ $cuenta->numero_cuenta }} - {{ $cuenta->socio->nombres }} {{ $cuenta->socio->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <div class="input-group">
                            <input type="text" name="buscar" class="form-control" placeholder="Referencia, cuenta..." value="{{ request('buscar') }}">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </button>
                            <a href="{{ route('movimientos-ahorro.index') }}" class="btn btn-light">Limpiar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Cuenta</th>
                            <th>Socio</th>
                            <th>Tipo</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Saldo Anterior</th>
                            <th>Saldo Posterior</th>
                            <th>Usuario</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                        <tr>
                            <td>
                                <div>{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $movimiento->fecha_movimiento->format('H:i') }}</small>
                            </td>
                            <td>{{ $movimiento->cuenta->numero_cuenta }}</td>
                            <td>{{ $movimiento->cuenta->socio->nombres }} {{ $movimiento->cuenta->socio->apellidos }}</td>
                            <td>
                                @if($movimiento->tipo_movimiento === 'DEPOSITO')
                                    <span class="badge bg-success">Depósito</span>
                                @else
                                    <span class="badge bg-danger">Retiro</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $movimiento->metodo_transaccion }}</small>
                                @if($movimiento->referencia)
                                    <br><small class="text-muted">Ref: {{ $movimiento->referencia }}</small>
                                @endif
                            </td>
                            <td>
                                @if($movimiento->tipo_movimiento === 'DEPOSITO')
                                    <strong class="text-success">+${{ number_format($movimiento->monto, 2) }}</strong>
                                @else
                                    <strong class="text-danger">-${{ number_format($movimiento->monto, 2) }}</strong>
                                @endif
                            </td>
                            <td>${{ number_format($movimiento->saldo_anterior, 2) }}</td>
                            <td><strong>${{ number_format($movimiento->saldo_posterior, 2) }}</strong></td>
                            <td>{{ $movimiento->usuario->nombre }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('movimientos-ahorro.show', $movimiento) }}" class="btn btn-sm btn-primary" title="Ver comprobante">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>
                                    @if($movimiento->fecha_movimiento->isToday())
                                    <form action="{{ route('movimientos-ahorro.anular', $movimiento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de anular este movimiento? Se revertirá el saldo.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Anular">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No hay movimientos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movimientos->hasPages())
            <div class="card-footer">
                {{ $movimientos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
