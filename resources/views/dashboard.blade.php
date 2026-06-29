<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Microfinanzas ATUK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Microfinanzas ATUK</h1>
            <div class="flex items-center space-x-4">
                <span>Bienvenido, {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 p-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-800">Socios</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ App\Models\Socio::count() }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-semibold text-green-800">Préstamos Activos</h3>
                    <p class="text-3xl font-bold text-green-600">{{ App\Models\Prestamo::where('estado', 'ACTIVO')->count() }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-800">Cuentas de Ahorro</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ App\Models\CuentaAhorro::count() }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Información del Usuario</h3>
                <div class="space-y-2">
                    <p><strong>Nombre:</strong> {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Rol:</strong> {{ auth()->user()->rol->nombre ?? 'Sin rol' }}</p>
                    <p><strong>Estado:</strong> {{ auth()->user()->estado }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
