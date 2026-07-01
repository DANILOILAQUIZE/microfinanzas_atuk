<?php

namespace App\Http\Controllers;

use App\Models\MovimientoAhorro;
use App\Models\CuentaAhorro;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MovimientoAhorroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MovimientoAhorro::with(['cuenta.socio', 'usuario']);

        // Filtros
        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('cuenta_id')) {
            $query->where('cuenta_id', $request->cuenta_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('referencia', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%")
                  ->orWhereHas('cuenta', function($q2) use ($buscar) {
                      $q2->where('numero_cuenta', 'like', "%{$buscar}%")
                         ->orWhereHas('socio', function($q3) use ($buscar) {
                             $q3->where('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%");
                         });
                  });
            });
        }

        $movimientos = $query->latest('fecha_movimiento')->paginate(15);

        // Obtener cuentas activas para filtro
        $cuentas = CuentaAhorro::with('socio')
            ->where('estado', 'ACTIVA')
            ->get();

        return view('movimientos-ahorro.index', compact('movimientos', 'cuentas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function crear($cuentaId = null)
    {
        $cuenta = null;
        if ($cuentaId) {
            $cuenta = CuentaAhorro::with('socio')->findOrFail($cuentaId);
            
            // Validar que la cuenta esté activa
            if ($cuenta->estado !== 'ACTIVA') {
                return back()->with('error', 'No se pueden realizar movimientos en una cuenta inactiva o bloqueada.');
            }
        }

        return view('movimientos-ahorro.crear', compact('cuenta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cuenta_id' => 'required|exists:cuentas_ahorro,id',
            'tipo_movimiento' => 'required|in:DEPOSITO,RETIRO',
            'metodo_transaccion' => 'required|in:EFECTIVO,TRANSFERENCIA,CHEQUE,TARJETA',
            'monto' => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $cuenta = CuentaAhorro::findOrFail($validated['cuenta_id']);

            // Validar que la cuenta esté activa
            if ($cuenta->estado !== 'ACTIVA') {
                return back()->with('error', 'No se pueden realizar movimientos en una cuenta inactiva o bloqueada.')
                    ->withInput();
            }

            // Validaciones específicas según tipo de movimiento
            if ($validated['tipo_movimiento'] === 'RETIRO') {
                // Obtener parámetros
                $montoMaximoRetiro = Parametro::where('clave', 'monto_maximo_retiro')->first();
                $montoMaximoRetiroValor = $montoMaximoRetiro ? floatval($montoMaximoRetiro->valor) : 5000;

                $saldoMinimo = Parametro::where('clave', 'saldo_minimo_cuenta')->first();
                $saldoMinimoValor = $saldoMinimo ? floatval($saldoMinimo->valor) : 10;

                // Validar monto máximo de retiro
                if ($validated['monto'] > $montoMaximoRetiroValor) {
                    return back()->with('error', "El monto máximo de retiro es $" . number_format($montoMaximoRetiroValor, 2))
                        ->withInput();
                }

                // Validar saldo disponible
                if ($validated['monto'] > $cuenta->saldo_disponible) {
                    return back()->with('error', 'Saldo insuficiente. Saldo disponible: $' . number_format($cuenta->saldo_disponible, 2))
                        ->withInput();
                }

                // Validar saldo mínimo
                $saldoResultante = $cuenta->saldo - $validated['monto'];
                if ($saldoResultante < $saldoMinimoValor) {
                    return back()->with('error', "El retiro dejaría la cuenta con menos del saldo mínimo requerido ($" . number_format($saldoMinimoValor, 2) . ")")
                        ->withInput();
                }
            }

            // Calcular saldos
            $saldoAnterior = $cuenta->saldo;
            $saldoPosterior = ($validated['tipo_movimiento'] === 'DEPOSITO') 
                ? $saldoAnterior + $validated['monto']
                : $saldoAnterior - $validated['monto'];

            // Crear movimiento
            $movimiento = MovimientoAhorro::create([
                'cuenta_id' => $validated['cuenta_id'],
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => $validated['tipo_movimiento'],
                'metodo_transaccion' => $validated['metodo_transaccion'],
                'referencia' => $validated['referencia'],
                'monto' => $validated['monto'],
                'saldo_anterior' => $saldoAnterior,
                'saldo_posterior' => $saldoPosterior,
                'fecha_movimiento' => Carbon::now(),
                'descripcion' => $validated['descripcion'],
                'observaciones' => $validated['observaciones'],
            ]);

            // Actualizar saldo de la cuenta
            $cuenta->update([
                'saldo' => $saldoPosterior,
                'saldo_disponible' => $saldoPosterior - $cuenta->saldo_bloqueado,
            ]);

            DB::commit();

            return redirect()->route('movimientos-ahorro.show', $movimiento)
                ->with('success', 'Movimiento registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el movimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MovimientoAhorro $movimientoAhorro)
    {
        $movimiento = $movimientoAhorro->load(['cuenta.socio', 'usuario']);
        return view('movimientos-ahorro.show', compact('movimiento'));
    }

    /**
     * Remove the specified resource from storage (anular movimiento).
     */
    public function anular(MovimientoAhorro $movimientoAhorro)
    {
        try {
            // Solo se pueden anular movimientos del día actual
            if (!$movimientoAhorro->fecha_movimiento->isToday()) {
                return back()->with('error', 'Solo se pueden anular movimientos del día actual.');
            }

            DB::beginTransaction();

            $cuenta = $movimientoAhorro->cuenta;

            // Revertir el saldo
            if ($movimientoAhorro->tipo_movimiento === 'DEPOSITO') {
                $nuevoSaldo = $cuenta->saldo - $movimientoAhorro->monto;
            } else {
                $nuevoSaldo = $cuenta->saldo + $movimientoAhorro->monto;
            }

            // Actualizar saldo de la cuenta
            $cuenta->update([
                'saldo' => $nuevoSaldo,
                'saldo_disponible' => $nuevoSaldo - $cuenta->saldo_bloqueado,
            ]);

            // Eliminar el movimiento
            $movimientoAhorro->delete();

            DB::commit();

            return redirect()->route('cuentas-ahorro.show', $cuenta)
                ->with('success', 'Movimiento anulado exitosamente. Saldo actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular el movimiento: ' . $e->getMessage());
        }
    }

    /**
     * Obtener cuentas activas (para AJAX).
     */
    public function getCuentasActivas()
    {
        $cuentas = CuentaAhorro::with('socio')
            ->where('estado', 'ACTIVA')
            ->select('id', 'numero_cuenta', 'socio_id', 'saldo', 'saldo_disponible')
            ->get();

        return response()->json($cuentas);
    }
}
