<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AuditoriaLog::with('usuario');

        // Filtro por usuario
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Filtro por acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por tabla
        if ($request->filled('tabla')) {
            $query->where('tabla', $request->tabla);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener lista de usuarios para el filtro
        $usuarios = \App\Models\Usuario::orderBy('nombre')->get();

        // Obtener lista de tablas únicas
        $tablas = AuditoriaLog::select('tabla')->distinct()->orderBy('tabla')->pluck('tabla');

        return view('auditoria.index', compact('auditorias', 'usuarios', 'tablas'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditoriaLog $auditoria)
    {
        $auditoria->load('usuario');
        
        // Decodificar los JSON de valores
        $valoresAntiguos = json_decode($auditoria->valores_antiguos, true);
        $valoresNuevos = json_decode($auditoria->valores_nuevos, true);
        
        return view('auditoria.show', compact('auditoria', 'valoresAntiguos', 'valoresNuevos'));
    }
}
