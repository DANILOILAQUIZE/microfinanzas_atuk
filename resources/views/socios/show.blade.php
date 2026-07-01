@extends('layouts.app')

@section('title', 'Detalle del Socio')

@section('header')
    <h2 class="page-title">{{ $socio->nombre }} {{ $socio->apellido }}</h2>
    <div class="text-muted">Cédula: {{ $socio->cedula }}</div>
@endsection

@section('actions')
    @if(hasPermission('editar_socios'))
    <a href="{{ route('socios.edit', $socio) }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
        </svg>
        Editar
    </a>
    @endif
    <a href="{{ route('socios.index') }}" class="btn">Volver</a>
@endsection

@section('content')

<div class="row row-cards">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información Personal</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">Cédula:</dt>
                    <dd class="col-7">{{ $socio->cedula }}</dd>
                    
                    <dt class="col-5">Nombre completo:</dt>
                    <dd class="col-7">{{ $socio->nombre }} {{ $socio->apellido }}</dd>
                    
                    <dt class="col-5">Fecha de Nacimiento:</dt>
                    <dd class="col-7">{{ $socio->fecha_nacimiento?->format('d/m/Y') ?? '-' }}</dd>
                    
                    <dt class="col-5">Edad:</dt>
                    <dd class="col-7">{{ $socio->fecha_nacimiento?->age ?? '-' }} años</dd>
                    
                    <dt class="col-5">Género:</dt>
                    <dd class="col-7">
                        @if($socio->genero == 'M') Masculino
                        @elseif($socio->genero == 'F') Femenino
                        @else {{ $socio->genero }}
                        @endif
                    </dd>
                    
                    <dt class="col-5">Ocupación:</dt>
                    <dd class="col-7">{{ $socio->ocupacion ?? '-' }}</dd>
                    
                    <dt class="col-5">Estado:</dt>
                    <dd class="col-7">
                        @if($socio->estado == 'ACTIVO')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Información de Contacto</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">Teléfono:</dt>
                    <dd class="col-7">{{ $socio->telefono }}</dd>
                    
                    <dt class="col-5">Email:</dt>
                    <dd class="col-7">{{ $socio->email ?? '-' }}</dd>
                    
                    <dt class="col-5">Dirección:</dt>
                    <dd class="col-7">{{ $socio->direccion }}</dd>
                    
                    <dt class="col-5">Ciudad:</dt>
                    <dd class="col-7">{{ $socio->ciudad ?? '-' }}</dd>
                    
                    <dt class="col-5">Fecha Registro:</dt>
                    <dd class="col-7">{{ $socio->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Cuentas de Ahorro --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cuentas de Ahorro</h3>
            </div>
            @if($socio->cuentasAhorro->count() > 0)
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Número Cuenta</th>
                            <th>Tipo</th>
                            <th>Saldo</th>
                            <th>Fecha Apertura</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($socio->cuentasAhorro as $cuenta)
                        <tr>
                            <td class="fw-bold">{{ $cuenta->numero_cuenta }}</td>
                            <td>{{ $cuenta->tipo_cuenta }}</td>
                            <td class="text-success fw-bold">${{ number_format($cuenta->saldo, 2) }}</td>
                            <td>{{ $cuenta->fecha_apertura?->format('d/m/Y') }}</td>
                            <td>
                                @if($cuenta->estado == 'ACTIVA')
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                    </svg>
                    <p>Este socio no tiene cuentas de ahorro registradas</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Préstamos --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Préstamos</h3>
            </div>
            @if($socio->prestamos->count() > 0)
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Monto</th>
                            <th>Saldo</th>
                            <th>Plazo</th>
                            <th>Fecha Desembolso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($socio->prestamos as $prestamo)
                        <tr>
                            <td class="fw-bold">${{ number_format($prestamo->monto, 2) }}</td>
                            <td>${{ number_format($prestamo->saldo, 2) }}</td>
                            <td>{{ $prestamo->plazo }} meses</td>
                            <td>{{ $prestamo->fecha_desembolso?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if($prestamo->estado == 'PENDIENTE')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($prestamo->estado == 'APROBADO')
                                    <span class="badge bg-info">Aprobado</span>
                                @elseif($prestamo->estado == 'ACTIVO')
                                    <span class="badge bg-success">Activo</span>
                                @elseif($prestamo->estado == 'FINALIZADO')
                                    <span class="badge bg-secondary">Finalizado</span>
                                @elseif($prestamo->estado == 'MORA')
                                    <span class="badge bg-danger">En Mora</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"/>
                        <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"/>
                    </svg>
                    <p>Este socio no tiene préstamos registrados</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
