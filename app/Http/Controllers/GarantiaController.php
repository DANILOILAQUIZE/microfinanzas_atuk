<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarantiaController extends Controller
{
    /**
     * Display a listing of the resource (todas las garantías del sistema).
     */
    public function index(Request $request)
    {
        $query = Garantia::with(['prestamo.socio']);

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Búsqueda por socio o descripción
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('descripcion', 'like', "%{$buscar}%")
                  ->orWhereHas('prestamo.socio', function($sq) use ($buscar) {
                      $sq->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%");
                  });
            });
        }

        $garantias = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('garantias.index', compact('garantias'));
    }

    /**
     * Store a newly created resource in storage (desde el detalle del préstamo).
     */
    public function store(Request $request)
    {
        $request->validate([
            'prestamo_id' => 'required|exists:prestamos,id',
            'tipo' => 'required|in:VEHICULO,INMUEBLE,MAQUINARIA,EQUIPOS,OTROS',
            'descripcion' => 'required|string|max:500',
            'valor' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date',
            'observaciones' => 'nullable|string',
        ], [
            'prestamo_id.required' => 'El préstamo es obligatorio',
            'tipo.required' => 'El tipo de garantía es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'valor.required' => 'El valor de la garantía es obligatorio',
            'valor.min' => 'El valor debe ser mayor a 0',
            'fecha_registro.required' => 'La fecha de registro es obligatoria',
        ]);

        DB::beginTransaction();
        try {
            $garantia = Garantia::create([
                'prestamo_id' => $request->prestamo_id,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion,
                'valor' => $request->valor,
                'estado' => 'ACTIVA',
                'fecha_registro' => $request->fecha_registro,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('prestamos.show', $request->prestamo_id)
                ->with('success', 'Garantía registrada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la garantía: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Garantia $garantia)
    {
        $garantia->load(['prestamo.socio']);
        return view('garantias.show', compact('garantia'));
    }

    /**
     * Show the form for editing the specified resource (AJAX para modal).
     */
    public function edit(Garantia $garantia)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($garantia);
        }

        return view('garantias.edit', compact('garantia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Garantia $garantia)
    {
        // Solo se pueden editar garantías activas
        if ($garantia->estado !== 'ACTIVA') {
            return back()->with('error', 'Solo se pueden editar garantías activas');
        }

        $request->validate([
            'tipo' => 'required|in:VEHICULO,INMUEBLE,MAQUINARIA,EQUIPOS,OTROS',
            'descripcion' => 'required|string|max:500',
            'valor' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $garantia->update([
            'tipo' => $request->tipo,
            'descripcion' => $request->descripcion,
            'valor' => $request->valor,
            'fecha_registro' => $request->fecha_registro,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('prestamos.show', $garantia->prestamo_id)
            ->with('success', 'Garantía actualizada exitosamente');
    }

    /**
     * Liberar una garantía (cuando el préstamo se cancela).
     */
    public function liberar(Garantia $garantia)
    {
        if ($garantia->estado !== 'ACTIVA') {
            return back()->with('error', 'Solo se pueden liberar garantías activas');
        }

        // Verificar que el préstamo esté cancelado
        if ($garantia->prestamo->estado !== 'CANCELADO') {
            return back()->with('error', 'Solo se pueden liberar garantías de préstamos cancelados');
        }

        DB::beginTransaction();
        try {
            $garantia->update([
                'estado' => 'LIBERADA',
                'fecha_liberacion' => now(),
            ]);

            DB::commit();

            return redirect()->route('prestamos.show', $garantia->prestamo_id)
                ->with('success', 'Garantía liberada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al liberar la garantía: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Garantia $garantia)
    {
        // Solo se pueden eliminar garantías activas de préstamos pendientes
        if ($garantia->prestamo->estado_aprobacion !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden eliminar garantías de préstamos pendientes');
        }

        $prestamoId = $garantia->prestamo_id;
        $garantia->delete();

        return redirect()->route('prestamos.show', $prestamoId)
            ->with('success', 'Garantía eliminada exitosamente');
    }
}
