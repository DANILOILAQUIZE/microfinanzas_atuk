<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema') — Caja de Ahorro ATUK</title>
    @vite(['node_modules/@tabler/core/dist/css/tabler.min.css', 'resources/css/app.css'])
    @stack('styles')
</head>
<body class="antialiased">
<div class="wrapper">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <div class="d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l18 0"/><path d="M3 10l18 0"/>
                        <path d="M5 6l7 -3l7 3"/>
                        <path d="M4 10l0 11"/><path d="M20 10l0 11"/>
                        <path d="M8 14l0 3"/><path d="M12 14l0 3"/><path d="M16 14l0 3"/>
                    </svg>
                    <span class="fw-bold fs-4">ATUK</span>
                </div>
            </a>

            {{-- Avatar móvil --}}
            <div class="navbar-nav flex-row d-lg-none">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                        <span class="avatar avatar-sm">
                            {{ strtoupper(substr(auth()->user()->nombre ?? 'A', 0, 1)) }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-mobile').submit();">
                            Cerrar sesión
                        </a>
                        <form id="logout-mobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>

            {{-- Menú principal --}}
            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="navbar-nav pt-lg-3">

                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l-2 0l9 -9l9 9l-2 0"/>
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- Separador Operaciones --}}
                    <li class="nav-item mt-2">
                        <div class="nav-link-title text-uppercase fw-bold small px-3" style="color: rgba(255,255,255,.4); font-size: .625rem; letter-spacing: .08em;">
                            Operaciones
                        </div>
                    </li>

                    {{-- Socios --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('socios.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Socios</span>
                        </a>
                    </li>

                    {{-- Préstamos --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('prestamos.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 9l6 -4l6 4"/>
                                    <path d="M6 9v11h12v-11"/>
                                    <path d="M9 21v-6h6v6"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Préstamos</span>
                        </a>
                    </li>

                    {{-- Pagos --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/>
                                    <path d="M3 10l18 0"/>
                                    <path d="M7 15l.01 0"/>
                                    <path d="M11 15l2 0"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Pagos</span>
                        </a>
                    </li>

                    {{-- Cuentas Ahorro --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cuentas-ahorro.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Cuentas de Ahorro</span>
                        </a>
                    </li>

                    {{-- Separador BI --}}
                    <li class="nav-item mt-2">
                        <div class="nav-link-title text-uppercase fw-bold small px-3" style="color: rgba(255,255,255,.4); font-size: .625rem; letter-spacing: .08em;">
                            Inteligencia de Negocios
                        </div>
                    </li>

                    {{-- Reportes --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                                    <path d="M9 12h6"/><path d="M9 16h6"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Reportes</span>
                        </a>
                    </li>

                    {{-- Alertas --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"/>
                                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Alertas de Riesgo</span>
                        </a>
                    </li>

                    {{-- Separador Admin --}}
                    <li class="nav-item mt-2">
                        <div class="nav-link-title text-uppercase fw-bold small px-3" style="color: rgba(255,255,255,.4); font-size: .625rem; letter-spacing: .08em;">
                            Administración
                        </div>
                    </li>

                    {{-- Usuarios --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Usuarios</span>
                        </a>
                    </li>

                    {{-- Roles --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="#">
                            <span class="nav-link-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">Roles y Permisos</span>
                        </a>
                    </li>

                </ul>

                {{-- Usuario en footer del sidebar --}}
                <div class="mt-auto pt-3 border-top border-secondary">
                    <div class="d-flex align-items-center px-3 py-2 gap-2">
                        <span class="avatar avatar-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->nombre ?? 'A', 0, 1)) }}
                        </span>
                        <div class="flex-fill overflow-hidden">
                            <div class="text-white text-truncate small fw-semibold">
                                {{ auth()->user()->nombre ?? '' }} {{ auth()->user()->apellido ?? '' }}
                            </div>
                            <div class="text-muted small text-truncate">
                                {{ auth()->user()->rol->nombre ?? '' }}
                            </div>
                        </div>
                        <a href="#" title="Cerrar sesión" class="text-muted ms-1"
                           onclick="event.preventDefault(); document.getElementById('logout-sidebar').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                                <path d="M9 12h12l-3 -3m0 6l-3 -3"/>
                            </svg>
                        </a>
                        <form id="logout-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>

            </div>
        </div>
    </aside>

    {{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
    <div class="page-wrapper">

        {{-- Navbar superior --}}
        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l-2 0l9 -9l9 9l-2 0"/>
                                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Inicio</span>
                                </a>
                            </li>
                        </ul>
                        <div class="navbar-nav flex-row order-md-last">
                            {{-- Notificaciones --}}
                            <div class="nav-item me-2">
                                <a href="#" class="nav-link px-0" title="Notificaciones">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                                        <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                                    </svg>
                                </a>
                            </div>
                            {{-- Perfil --}}
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                    <span class="avatar avatar-sm">
                                        {{ strtoupper(substr(auth()->user()->nombre ?? 'A', 0, 1)) }}
                                    </span>
                                    <div class="d-none d-xl-block ps-2">
                                        <div>{{ auth()->user()->nombre ?? '' }} {{ auth()->user()->apellido ?? '' }}</div>
                                        <div class="mt-1 small text-secondary">{{ auth()->user()->rol->nombre ?? '' }}</div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <a href="#" class="dropdown-item">Mi perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item text-danger"
                                       onclick="event.preventDefault(); document.getElementById('logout-nav').submit();">
                                        Cerrar sesión
                                    </a>
                                    <form id="logout-nav" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Header de página --}}
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

        {{-- Cuerpo de página --}}
        <div class="page-body">
            <div class="container-xl">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible mt-3" role="alert">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                        <div>{{ session('success') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible mt-3" role="alert">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9v4"/><path d="M12 16h.01"/>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                @yield('content')

            </div>
        </div>

        {{-- Footer --}}
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center">
                    <div class="col">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <strong>Caja de Ahorro ATUK</strong> &copy; {{ date('Y') }}
                            </li>
                            <li class="list-inline-item text-muted">
                                Sistema de Microfinanzas
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
