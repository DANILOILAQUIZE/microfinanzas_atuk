@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<style>
    /* Card del login */
    .login-card {
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border-radius: 16px;
        overflow: hidden;
        background: white;
    }

    .login-card .card-body {
        padding: 3rem 2.5rem;
    }

    /* Logo pequeño y elegante */
    .logo-login {
        max-width: 200px;
        height: auto;
        margin: 0 auto 1.5rem;
        display: block;
    }

    /* Título */
    .login-title {
        color: #1a1a1a;
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 1.75rem;
    }

    .login-subtitle {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 1.75rem;
    }

    /* Labels */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    /* Inputs limpios */
    .form-control {
        border-radius: 10px;
        padding: 0.875rem 1rem;
        border: 1.5px solid #dee2e6;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #2E5AAC;
        box-shadow: 0 0 0 3px rgba(46, 90, 172, 0.1);
    }

    /* Input group */
    .input-group-text {
        border-radius: 0 10px 10px 0;
        border: 1.5px solid #dee2e6;
        border-left: none;
        background: white;
    }

    .input-group .form-control {
        border-radius: 10px 0 0 10px;
    }

    /* Checkbox */
    .form-check-input:checked {
        background-color: #2E5AAC;
        border-color: #2E5AAC;
    }

    /* Botón moderno */
    .btn-login {
        background: #2E5AAC;
        border: none;
        padding: 0.875rem;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 10px;
        transition: all 0.2s ease;
        color: white;
    }

    .btn-login:hover {
        background: #245092;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46, 90, 172, 0.3);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* Footer minimalista */
    .login-footer {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.9);
        font-size: 0.875rem;
    }

    /* Alerta limpia */
    .alert {
        border-radius: 10px;
        border: none;
    }
</style>

<div class="card login-card">
    <div class="card-body">
        <!-- Logo pequeño -->
        <img src="{{ asset('images/logo-atuk.jpeg') }}" alt="ATUK" class="logo-login">

        <!-- Título y subtítulo -->
        <h2 class="login-title text-center">Bienvenido</h2>
        <p class="login-subtitle text-center">Ingresa tus credenciales para continuar</p>

        <form method="POST" action="{{ route('login') }}" autocomplete="off">
            @csrf

            @if($errors->any())
            <div class="alert alert-danger mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v4"/><path d="M12 16h.01"/>
                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="correo@ejemplo.com" autocomplete="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password-input"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••" autocomplete="current-password" required>
                    <span class="input-group-text">
                        <a href="#" class="text-muted" onclick="togglePassword(); return false;" title="Mostrar contraseña">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                        </a>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input">
                    <span class="form-check-label">Mantener sesión iniciada</span>
                </label>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Iniciar Sesión
            </button>
        </form>
    </div>
</div>

<div class="text-center login-footer">
    <div>Sistema de Gestión de Microfinanzas</div>
    <div class="small mt-1 opacity-75">&copy; {{ date('Y') }} ATUK - Institución Financiera</div>
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
