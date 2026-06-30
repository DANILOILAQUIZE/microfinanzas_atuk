@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Iniciar sesión</h2>

        <form method="POST" action="{{ route('login') }}" autocomplete="off">
            @csrf

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9v4"/><path d="M12 16h.01"/>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                        </svg>
                    </div>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="correo@ejemplo.com" autocomplete="email" required>
            </div>

            <div class="mb-2">
                <label class="form-label">
                    Contraseña
                </label>
                <div class="input-group input-group-flat">
                    <input type="password" name="password" id="password-input"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Tu contraseña" autocomplete="current-password" required>
                    <span class="input-group-text">
                        <a href="#" class="link-secondary" onclick="togglePassword()" title="Mostrar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" id="eye-icon" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                        </a>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input">
                    <span class="form-check-label">Recordarme</span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                        <path d="M20 12h-13l3 -3m0 6l-3 -3"/>
                    </svg>
                    Ingresar al sistema
                </button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-muted mt-3">
    Sistema de Gestión de Microfinanzas
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password-input');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush
@endsection
