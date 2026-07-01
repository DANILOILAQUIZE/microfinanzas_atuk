<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\Request;

class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Socio::query();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por nombre, cédula o email
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('apellidos', 'like', "%{$buscar}%")
                  ->orWhere('cedula', 'like', "%{$buscar}%")
                  ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        $socios = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('socios.index', compact('socios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('socios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|max:20|unique:socios,cedula',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'ocupacion' => 'nullable|string|max:100',
        ], [
            'cedula.required' => 'La cédula es obligatoria',
            'cedula.unique' => 'Ya existe un socio con esta cédula',
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'genero.required' => 'El género es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
        ]);

        Socio::create([
            'cedula' => $request->cedula,
            'nombres' => $request->nombre,  // Mapeo correcto
            'apellidos' => $request->apellido,  // Mapeo correcto
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'correo' => $request->email,  // Mapeo correcto
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'ocupacion' => $request->ocupacion,
            'estado' => 'ACTIVO',
        ]);

        return redirect()->route('socios.index')
            ->with('success', 'Socio registrado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Socio $socio)
    {
        $socio->load(['cuentasAhorro', 'prestamos']);
        return view('socios.show', compact('socio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Socio $socio)
    {
        // Si es una petición AJAX, devolver JSON para el modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'socio' => $socio
            ]);
        }
        
        return view('socios.edit', compact('socio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Socio $socio)
    {
        $request->validate([
            'cedula' => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'ocupacion' => 'nullable|string|max:100',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ], [
            'cedula.required' => 'La cédula es obligatoria',
            'cedula.unique' => 'Ya existe un socio con esta cédula',
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'genero.required' => 'El género es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'direccion.required' => 'La dirección es obligatoria',
        ]);

        $socio->update([
            'cedula' => $request->cedula,
            'nombres' => $request->nombre,  // Mapeo correcto
            'apellidos' => $request->apellido,  // Mapeo correcto
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'correo' => $request->email,  // Mapeo correcto
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'ocupacion' => $request->ocupacion,
            'estado' => $request->estado,
        ]);

        return redirect()->route('socios.index')
            ->with('success', 'Socio actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Socio $socio)
    {
        // Verificar si tiene cuentas o préstamos activos
        if ($socio->cuentasAhorro()->count() > 0 || $socio->prestamos()->count() > 0) {
            return redirect()->route('socios.index')
                ->with('error', 'No se puede eliminar el socio porque tiene cuentas o préstamos asociados');
        }

        $socio->delete();

        return redirect()->route('socios.index')
            ->with('success', 'Socio eliminado exitosamente');
    }
}
