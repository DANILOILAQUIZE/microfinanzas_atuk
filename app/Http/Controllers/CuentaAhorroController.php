<?php

namespace App\Http\Controllers;

use App\Models\CuentaAhorro;
use App\Models\Socio;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CuentaAhorroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CuentaAhorro::with('socio');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('numero_cuenta', 'like', "%{$buscar}%")
                  ->orWhereHas('socio', function($q2) use ($buscar) {
                      $q2->where('nombres', 'like', "%{$buscar}%")
                         ->orWhere('apellidos', 'like', "%{$buscar}%")
                         ->orWhere('cedula', 'like', "%{$buscar}%");
                  });
            });
        }

        $cuentas = $query->latest()->paginate(10);

        return view('cuentas-ahorro.index', compact('cuentas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obtener monto mínimo de ahorro de parámetros
        $montoMinimo = Parametro::where('clave', 'monto_minimo_ahorro')->first();
        $montoMinimoValor = $montoMinimo ? floatval($montoMinimo->valor) : 50;

        $validated = $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'deposito_inicial' => "required|numeric|min:{$montoMinimoValor}",
            'fecha_apertura' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'deposito_inicial.min' => "El depósito inicial debe ser al menos $" . number_format($montoMinimoValor, 2),
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el socio no tenga ya una cuenta activa
            $cuentaExistente = CuentaAhorro::where('socio_id', $validated['socio_id'])
                ->whereIn('estado', ['ACTIVA', 'BLOQUEADA'])
                ->first();

            if ($cuentaExistente) {
                return back()->with('error', 'El socio ya tiene una cuenta de ahorro activa.');
            }

            // Generar número de cuenta único (formato: CA-YYYYMMDD-0001)
            $fecha = Carbon::parse($validated['fecha_apertura']);
            $prefijo = 'CA-' . $fecha->format('Ymd');
            
            $ultimaCuenta = CuentaAhorro::where('numero_cuenta', 'like', "{$prefijo}%")
                ->orderBy('numero_cuenta', 'desc')
                ->first();

            if ($ultimaCuenta) {
                $ultimoNumero = intval(substr($ultimaCuenta->numero_cuenta, -4));
                $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nuevoNumero = '0001';
            }

            $numeroCuenta = $prefijo . '-' . $nuevoNumero;

            // Crear cuenta de ahorro
            $cuenta = CuentaAhorro::create([
                'socio_id' => $validated['socio_id'],
                'numero_cuenta' => $numeroCuenta,
                'fecha_apertura' => $validated['fecha_apertura'],
                'deposito_inicial' => $validated['deposito_inicial'],
                'saldo' => $validated['deposito_inicial'],
                'saldo_disponible' => $validated['deposito_inicial'],
                'saldo_bloqueado' => 0,
                'estado' => 'ACTIVA',
                'observaciones' => $validated['observaciones'],
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cuenta de ahorro creada exitosamente.',
                    'cuenta' => $cuenta->load('socio')
                ]);
            }

            return redirect()->route('cuentas-ahorro.index')
                ->with('success', 'Cuenta de ahorro creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la cuenta de ahorro: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al crear la cuenta de ahorro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CuentaAhorro $cuentaAhorro)
    {
        $cuenta = $cuentaAhorro->load(['socio', 'movimientosAhorro' => function($query) {
            $query->latest()->limit(10);
        }]);

        // Calcular estadísticas
        $totalDepositos = $cuenta->movimientosAhorro()
            ->where('tipo_movimiento', 'DEPOSITO')
            ->sum('monto');

        $totalRetiros = $cuenta->movimientosAhorro()
            ->where('tipo_movimiento', 'RETIRO')
            ->sum('monto');

        $cantidadMovimientos = $cuenta->movimientosAhorro()->count();

        return view('cuentas-ahorro.show', compact('cuenta', 'totalDepositos', 'totalRetiros', 'cantidadMovimientos'));
    }

    /**
     * Show the form for editing the specified resource (AJAX).
     */
    public function edit($id)
    {
        $cuenta = CuentaAhorro::with('socio')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json($cuenta);
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuentaAhorro $cuentaAhorro)
    {
        $validated = $request->validate([
            'estado' => 'required|in:ACTIVA,INACTIVA,BLOQUEADA',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            // No se permite cambiar estado si hay saldo
            if ($validated['estado'] === 'INACTIVA' && $cuentaAhorro->saldo > 0) {
                return back()->with('error', 'No se puede inactivar una cuenta con saldo pendiente.');
            }

            $cuentaAhorro->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cuenta actualizada exitosamente.',
                    'cuenta' => $cuentaAhorro->load('socio')
                ]);
            }

            return redirect()->route('cuentas-ahorro.index')
                ->with('success', 'Cuenta actualizada exitosamente.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la cuenta: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al actualizar la cuenta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuentaAhorro $cuentaAhorro)
    {
        try {
            // Validar que no tenga saldo
            if ($cuentaAhorro->saldo > 0) {
                return back()->with('error', 'No se puede eliminar una cuenta con saldo.');
            }

            // Validar que no tenga movimientos
            if ($cuentaAhorro->movimientosAhorro()->count() > 0) {
                return back()->with('error', 'No se puede eliminar una cuenta con movimientos registrados.');
            }

            $cuentaAhorro->delete();

            return redirect()->route('cuentas-ahorro.index')
                ->with('success', 'Cuenta de ahorro eliminada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la cuenta: ' . $e->getMessage());
        }
    }

    /**
     * Obtener socios sin cuenta activa (para AJAX).
     */
    public function getSociosSinCuenta()
    {
        $socios = Socio::whereDoesntHave('cuentasAhorro', function($query) {
            $query->whereIn('estado', ['ACTIVA', 'BLOQUEADA']);
        })->where('estado', 'ACTIVO')
          ->select('id', 'nombres', 'apellidos', 'cedula')
          ->get();

        return response()->json($socios);
    }
}
