<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Socio;
use App\Models\TipoPrestamo;
use App\Models\CuentaAhorro;
use App\Models\MovimientoAhorro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prestamo::with(['socio', 'tipoPrestamo', 'usuario', 'usuarioAprobador']);

        // Filtro por estado de aprobación
        if ($request->filled('estado_aprobacion')) {
            $query->where('estado_aprobacion', $request->estado_aprobacion);
        }

        // Filtro por estado del préstamo
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de préstamo
        if ($request->filled('tipo_prestamo_id')) {
            $query->where('tipo_prestamo_id', $request->tipo_prestamo_id);
        }

        // Búsqueda por socio
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('socio', function($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('cedula', 'like', "%{$buscar}%");
            });
        }

        $prestamos = $query->orderBy('created_at', 'desc')->paginate(15);
        $tiposPrestamo = TipoPrestamo::where('estado', 'ACTIVO')->get();
        $socios = Socio::where('estado', 'ACTIVO')->get();

        return view('prestamos.index', compact('prestamos', 'tiposPrestamo', 'socios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'tipo_prestamo_id' => 'required|exists:tipos_prestamo,id',
            'monto' => 'required|numeric|min:0',
            'plazo' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
        ], [
            'socio_id.required' => 'Debe seleccionar un socio',
            'tipo_prestamo_id.required' => 'Debe seleccionar un tipo de préstamo',
            'monto.required' => 'El monto es obligatorio',
            'monto.min' => 'El monto debe ser mayor a 0',
            'plazo.required' => 'El plazo es obligatorio',
            'plazo.min' => 'El plazo debe ser al menos 1 mes',
        ]);

        // Obtener el tipo de préstamo para validaciones y cálculos
        $tipoPrestamo = TipoPrestamo::findOrFail($request->tipo_prestamo_id);

        // Validar montos y plazos según el tipo de préstamo
        if ($request->monto < $tipoPrestamo->monto_minimo || $request->monto > $tipoPrestamo->monto_maximo) {
            return back()->withErrors([
                'monto' => "El monto debe estar entre $" . number_format($tipoPrestamo->monto_minimo, 2) . 
                           " y $" . number_format($tipoPrestamo->monto_maximo, 2)
            ])->withInput();
        }

        if ($request->plazo < $tipoPrestamo->plazo_minimo || $request->plazo > $tipoPrestamo->plazo_maximo) {
            return back()->withErrors([
                'plazo' => "El plazo debe estar entre {$tipoPrestamo->plazo_minimo} y {$tipoPrestamo->plazo_maximo} meses"
            ])->withInput();
        }

        // Calcular intereses y montos
        $interes = $tipoPrestamo->interes;
        $interesTotal = ($request->monto * ($interes / 100) * $request->plazo) / 12; // Interés simple anual
        $montoTotal = $request->monto + $interesTotal;
        $montoCuota = $montoTotal / $request->plazo;

        DB::beginTransaction();
        try {
            $prestamo = Prestamo::create([
                'socio_id' => $request->socio_id,
                'tipo_prestamo_id' => $request->tipo_prestamo_id,
                'usuario_id' => auth()->id(),
                'fecha_solicitud' => now(),
                'monto' => $request->monto,
                'monto_total' => $montoTotal,
                'monto_cuota' => $montoCuota,
                'interes' => $interes,
                'plazo' => $request->plazo,
                'saldo' => $montoTotal, // Inicialmente el saldo es el monto total
                'estado' => 'PENDIENTE',
                'estado_aprobacion' => 'PENDIENTE',
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('prestamos.index')
                ->with('success', 'Solicitud de préstamo registrada exitosamente. Pendiente de aprobación.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el préstamo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prestamo $prestamo)
    {
        $prestamo->load(['socio', 'tipoPrestamo', 'usuario', 'usuarioAprobador', 'cuotas', 'garantias']);
        return view('prestamos.show', compact('prestamo'));
    }

    /**
     * Aprobar un préstamo.
     */
    public function aprobar(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'fecha_desembolso' => 'required|date|after_or_equal:today',
            'metodo_desembolso' => 'required|in:EFECTIVO,TRANSFERENCIA,DEPOSITO_AHORRO,CHEQUE',
            'fecha_primer_pago' => 'required|date|after_or_equal:fecha_desembolso',
            'dia_vencimiento' => 'required|integer|min:1|max:28',
            'observaciones' => 'nullable|string',
        ], [
            'fecha_desembolso.required' => 'La fecha de desembolso es obligatoria',
            'fecha_desembolso.after_or_equal' => 'La fecha de desembolso debe ser hoy o posterior',
            'metodo_desembolso.required' => 'El método de desembolso es obligatorio',
            'fecha_primer_pago.required' => 'La fecha del primer pago es obligatoria',
            'fecha_primer_pago.after_or_equal' => 'El primer pago debe ser después o igual al desembolso',
            'dia_vencimiento.required' => 'El día de vencimiento es obligatorio',
            'dia_vencimiento.min' => 'El día de vencimiento debe ser entre 1 y 28',
            'dia_vencimiento.max' => 'El día de vencimiento debe ser entre 1 y 28',
        ]);

        if ($prestamo->estado_aprobacion !== 'PENDIENTE') {
            return back()->with('error', 'Este préstamo ya fue procesado');
        }

        // VALIDACIÓN OBLIGATORIA: El socio DEBE tener cuenta de ahorro activa
        // Esto es requisito para TODOS los métodos de desembolso
        $cuentaAhorro = CuentaAhorro::where('socio_id', $prestamo->socio_id)
            ->where('estado', 'ACTIVA')
            ->first();

        if (!$cuentaAhorro) {
            return back()->with('error', 'El socio no tiene una cuenta de ahorro activa. Todos los socios deben tener una cuenta de ahorro para poder acceder a préstamos. Por favor, cree una cuenta de ahorro antes de aprobar el préstamo.');
        }

        DB::beginTransaction();
        try {
            // 1. Actualizar el préstamo
            $prestamo->update([
                'estado_aprobacion' => 'APROBADO',
                'estado' => 'ACTIVO',
                'fecha_aprobacion' => now(),
                'fecha_desembolso' => $request->fecha_desembolso,
                'usuario_aprobador_id' => auth()->id(),
                'observaciones' => $request->observaciones,
            ]);

            // 2. Generar cuotas automáticamente con día de vencimiento mensual
            $this->generarCuotas($prestamo, $request->fecha_primer_pago, $request->dia_vencimiento);
            
            // 3. Registrar el desembolso SOLO SI el método es depósito a ahorro
            $mensajeDesembolso = '';
            if ($request->metodo_desembolso === 'DEPOSITO_AHORRO') {
                // Registrar el desembolso como depósito en la cuenta de ahorro
                MovimientoAhorro::create([
                    'cuenta_id' => $cuentaAhorro->id,
                    'tipo_movimiento' => 'DEPOSITO',
                    'monto' => $prestamo->monto,
                    'saldo_anterior' => $cuentaAhorro->saldo,
                    'saldo_posterior' => $cuentaAhorro->saldo + $prestamo->monto,
                    'fecha_movimiento' => $request->fecha_desembolso,
                    'descripcion' => 'Desembolso de préstamo #' . $prestamo->id,
                    'usuario_id' => auth()->id(),
                    'referencia' => 'PRESTAMO-' . $prestamo->id,
                    'metodo_transaccion' => 'TRANSFERENCIA',
                ]);

                // Actualizar el saldo de la cuenta de ahorro
                $cuentaAhorro->increment('saldo', $prestamo->monto);
                
                $mensajeDesembolso = ' Se depositó $' . number_format($prestamo->monto, 2) . ' en la cuenta de ahorro del socio.';
            } else {
                // Para otros métodos, solo registrar la aprobación
                $metodosTexto = [
                    'EFECTIVO' => 'en efectivo',
                    'TRANSFERENCIA' => 'por transferencia a cuenta externa',
                    'CHEQUE' => 'mediante cheque',
                ];
                $mensajeDesembolso = ' El desembolso de $' . number_format($prestamo->monto, 2) . ' se realizará ' . $metodosTexto[$request->metodo_desembolso] . '.';
            }
            
            DB::commit();

            return redirect()->route('prestamos.show', $prestamo)
                ->with('success', 'Préstamo aprobado exitosamente. Se generaron ' . $prestamo->plazo . ' cuotas con vencimiento el día ' . $request->dia_vencimiento . ' de cada mes.' . $mensajeDesembolso);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Generar cuotas para un préstamo aprobado.
     */
    private function generarCuotas(Prestamo $prestamo, $fechaPrimerPago, $diaVencimiento)
    {
        $montoTotal = $prestamo->monto_total;
        $numeroCuotas = $prestamo->plazo;
        $montoCuota = $prestamo->monto_cuota;
        
        // Calcular el monto de capital e interés por cuota
        // En un sistema de cuota fija, el interés se distribuye proporcionalmente
        $interesTotal = $prestamo->monto_total - $prestamo->monto;
        $interesPorCuota = $interesTotal / $numeroCuotas;
        $capitalPorCuota = $prestamo->monto / $numeroCuotas;
        
        $saldoPendiente = $montoTotal;
        
        // Convertir fecha a objeto Carbon y día a entero
        $fechaVencimiento = \Carbon\Carbon::parse($fechaPrimerPago);
        $diaVencimiento = (int) $diaVencimiento; // Convertir a entero
        
        for ($i = 1; $i <= $numeroCuotas; $i++) {
            // Para la primera cuota, usar la fecha exacta ingresada
            if ($i > 1) {
                // Para las siguientes cuotas, avanzar un mes
                $fechaVencimiento->addMonth();
                
                // Ajustar al día de vencimiento especificado
                // Si el mes no tiene ese día (ej: 31 en febrero), usar el último día del mes
                $ultimoDiaMes = $fechaVencimiento->daysInMonth;
                $diaAjustado = min($diaVencimiento, $ultimoDiaMes);
                $fechaVencimiento->day = $diaAjustado;
            }
            
            // Para la última cuota, ajustar el monto para evitar errores de redondeo
            if ($i === $numeroCuotas) {
                $montoCuotaActual = $saldoPendiente;
            } else {
                $montoCuotaActual = round($montoCuota, 2);
            }
            
            \App\Models\Cuota::create([
                'prestamo_id' => $prestamo->id,
                'numero_cuota' => $i,
                'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
                'monto' => $montoCuotaActual,
                'capital' => round($capitalPorCuota, 2),
                'interes' => round($interesPorCuota, 2),
                'mora' => 0,
                'saldo_pendiente' => $saldoPendiente,
                'estado' => 'PENDIENTE',
            ]);
            
            $saldoPendiente -= $montoCuotaActual;
        }
    }

    /**
     * Rechazar un préstamo.
     */
    public function rechazar(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|min:10',
        ], [
            'motivo_rechazo.required' => 'El motivo de rechazo es obligatorio',
            'motivo_rechazo.min' => 'El motivo debe tener al menos 10 caracteres',
        ]);

        if ($prestamo->estado_aprobacion !== 'PENDIENTE') {
            return back()->with('error', 'Este préstamo ya fue procesado');
        }

        DB::beginTransaction();
        try {
            $prestamo->update([
                'estado_aprobacion' => 'RECHAZADO',
                'estado' => 'RECHAZADO',
                'fecha_aprobacion' => now(),
                'usuario_aprobador_id' => auth()->id(),
                'motivo_rechazo' => $request->motivo_rechazo,
            ]);

            DB::commit();

            return redirect()->route('prestamos.show', $prestamo)
                ->with('success', 'Préstamo rechazado');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al rechazar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource (solo para préstamos pendientes).
     */
    public function edit(Prestamo $prestamo)
    {
        if ($prestamo->estado_aprobacion !== 'PENDIENTE') {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'error' => 'Solo se pueden editar préstamos pendientes'
                ], 400);
            }
            return redirect()->route('prestamos.index')
                ->with('error', 'Solo se pueden editar préstamos pendientes');
        }

        $socios = Socio::where('estado', 'ACTIVO')->get();
        $tiposPrestamo = TipoPrestamo::where('estado', 'ACTIVO')->get();
        
        // Si es AJAX, devolver JSON para modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'prestamo' => $prestamo,
                'socios' => $socios,
                'tiposPrestamo' => $tiposPrestamo
            ]);
        }
        
        return view('prestamos.edit', compact('prestamo', 'socios', 'tiposPrestamo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->estado_aprobacion !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden editar préstamos pendientes');
        }

        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'tipo_prestamo_id' => 'required|exists:tipos_prestamo,id',
            'monto' => 'required|numeric|min:0',
            'plazo' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
        ]);

        // Obtener el tipo de préstamo para validaciones
        $tipoPrestamo = TipoPrestamo::findOrFail($request->tipo_prestamo_id);

        // Validar montos y plazos
        if ($request->monto < $tipoPrestamo->monto_minimo || $request->monto > $tipoPrestamo->monto_maximo) {
            return back()->withErrors([
                'monto' => "El monto debe estar entre $" . number_format($tipoPrestamo->monto_minimo, 2) . 
                           " y $" . number_format($tipoPrestamo->monto_maximo, 2)
            ])->withInput();
        }

        if ($request->plazo < $tipoPrestamo->plazo_minimo || $request->plazo > $tipoPrestamo->plazo_maximo) {
            return back()->withErrors([
                'plazo' => "El plazo debe estar entre {$tipoPrestamo->plazo_minimo} y {$tipoPrestamo->plazo_maximo} meses"
            ])->withInput();
        }

        // Recalcular intereses y montos
        $interes = $tipoPrestamo->interes;
        $interesTotal = ($request->monto * ($interes / 100) * $request->plazo) / 12;
        $montoTotal = $request->monto + $interesTotal;
        $montoCuota = $montoTotal / $request->plazo;

        $prestamo->update([
            'socio_id' => $request->socio_id,
            'tipo_prestamo_id' => $request->tipo_prestamo_id,
            'monto' => $request->monto,
            'monto_total' => $montoTotal,
            'monto_cuota' => $montoCuota,
            'interes' => $interes,
            'plazo' => $request->plazo,
            'saldo' => $montoTotal,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('prestamos.index')
            ->with('success', 'Préstamo actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prestamo $prestamo)
    {
        // Solo se pueden eliminar préstamos pendientes o rechazados
        if (!in_array($prestamo->estado_aprobacion, ['PENDIENTE', 'RECHAZADO'])) {
            return back()->with('error', 'Solo se pueden eliminar préstamos pendientes o rechazados');
        }

        // Verificar que no tenga cuotas pagadas
        if ($prestamo->cuotas()->where('estado', 'PAGADO')->count() > 0) {
            return back()->with('error', 'No se puede eliminar un préstamo con cuotas pagadas');
        }

        DB::beginTransaction();
        try {
            // Eliminar cuotas y garantías
            $prestamo->cuotas()->delete();
            $prestamo->garantias()->delete();
            $prestamo->delete();

            DB::commit();

            return redirect()->route('prestamos.index')
                ->with('success', 'Préstamo eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información del tipo de préstamo (para AJAX).
     */
    public function getTipoPrestamo($id)
    {
        $tipoPrestamo = TipoPrestamo::findOrFail($id);
        return response()->json($tipoPrestamo);
    }

    /**
     * Ejecutar detección de mora manualmente.
     */
    public function ejecutarDeteccionMora()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('mora:detectar');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return redirect()->route('prestamos.index')
                ->with('success', 'Detección de mora ejecutada exitosamente. ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al ejecutar detección de mora: ' . $e->getMessage());
        }
    }
}
