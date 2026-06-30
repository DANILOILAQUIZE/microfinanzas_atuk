<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acceso') — Caja de Ahorro ATUK</title>

    @vite(['node_modules/@tabler/core/dist/css/tabler.min.css', 'resources/css/app.css'])
</head>
<body class="antialiased d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">

            {{-- Logo --}}
            <div class="text-center mb-4">
                <a href="/" class="navbar-brand navbar-brand-autodark">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" class="text-primary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 21l18 0"/>
                            <path d="M3 10l18 0"/>
                            <path d="M5 6l7 -3l7 3"/>
                            <path d="M4 10l0 11"/>
                            <path d="M20 10l0 11"/>
                            <path d="M8 14l0 3"/>
                            <path d="M12 14l0 3"/>
                            <path d="M16 14l0 3"/>
                        </svg>
                        <span class="h2 mb-0 fw-bold text-primary">ATUK</span>
                    </div>
                    <div class="text-muted mt-1">Caja de Ahorro</div>
                </a>
            </div>

            @yield('content')

        </div>
    </div>

    @vite(['node_modules/@tabler/core/dist/js/tabler.min.js'])
</body>
</html>
