@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('header')
    <h2 class="page-title">Auditoría del Sistema</h2>
    <div class="text-muted">Historial completo de operaciones y cambios en el sistema</div>
@endsection

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <form method="GET" action="{{ route('auditoria.index') }}" class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <select name="usuario_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre }} {{ $usuario->apellido }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Acción</label>
                        <select name="accion" class="form-select">
                            <option value="">Todas</option>
                            <option value="CREAR" {{ request('accion') == 'CREAR' ? 'selected' : '' }}>Crear</option>
                            <option value="ACTUALIZAR" {{ request('accion') == 'ACTUALIZAR' ? 'selected' : '' }}>Actualizar</option>
                            <option value="ELIMINAR" {{ request('accion') == 'ELIMINAR' ? 'selected' : '' }}>Eliminar</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Módulo</label>
                        <select name="tabla" class="form-select">
                            <option value="">Todos</option>
                            @foreach($tablas as $tabla)
                            <option value="{{ $tabla }}" {{ request('tabla') == $tabla ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $tabla)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de auditoría --}}
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Registro</th>
                            <th>IP</th>
                            <th class="w-1">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditorias as $auditoria)
                        <tr>
                            <td class="text-muted">
                                <div>{{ $auditoria->created_at->format('d/m/Y') }}</div>
                                <div class="small">{{ $auditoria->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2 bg-blue-lt">
                                        {{ $auditoria->usuario ? strtoupper(substr($auditoria->usuario->nombre, 0, 1)) : '?' }}
                                    </span>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $auditoria->usuario ? $auditoria->usuario->nombre . ' ' . $auditoria->usuario->apellido : 'Sistema' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $auditoria->usuario->rol->nombre ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($auditoria->accion == 'CREAR')
                                    <span class="badge bg-success">Crear</span>
                                @elseif($auditoria->accion == 'ACTUALIZAR')
                                    <span class="badge bg-warning">Actualizar</span>
                                @elseif($auditoria->accion == 'ELIMINAR')
                                    <span class="badge bg-danger">Eliminar</span>
                                @else
                                    <span class="badge bg-secondary">{{ $auditoria->accion }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-blue-lt">
                                    {{ ucfirst(str_replace('_', ' ', $auditoria->tabla)) }}
                                </span>
                            </td>
                            <td class="text-muted">
                                #{{ $auditoria->registro_id ?? 'N/A' }}
                            </td>
                            <td class="text-muted small">
                                {{ $auditoria->ip_address ?? 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ route('auditoria.show', $auditoria) }}" class="btn btn-sm btn-primary">
                                    Ver
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay registros de auditoría
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($auditorias->hasPages())
            <div class="card-footer">
                {{ $auditorias->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
