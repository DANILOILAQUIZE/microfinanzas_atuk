<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Cuota;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pago::with(['cuota.prestamo.socio', 'usuario']);

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);
        }

        // Filtro por método de pago
        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        // Búsqueda por socio
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('cuota.prestamo.socio', function($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('cedula', 'like', "%{$buscar}%");
            });
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->paginate(15);

        return view('pagos.index', compact('pagos'));
    }

    /**
     * Vista para registrar un pago desde el detalle del préstamo.
     */
    public function registrarPago($prestamoId)
    {
        $prestamo = Prestamo::with(['socio', 'cuotas' => function($query) {
            $query->where('estado', '!=', 'PAGADA')->orderBy('numero_cuota');
        }])->findOrFail($prestamoId);

        // Solo mostrar cuotas pendientes o vencidas
        return view('pagos.registrar', compact('prestamo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cuota_id' => 'required|exists:cuotas,id',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:EFECTIVO,TRANSFERENCIA,CHEQUE,TARJETA',
            'fecha_pago' => 'required|date',
        ], [
            'cuota_id.required' => 'Debe seleccionar una cuota',
            'monto.required' => 'El monto es obligatorio',
            'monto.min' => 'El monto debe ser mayor a 0',
            'metodo_pago.required' => 'El método de pago es obligatorio',
            'fecha_pago.required' => 'La fecha de pago es obligatoria',
        ]);

        $cuota = Cuota::with('prestamo')->findOrFail($request->cuota_id);

        // Validar que la cuota no esté ya pagada
        if ($cuota->estado === 'PAGADA') {
            return back()->with('error', 'Esta cuota ya ha sido pagada');
        }

        // Validar el monto (debe ser el monto de la cuota + mora si aplica)
        $montoEsperado = $cuota->monto + $cuota->mora;
        $diferencia = abs($request->monto - $montoEsperado);
        
        // Permitir una diferencia de hasta 0.02 por redondeo
        if ($diferencia > 0.02) {
            $errorMsg = 'El monto debe ser exactamente $' . number_format($montoEsperado, 2);
            if ($cuota->mora > 0) {
                $errorMsg .= ' (Cuota: $' . number_format($cuota->monto, 2) . ' + Mora: $' . number_format($cuota->mora, 2) . ')';
            }
            $errorMsg .= '. Recibido: $' . number_format($request->monto, 2);
            return back()->withErrors([
                'monto' => $errorMsg
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Registrar el pago
            $pago = Pago::create([
                'cuota_id' => $request->cuota_id,
                'usuario_id' => auth()->id(),
                'fecha_pago' => $request->fecha_pago,
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
            ]);

            // Actualizar el estado de la cuota
            $cuota->update([
                'estado' => 'PAGADA',
                'fecha_pago' => $request->fecha_pago,
                'mora' => 0, // Resetear mora después de pagar
            ]);

            // Actualizar el saldo del préstamo (restar solo el capital de la cuota, no la mora ni intereses)
            $prestamo = $cuota->prestamo;
            $nuevoSaldo = $prestamo->saldo - $cuota->capital;
            
            // Redondear el saldo para evitar problemas de decimales
            $nuevoSaldo = round($nuevoSaldo, 2);
            
            $prestamo->update(['saldo' => $nuevoSaldo]);

            // Si el saldo llegó a 0 o es negativo, marcar el préstamo como CANCELADO
            if ($nuevoSaldo <= 0.01) { // Tolerancia de 1 centavo
                $prestamo->update([
                    'estado' => 'CANCELADO',
                    'saldo' => 0 // Forzar a 0 exacto
                ]);
            } else {
                // Si aún hay saldo pero no hay cuotas vencidas, cambiar a ACTIVO
                $cuotasVencidas = $prestamo->cuotas()
                    ->where('estado', 'VENCIDA')
                    ->where('id', '!=', $cuota->id) // Excluir la cuota que acabamos de pagar
                    ->count();
                
                if ($cuotasVencidas == 0 && $prestamo->estado == 'VENCIDO') {
                    $prestamo->update(['estado' => 'ACTIVO']);
                }
            }

            DB::commit();

            return redirect()->route('prestamos.show', $prestamo->id)
                ->with('success', 'Pago registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pago $pago)
    {
        $pago->load(['cuota.prestamo.socio', 'usuario']);
        return view('pagos.show', compact('pago'));
    }

    /**
     * Anular un pago (solo si es reciente).
     */
    public function anular(Pago $pago)
    {
        // Solo permitir anular pagos del mismo día
        if ($pago->fecha_pago->isToday() === false) {
            return back()->with('error', 'Solo se pueden anular pagos del día actual');
        }

        DB::beginTransaction();
        try {
            $cuota = $pago->cuota;
            $prestamo = $cuota->prestamo;

            // Revertir el estado de la cuota
            $cuota->update([
                'estado' => 'PENDIENTE',
                'fecha_pago' => null,
            ]);

            // Revertir el saldo del préstamo
            $nuevoSaldo = $prestamo->saldo + $pago->monto;
            $prestamo->update([
                'saldo' => $nuevoSaldo,
                'estado' => 'ACTIVO', // Volver a estado activo si estaba cancelado
            ]);

            // Eliminar el registro de pago
            $pago->delete();

            DB::commit();

            return redirect()->route('prestamos.show', $prestamo->id)
                ->with('success', 'Pago anulado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular el pago: ' . $e->getMessage());
        }
    }
}
