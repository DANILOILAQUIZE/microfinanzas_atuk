<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acceso') — ATUK Institución Financiera</title>

    @vite(['node_modules/@tabler/core/dist/css/tabler.min.css', 'resources/css/app.css'])
    
    <style>
        body {
            background: linear-gradient(135deg, #2E5AAC 0%, #1a3d7a 50%, #0d2d5e 100%);
            min-height: 100vh;
        }

        .page-center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .container-tight {
            max-width: 480px;
            width: 100%;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            @yield('content')
        </div>
    </div>

    @vite(['node_modules/@tabler/core/dist/js/tabler.min.js', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>
