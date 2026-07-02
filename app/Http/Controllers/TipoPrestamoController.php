<?php

namespace App\Http\Controllers;

use App\Models\TipoPrestamo;
use Illuminate\Http\Request;

class TipoPrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoPrestamo::query();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $tiposPrestamo = $query->orderBy('nombre', 'asc')->paginate(15);

        return view('tipos-prestamo.index', compact('tiposPrestamo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos-prestamo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_prestamo,nombre',
            'descripcion' => 'nullable|string|max:500',
            'interes' => 'required|numeric|min:0|max:100',
            'monto_minimo' => 'required|numeric|min:0',
            'monto_maximo' => 'required|numeric|min:0|gte:monto_minimo',
            'plazo_minimo' => 'required|integer|min:1',
            'plazo_maximo' => 'required|integer|min:1|gte:plazo_minimo',
            'requiere_garantia' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de préstamo con este nombre',
            'interes.required' => 'La tasa de interés es obligatoria',
            'interes.min' => 'La tasa de interés no puede ser negativa',
            'interes.max' => 'La tasa de interés no puede ser mayor a 100%',
            'monto_minimo.required' => 'El monto mínimo es obligatorio',
            'monto_maximo.required' => 'El monto máximo es obligatorio',
            'monto_maximo.gte' => 'El monto máximo debe ser mayor o igual al monto mínimo',
            'plazo_minimo.required' => 'El plazo mínimo es obligatorio',
            'plazo_maximo.required' => 'El plazo máximo es obligatorio',
            'plazo_maximo.gte' => 'El plazo máximo debe ser mayor o igual al plazo mínimo',
        ]);

        TipoPrestamo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'interes' => $request->interes,
            'monto_minimo' => $request->monto_minimo,
            'monto_maximo' => $request->monto_maximo,
            'plazo_minimo' => $request->plazo_minimo,
            'plazo_maximo' => $request->plazo_maximo,
            'requiere_garantia' => $request->has('requiere_garantia') ? 1 : 0,
            'estado' => 'ACTIVO',
        ]);

        return redirect()->route('tipos-prestamo.index')
            ->with('success', 'Tipo de préstamo creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoPrestamo $tiposPrestamo)
    {
        $tiposPrestamo->load(['prestamos' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);
        
        return view('tipos-prestamo.show', compact('tiposPrestamo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoPrestamo $tiposPrestamo)
    {
        // Si es una petición AJAX, devolver JSON para el modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'tipo' => $tiposPrestamo
            ]);
        }
        
        return view('tipos-prestamo.edit', compact('tiposPrestamo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoPrestamo $tiposPrestamo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_prestamo,nombre,' . $tiposPrestamo->id,
            'descripcion' => 'nullable|string|max:500',
            'interes' => 'required|numeric|min:0|max:100',
            'monto_minimo' => 'required|numeric|min:0',
            'monto_maximo' => 'required|numeric|min:0|gte:monto_minimo',
            'plazo_minimo' => 'required|integer|min:1',
            'plazo_maximo' => 'required|integer|min:1|gte:plazo_minimo',
            'requiere_garantia' => 'boolean',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de préstamo con este nombre',
            'interes.required' => 'La tasa de interés es obligatoria',
            'interes.min' => 'La tasa de interés no puede ser negativa',
            'interes.max' => 'La tasa de interés no puede ser mayor a 100%',
            'monto_minimo.required' => 'El monto mínimo es obligatorio',
            'monto_maximo.required' => 'El monto máximo es obligatorio',
            'monto_maximo.gte' => 'El monto máximo debe ser mayor o igual al monto mínimo',
            'plazo_minimo.required' => 'El plazo mínimo es obligatorio',
            'plazo_maximo.required' => 'El plazo máximo es obligatorio',
            'plazo_maximo.gte' => 'El plazo máximo debe ser mayor o igual al plazo mínimo',
        ]);

        $tiposPrestamo->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'interes' => $request->interes,
            'monto_minimo' => $request->monto_minimo,
            'monto_maximo' => $request->monto_maximo,
            'plazo_minimo' => $request->plazo_minimo,
            'plazo_maximo' => $request->plazo_maximo,
            'requiere_garantia' => $request->has('requiere_garantia') ? 1 : 0,
            'estado' => $request->estado,
        ]);

        return redirect()->route('tipos-prestamo.index')
            ->with('success', 'Tipo de préstamo actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoPrestamo $tiposPrestamo)
    {
        // Verificar si tiene préstamos asociados
        if ($tiposPrestamo->prestamos()->count() > 0) {
            return redirect()->route('tipos-prestamo.index')
                ->with('error', 'No se puede eliminar el tipo de préstamo porque tiene préstamos asociados');
        }

        $tiposPrestamo->delete();

        return redirect()->route('tipos-prestamo.index')
            ->with('success', 'Tipo de préstamo eliminado exitosamente');
    }
}
