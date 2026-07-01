@extends('layouts.app')

@section('title', 'Editar Tipo de Préstamo')

@section('header')
    <h2 class="page-title">Editar Tipo de Préstamo</h2>
    <div class="text-muted">Modifica la información del producto crediticio</div>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <form action="{{ route('tipos-prestamo.update', $tiposPrestamo) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Tipo de Préstamo</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Nombre</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   name="nombre" value="{{ old('nombre', $tiposPrestamo->nombre) }}" 
                                   placeholder="Ej: Microcrédito, Consumo, Vivienda" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Tasa de Interés Anual (%)</label>
                            <input type="number" step="0.01" class="form-control @error('interes') is-invalid @enderror" 
                                   name="interes" value="{{ old('interes', $tiposPrestamo->interes) }}" 
                                   placeholder="Ej: 12.50" required>
                            @error('interes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      name="descripcion" rows="3" 
                                      placeholder="Descripción del tipo de préstamo...">{{ old('descripcion', $tiposPrestamo->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Límites de Montos y Plazos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Mínimo ($)</label>
                            <input type="number" step="0.01" class="form-control @error('monto_minimo') is-invalid @enderror" 
                                   name="monto_minimo" value="{{ old('monto_minimo', $tiposPrestamo->monto_minimo) }}" 
                                   placeholder="Ej: 500.00" required>
                            @error('monto_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Monto Máximo ($)</label>
                            <input type="number" step="0.01" class="form-control @error('monto_maximo') is-invalid @enderror" 
                                   name="monto_maximo" value="{{ old('monto_maximo', $tiposPrestamo->monto_maximo) }}" 
                                   placeholder="Ej: 10000.00" required>
                            @error('monto_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Mínimo (meses)</label>
                            <input type="number" class="form-control @error('plazo_minimo') is-invalid @enderror" 
                                   name="plazo_minimo" value="{{ old('plazo_minimo', $tiposPrestamo->plazo_minimo) }}" 
                                   placeholder="Ej: 6" required>
                            @error('plazo_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Plazo Máximo (meses)</label>
                            <input type="number" class="form-control @error('plazo_maximo') is-invalid @enderror" 
                                   name="plazo_maximo" value="{{ old('plazo_maximo', $tiposPrestamo->plazo_maximo) }}" 
                                   placeholder="Ej: 24" required>
                            @error('plazo_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Estado</label>
                            <select class="form-select @error('estado') is-invalid @enderror" name="estado" required>
                                <option value="ACTIVO" {{ old('estado', $tiposPrestamo->estado) == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                <option value="INACTIVO" {{ old('estado', $tiposPrestamo->estado) == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requiere_garantia" 
                                       value="1" {{ old('requiere_garantia', $tiposPrestamo->requiere_garantia) ? 'checked' : '' }}>
                                <span class="form-check-label">¿Requiere garantía?</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tipos-prestamo.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Actualizar Tipo de Préstamo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
