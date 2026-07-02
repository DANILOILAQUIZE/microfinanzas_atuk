<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema') — Caja de Ahorro ATUK</title>

    @vite(['node_modules/@tabler/core/dist/css/tabler.min.css', 'resources/css/app.css'])

    <style>
        /* Forzar sidebar visible en todas las resoluciones */
        .navbar-vertical.navbar-expand-lg {
            display: flex !important;
            flex-basis: auto;
        }
        
        .navbar-vertical .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        
        @media (min-width: 992px) {
            .navbar-vertical.navbar-expand-lg {
                width: 15rem;
            }
        }

        /* Color azul chevere para el sidebar */
        .navbar-vertical {
            background: linear-gradient(180deg, #0d2d5e 0%, #0a1f44 100%) !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .navbar-vertical .navbar-brand {
            color: white !important;
        }

        .navbar-vertical .nav-link {
            color: rgba(255,255,255,0.8) !important;
        }

        .navbar-vertical .nav-link:hover,
        .navbar-vertical .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.1) !important;
        }

        .navbar-vertical .nav-link-title {
            color: rgba(255,255,255,0.9) !important;
        }

        .navbar-vertical .dropdown-menu {
            background: rgba(255,255,255,0.95);
        }

        /* Separador visual antes del footer */
        .sidebar-divider {
            border-top: 2px solid rgba(255,255,255,0.2);
            margin: 1.5rem 0.75rem;
        }

        /* Footer del sidebar más espaciado */
        .sidebar-footer-wrapper {
            margin-top: auto;
            padding-top: 1rem;
        }

        /* ========== BOTONES DE ACCIÓN MEJORADOS ========== */
        .btn-action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: nowrap;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0 !important;
            border-radius: 6px;
            border: 1.5px solid;
            background: white !important;
            transition: all 0.2s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .btn-action.btn-action-view {
            border-color: #0d6efd;
            color: #0d6efd !important;
        }

        .btn-action.btn-action-view:hover {
            background: #0d6efd !important;
            color: white !important;
        }

        .btn-action.btn-action-edit {
            border-color: #ffc107;
            color: #ffc107 !important;
        }

        .btn-action.btn-action-edit:hover {
            background: #ffc107 !important;
            color: #000 !important;
        }

        .btn-action.btn-action-delete {
            border-color: #dc3545;
            color: #dc3545 !important;
        }

        .btn-action.btn-action-delete:hover {
            background: #dc3545 !important;
            color: white !important;
        }

        .btn-action.btn-action-success {
            border-color: #198754;
            color: #198754 !important;
        }

        .btn-action.btn-action-success:hover {
            background: #198754 !important;
            color: white !important;
        }

        .btn-action.btn-action-info {
            border-color: #0dcaf0;
            color: #0dcaf0 !important;
        }

        .btn-action.btn-action-info:hover {
            background: #0dcaf0 !important;
            color: white !important;
        }

        .btn-action .icon {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        /* Asegurar que los botones no hereden estilos de Tabler */
        .btn-action.btn-sm {
            width: 32px !important;
            height: 32px !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="page">

        {{-- SIDEBAR --}}
        <aside class="navbar navbar-vertical navbar-expand-lg">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('dashboard') }}">
                        ATUK
                    </a>
                </h1>

                <div class="navbar-collapse" id="sidebar-menu" style="display: block !important;">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                                </span>
                                <span class="nav-link-title">Operaciones</span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a class="dropdown-item" href="{{ route('socios.index') }}">Socios</a>
                                        <a class="dropdown-item" href="{{ route('cuentas-ahorro.index') }}">Cuentas de Ahorro</a>
                                        <a class="dropdown-item" href="{{ route('movimientos-ahorro.index') }}">Movimientos de Ahorro</a>
                                        <a class="dropdown-item" href="{{ route('prestamos.index') }}">Préstamos</a>
                                        <a class="dropdown-item" href="{{ route('pagos.index') }}">Pagos</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
                                </span>
                                <span class="nav-link-title">Inteligencia de Negocios</span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a class="dropdown-item" href="{{ route('reportes.index') }}">Reportes</a>
                                        <a class="dropdown-item" href="{{ route('alertas.index') }}">Alertas de Riesgo</a>
                                        <a class="dropdown-item" href="{{ route('notificaciones.index') }}">Notificaciones</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /></svg>
                                </span>
                                <span class="nav-link-title">Administración</span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a class="dropdown-item" href="{{ route('usuarios.index') }}">Usuarios</a>
                                        <a class="dropdown-item" href="{{ route('roles.index') }}">Roles y Permisos</a>
                                        <a class="dropdown-item" href="{{ route('tipos-prestamo.index') }}">Tipos de Préstamo</a>
                                        <a class="dropdown-item" href="{{ route('parametros.index') }}">Parámetros</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    {{-- Separador visual --}}
                    <div class="sidebar-divider"></div>

                    {{-- Footer con cerrar sesión --}}
                    <div class="sidebar-footer-wrapper">
                        <div class="card mb-0 border-0 shadow-none" style="background: rgba(255,255,255,0.1);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="avatar avatar-sm me-2" style="background: rgba(255,255,255,0.2);">
                                        {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
                                    </span>
                                    <div class="flex-fill text-white">
                                        <div class="small fw-bold">
                                            {{ auth()->user()->nombre ?? '' }} {{ auth()->user()->apellido ?? '' }}
                                        </div>
                                        <div class="text-white-50 small">
                                            {{ auth()->user()->rol->nombre ?? '' }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                                   class="btn btn-sm btn-light w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                                        <path d="M9 12h12l-3 -3"/>
                                        <path d="M18 15l3 -3"/>
                                    </svg>
                                    Cerrar sesión
                                </a>
                                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <div class="page-wrapper">

            {{-- HEADER DE PÁGINA --}}
            @hasSection('header')
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            @yield('header')
                        </div>
                        @hasSection('actions')
                        <div class="col-auto ms-auto d-print-none">
                            @yield('actions')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- CONTENIDO --}}
            <div class="page-body">
                <div class="container-xl">

                    {{-- Alertas flash --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                            </div>
                            <div>{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 9v4"/>
                                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                    <path d="M12 16h.01"/>
                                </svg>
                            </div>
                            <div>{{ session('error') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            {{-- FOOTER --}}
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Caja de Ahorro ATUK &copy; {{ date('Y') }}
                                </li>
                                <li class="list-inline-item">
                                    <span class="text-muted">Sistema de Microfinanzas</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @vite(['node_modules/@tabler/core/dist/js/tabler.min.js', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>
