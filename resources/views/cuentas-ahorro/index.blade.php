@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Cuentas de Ahorro</h2>
                <div class="text-muted mt-1">Administración de cuentas de ahorro de socios</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearCuentaModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Nueva Cuenta
                </button>
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
                <form method="GET" action="{{ route('cuentas-ahorro.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="ACTIVA" {{ request('estado') == 'ACTIVA' ? 'selected' : '' }}>Activa</option>
                            <option value="INACTIVA" {{ request('estado') == 'INACTIVA' ? 'selected' : '' }}>Inactiva</option>
                            <option value="BLOQUEADA" {{ request('estado') == 'BLOQUEADA' ? 'selected' : '' }}>Bloqueada</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control" placeholder="Número de cuenta, nombre o DUI del socio..." value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                Buscar
                            </button>
                            <a href="{{ route('cuentas-ahorro.index') }}" class="btn btn-light">Limpiar</a>
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
                            <th>Nº Cuenta</th>
                            <th>Socio</th>
                            <th>DUI</th>
                            <th>Fecha Apertura</th>
                            <th>Depósito Inicial</th>
                            <th>Saldo Actual</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuentas as $cuenta)
                        <tr>
                            <td>
                                <strong>{{ $cuenta->numero_cuenta }}</strong>
                            </td>
                            <td>
                                {{ $cuenta->socio->nombres }} {{ $cuenta->socio->apellidos }}
                            </td>
                            <td>{{ $cuenta->socio->dui }}</td>
                            <td>{{ $cuenta->fecha_apertura->format('d/m/Y') }}</td>
                            <td>${{ number_format($cuenta->deposito_inicial, 2) }}</td>
                            <td>
                                <strong class="text-primary">${{ number_format($cuenta->saldo, 2) }}</strong>
                                @if($cuenta->saldo_bloqueado > 0)
                                    <small class="text-muted d-block">Bloqueado: ${{ number_format($cuenta->saldo_bloqueado, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($cuenta->estado === 'ACTIVA')
                                    <span class="badge bg-success">Activa</span>
                                @elseif($cuenta->estado === 'BLOQUEADA')
                                    <span class="badge bg-warning">Bloqueada</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('cuentas-ahorro.show', $cuenta) }}" class="btn btn-sm btn-primary" title="Ver detalles">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info" onclick="editarCuenta({{ $cuenta->id }})" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                    @if($cuenta->saldo == 0 && $cuenta->movimientosAhorro->count() == 0)
                                    <form action="{{ route('cuentas-ahorro.destroy', $cuenta) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta cuenta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay cuentas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cuentas->hasPages())
            <div class="card-footer">
                {{ $cuentas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Crear Cuenta -->
<div class="modal fade" id="crearCuentaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCrearCuenta" method="POST" action="{{ route('cuentas-ahorro.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Cuenta de Ahorro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Nota:</strong> El depósito inicial será el saldo de apertura de la cuenta.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Socio</label>
                            <select name="socio_id" id="socio_id_crear" class="form-select" required>
                                <option value="">Seleccione un socio...</option>
                            </select>
                            <small class="form-hint">Solo socios sin cuenta activa</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Fecha de Apertura</label>
                            <input type="date" name="fecha_apertura" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Depósito Inicial</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="deposito_inicial" id="deposito_inicial" class="form-control" step="0.01" min="50" required>
                            </div>
                            <small class="form-hint text-muted">Mínimo: $50.00</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Cuenta -->
<div class="modal fade" id="editarCuentaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarCuenta" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cuenta de Ahorro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Número de Cuenta</label>
                        <input type="text" id="edit_numero_cuenta" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Socio</label>
                        <input type="text" id="edit_socio" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Estado</label>
                        <select name="estado" id="edit_estado" class="form-select" required>
                            <option value="ACTIVA">Activa</option>
                            <option value="BLOQUEADA">Bloqueada</option>
                            <option value="INACTIVA">Inactiva</option>
                        </select>
                        <small class="form-hint">No se puede inactivar una cuenta con saldo</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Cargar socios sin cuenta al abrir el modal
    document.getElementById('crearCuentaModal').addEventListener('show.bs.modal', function() {
        fetch('{{ route('cuentas-ahorro.socios-sin-cuenta') }}')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('socio_id_crear');
                select.innerHTML = '<option value="">Seleccione un socio...</option>';
                data.forEach(socio => {
                    const option = document.createElement('option');
                    option.value = socio.id;
                    option.textContent = `${socio.nombres} ${socio.apellidos} - ${socio.dui}`;
                    select.appendChild(option);
                });
            });
    });

    // Editar cuenta
    function editarCuenta(id) {
        fetch(`/cuentas-ahorro/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_numero_cuenta').value = data.numero_cuenta;
            document.getElementById('edit_socio').value = `${data.socio.nombres} ${data.socio.apellidos}`;
            document.getElementById('edit_estado').value = data.estado;
            document.getElementById('edit_observaciones').value = data.observaciones || '';
            
            document.getElementById('formEditarCuenta').action = `/cuentas-ahorro/${data.id}`;
            
            new bootstrap.Modal(document.getElementById('editarCuentaModal')).show();
        });
    }
</script>
@endpush
