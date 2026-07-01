@extends('layouts.app')

@section('title', 'Nuevo Rol')

@section('header')
    <h2 class="page-title">Nuevo Rol</h2>
    <div class="text-muted">Crea un nuevo rol y asigna permisos</div>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Rol</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Nombre del Rol</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}" placeholder="Ej: Gerente" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                       value="{{ old('descripcion') }}" placeholder="Descripción del rol">
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Permisos</h3>
                    <div class="card-actions">
                        <a href="#" onclick="seleccionarTodos(); return false;" class="btn btn-sm">Seleccionar todos</a>
                        <a href="#" onclick="deseleccionarTodos(); return false;" class="btn btn-sm">Deseleccionar todos</a>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permisos as $modulo => $permisosModulo)
                    <div class="mb-4">
                        <h4 class="subheader mb-2">{{ $modulo }}</h4>
                        <div class="row">
                            @foreach($permisosModulo as $permiso)
                            <div class="col-md-4 col-lg-3">
                                <label class="form-check">
                                    <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}" 
                                           class="form-check-input permiso-checkbox"
                                           {{ in_array($permiso->id, old('permisos', [])) ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ $permiso->nombre }}</span>
                                </label>
                                <div class="text-muted small">{{ $permiso->descripcion }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-link">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                            <path d="M14 4l0 4l-6 0l0 -4"/>
                        </svg>
                        Guardar Rol
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function seleccionarTodos() {
    document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deseleccionarTodos() {
    document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endpush
