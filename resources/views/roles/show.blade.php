@extends('layouts.app')

@section('title', 'Detalle del Rol')

@section('header')
    <h2 class="page-title">{{ $rol->nombre }}</h2>
    <div class="text-muted">{{ $rol->descripcion }}</div>
@endsection

@section('actions')
    <a href="{{ route('roles.edit', $rol) }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
        </svg>
        Editar
    </a>
    <a href="{{ route('roles.index') }}" class="btn">Volver</a>
@endsection

@section('content')

<div class="row row-cards">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">Nombre:</dt>
                    <dd class="col-7">{{ $rol->nombre }}</dd>
                    
                    <dt class="col-5">Descripción:</dt>
                    <dd class="col-7">{{ $rol->descripcion ?? '-' }}</dd>
                    
                    <dt class="col-5">Estado:</dt>
                    <dd class="col-7">
                        @if($rol->estado == 'ACTIVO')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </dd>
                    
                    <dt class="col-5">Usuarios:</dt>
                    <dd class="col-7">
                        <span class="badge bg-blue-lt">{{ $rol->usuarios->count() }}</span>
                    </dd>
                    
                    <dt class="col-5">Permisos:</dt>
                    <dd class="col-7">
                        <span class="badge bg-green-lt">{{ $rol->permisos->count() }}</span>
                    </dd>
                </dl>
            </div>
        </div>

        @if($rol->usuarios->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Usuarios con este Rol</h3>
            </div>
            <div class="list-group list-group-flush">
                @foreach($rol->usuarios as $usuario)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-sm">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</span>
                        </div>
                        <div class="col text-truncate">
                            <div class="text-reset d-block">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                            <div class="text-muted text-truncate mt-n1">{{ $usuario->email }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permisos Asignados</h3>
            </div>
            <div class="card-body">
                @if($rol->permisos->count() > 0)
                    @php
                        $permisosPorModulo = $rol->permisos->groupBy('modulo');
                    @endphp
                    @foreach($permisosPorModulo as $modulo => $permisos)
                    <div class="mb-4">
                        <h4 class="subheader mb-2">{{ $modulo }}</h4>
                        <div class="row">
                            @foreach($permisos as $permiso)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    <div>
                                        <div class="fw-bold">{{ $permiso->nombre }}</div>
                                        <div class="text-muted small">{{ $permiso->descripcion }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        Este rol no tiene permisos asignados
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
