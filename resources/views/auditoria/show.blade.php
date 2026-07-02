@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('header')
    <h2 class="page-title">Detalle de Auditoría</h2>
    <div class="text-muted">Registro #{{ $auditoria->id }} - {{ $auditoria->created_at->format('d/m/Y h:i:s A') }}</div>
@endsection

@section('actions')
    <a href="{{ route('auditoria.index') }}" class="btn btn-outline-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
        </svg>
        Volver al listado
    </a>
@endsection

@section('content')

@php
    // Función para formatear valores
    function formatearValor($valor) {
        if ($valor === null) {
            return '-';
        }
        
        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }
        
        // Si es una fecha ISO, formatearla
        if (is_string($valor) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $valor)) {
            try {
                return \Carbon\Carbon::parse($valor)->format('d/m/Y h:i A');
            } catch (\Exception $e) {
                return $valor;
            }
        }
        
        // Si es un número decimal, formatearlo
        if (is_numeric($valor) && floor($valor) != $valor) {
            return '$' . number_format($valor, 2);
        }
        
        if (is_array($valor)) {
            return json_encode($valor);
        }
        
        return $valor;
    }
    
    // Campos técnicos que no queremos mostrar
    $camposOcultos = ['id', 'created_at', 'updated_at', 'deleted_at'];
    
    // Filtrar campos ocultos
    $valoresAntiguosFiltrados = $valoresAntiguos ? array_diff_key($valoresAntiguos, array_flip($camposOcultos)) : [];
    $valoresNuevosFiltrados = $valoresNuevos ? array_diff_key($valoresNuevos, array_flip($camposOcultos)) : [];
@endphp

<div class="row">
    <div class="col-md-4">
        {{-- Información General --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Usuario</label>
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
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Acción</label>
                    <div>
                        @if($auditoria->accion == 'CREAR')
                            <span class="badge bg-success badge-lg">Crear</span>
                        @elseif($auditoria->accion == 'ACTUALIZAR')
                            <span class="badge bg-warning badge-lg">Actualizar</span>
                        @elseif($auditoria->accion == 'ELIMINAR')
                            <span class="badge bg-danger badge-lg">Eliminar</span>
                        @else
                            <span class="badge bg-secondary badge-lg">{{ $auditoria->accion }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Módulo</label>
                    <div>
                        <span class="badge bg-blue-lt badge-lg">
                            {{ ucfirst(str_replace('_', ' ', $auditoria->tabla)) }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Registro ID</label>
                    <div class="fw-bold">#{{ $auditoria->registro_id ?? 'N/A' }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Fecha y Hora</label>
                    <div class="fw-bold">{{ $auditoria->created_at->format('d/m/Y h:i:s A') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Dirección IP</label>
                    <div class="fw-bold">{{ $auditoria->ip_address ?? 'N/A' }}</div>
                </div>

                <div class="mb-0">
                    <label class="form-label text-muted">Navegador</label>
                    <div class="text-muted small" style="word-break: break-word;">
                        {{ $auditoria->user_agent ? Str::limit($auditoria->user_agent, 100) : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Cambios Realizados --}}
        @if($auditoria->accion == 'CREAR')
            <div class="card">
                <div class="card-header bg-success-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Datos Creados
                    </h3>
                </div>
                <div class="card-body">
                    @if($valoresNuevosFiltrados && count($valoresNuevosFiltrados) > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Campo</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($valoresNuevosFiltrados as $campo => $valor)
                                    <tr>
                                        <td class="text-muted">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-green-lt">
                                                {{ formatearValor($valor) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <p class="empty-title">No hay datos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($auditoria->accion == 'ACTUALIZAR')
            <div class="card">
                <div class="card-header bg-warning-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Cambios Realizados
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        // Solo mostrar campos que realmente cambiaron
                        $camposCambiados = [];
                        foreach($valoresNuevosFiltrados as $campo => $valorNuevo) {
                            $valorAntiguo = $valoresAntiguosFiltrados[$campo] ?? null;
                            if ($valorNuevo != $valorAntiguo) {
                                $camposCambiados[$campo] = [
                                    'antiguo' => $valorAntiguo,
                                    'nuevo' => $valorNuevo
                                ];
                            }
                        }
                    @endphp
                    
                    @if(count($camposCambiados) > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Campo</th>
                                        <th style="width: 30%;">Valor Anterior</th>
                                        <th style="width: 10%;"></th>
                                        <th style="width: 30%;">Valor Nuevo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($camposCambiados as $campo => $valores)
                                    <tr>
                                        <td class="text-muted">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-red-lt">
                                                {{ formatearValor($valores['antiguo']) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l14 0"/>
                                                <path d="M15 16l4 -4"/>
                                                <path d="M15 8l4 4"/>
                                            </svg>
                                        </td>
                                        <td>
                                            <span class="badge bg-green-lt">
                                                {{ formatearValor($valores['nuevo']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <line x1="9" y1="10" x2="9.01" y2="10"/>
                                    <line x1="15" y1="10" x2="15.01" y2="10"/>
                                    <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"/>
                                </svg>
                            </div>
                            <p class="empty-title">No se detectaron cambios</p>
                            <p class="empty-subtitle text-muted">Puede que solo se hayan actualizado campos técnicos</p>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($auditoria->accion == 'ELIMINAR')
            <div class="card">
                <div class="card-header bg-danger-lt">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="4" y1="7" x2="20" y2="7"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                        </svg>
                        Datos Eliminados
                    </h3>
                </div>
                <div class="card-body">
                    @if($valoresAntiguosFiltrados && count($valoresAntiguosFiltrados) > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Campo</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($valoresAntiguosFiltrados as $campo => $valor)
                                    <tr>
                                        <td class="text-muted">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $campo)) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-red-lt text-decoration-line-through">
                                                {{ formatearValor($valor) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <p class="empty-title">No hay datos disponibles</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
