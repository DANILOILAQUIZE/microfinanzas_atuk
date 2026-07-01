<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Parametro::query();

        // Filtro por grupo
        if ($request->filled('grupo')) {
            $query->where('grupo', $request->grupo);
        }

        // Búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('clave', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $parametros = $query->orderBy('grupo')->orderBy('nombre')->paginate(20);
        $grupos = Parametro::select('grupo')->distinct()->pluck('grupo');

        return view('parametros.index', compact('parametros', 'grupos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:100|unique:parametros,clave',
            'nombre' => 'required|string|max:150',
            'valor' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:texto,numero,porcentaje,booleano',
            'grupo' => 'nullable|string|max:50',
        ], [
            'clave.required' => 'La clave es obligatoria',
            'clave.unique' => 'Ya existe un parámetro con esta clave',
            'nombre.required' => 'El nombre es obligatorio',
            'valor.required' => 'El valor es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
        ]);

        Parametro::create($request->all());

        return redirect()->route('parametros.index')
            ->with('success', 'Parámetro creado exitosamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parametro $parametro)
    {
        // Si es una petición AJAX, devolver JSON para el modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'parametro' => $parametro
            ]);
        }
        
        return response()->json(['parametro' => $parametro]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parametro $parametro)
    {
        $request->validate([
            'clave' => 'required|string|max:100|unique:parametros,clave,' . $parametro->id,
            'nombre' => 'required|string|max:150',
            'valor' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:texto,numero,porcentaje,booleano',
            'grupo' => 'nullable|string|max:50',
        ], [
            'clave.required' => 'La clave es obligatoria',
            'clave.unique' => 'Ya existe un parámetro con esta clave',
            'nombre.required' => 'El nombre es obligatorio',
            'valor.required' => 'El valor es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
        ]);

        $parametro->update($request->all());

        return redirect()->route('parametros.index')
            ->with('success', 'Parámetro actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parametro $parametro)
    {
        $parametro->delete();

        return redirect()->route('parametros.index')
            ->with('success', 'Parámetro eliminado exitosamente');
    }
}
