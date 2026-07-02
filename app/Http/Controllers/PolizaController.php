<?php

namespace App\Http\Controllers;

use App\Models\Poliza;
use App\Models\DetallePoliza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PolizaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Poliza::with('usuario', 'detalles');

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        $polizas = $query->orderBy('fecha', 'desc')->paginate(20);

        return view('polizas.index', compact('polizas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('polizas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:INGRESO,EGRESO,DIARIO',
            'concepto' => 'required|string|max:255',
            'detalles' => 'required|array|min:2',
            'detalles.*.cuenta' => 'required|string|max:100',
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0',
        ], [
            'fecha.required' => 'La fecha es obligatoria',
            'tipo.required' => 'El tipo de póliza es obligatorio',
            'concepto.required' => 'El concepto es obligatorio',
            'detalles.required' => 'Debe agregar al menos 2 detalles (debe y haber)',
            'detalles.min' => 'Debe agregar al menos 2 detalles (debe y haber)',
        ]);

        // Validar que debe = haber
        $totalDebe = collect($request->detalles)->sum('debe');
        $totalHaber = collect($request->detalles)->sum('haber');

        if (round($totalDebe, 2) != round($totalHaber, 2)) {
            return back()->withInput()->with('error', 'La póliza no está balanceada. Debe = Haber. Total Debe: $' . number_format($totalDebe, 2) . ' | Total Haber: $' . number_format($totalHaber, 2));
        }

        DB::beginTransaction();
        try {
            // Crear póliza
            $poliza = Poliza::create([
                'usuario_id' => auth()->id(),
                'fecha' => $request->fecha,
                'tipo' => $request->tipo,
                'concepto' => $request->concepto,
            ]);

            // Crear detalles
            foreach ($request->detalles as $detalle) {
                if ($detalle['debe'] > 0 || $detalle['haber'] > 0) {
                    DetallePoliza::create([
                        'poliza_id' => $poliza->id,
                        'cuenta' => $detalle['cuenta'],
                        'descripcion' => $detalle['descripcion'],
                        'debe' => $detalle['debe'],
                        'haber' => $detalle['haber'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polizas.index')
                ->with('success', 'Póliza contable creada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear la póliza: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Poliza $poliza)
    {
        $poliza->load('usuario', 'detalles');
        
        $totalDebe = $poliza->detalles->sum('debe');
        $totalHaber = $poliza->detalles->sum('haber');
        
        return view('polizas.show', compact('poliza', 'totalDebe', 'totalHaber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poliza $poliza)
    {
        $poliza->load('detalles');
        return view('polizas.edit', compact('poliza'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poliza $poliza)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:INGRESO,EGRESO,DIARIO',
            'concepto' => 'required|string|max:255',
            'detalles' => 'required|array|min:2',
            'detalles.*.cuenta' => 'required|string|max:100',
            'detalles.*.descripcion' => 'required|string|max:255',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0',
        ]);

        // Validar que debe = haber
        $totalDebe = collect($request->detalles)->sum('debe');
        $totalHaber = collect($request->detalles)->sum('haber');

        if (round($totalDebe, 2) != round($totalHaber, 2)) {
            return back()->withInput()->with('error', 'La póliza no está balanceada. Debe = Haber');
        }

        DB::beginTransaction();
        try {
            // Actualizar póliza
            $poliza->update([
                'fecha' => $request->fecha,
                'tipo' => $request->tipo,
                'concepto' => $request->concepto,
            ]);

            // Eliminar detalles antiguos
            $poliza->detalles()->delete();

            // Crear nuevos detalles
            foreach ($request->detalles as $detalle) {
                if ($detalle['debe'] > 0 || $detalle['haber'] > 0) {
                    DetallePoliza::create([
                        'poliza_id' => $poliza->id,
                        'cuenta' => $detalle['cuenta'],
                        'descripcion' => $detalle['descripcion'],
                        'debe' => $detalle['debe'],
                        'haber' => $detalle['haber'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('polizas.index')
                ->with('success', 'Póliza contable actualizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar la póliza: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poliza $poliza)
    {
        try {
            $poliza->detalles()->delete();
            $poliza->delete();

            return redirect()->route('polizas.index')
                ->with('success', 'Póliza contable eliminada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la póliza: ' . $e->getMessage());
        }
    }
}
