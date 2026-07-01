<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class NotificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Notificacion::with(['socio', 'usuario'])
            ->orderBy('fecha_notificacion', 'desc');

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por canal
        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }

        // Filtro por estado (leída/no leída)
        if ($request->filled('leida')) {
            $query->where('leida', $request->leida === '1');
        }

        // Filtro por estado de envío
        if ($request->filled('enviada')) {
            $query->where('enviada', $request->enviada === '1');
        }

        // Búsqueda por socio
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('socio', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
            });
        }

        $notificaciones = $query->paginate(15);

        // Estadísticas
        $stats = [
            'total' => Notificacion::count(),
            'no_leidas' => Notificacion::where('leida', false)->count(),
            'pendientes_envio' => Notificacion::where('enviada', false)->where('canal', '!=', 'SISTEMA')->count(),
            'hoy' => Notificacion::whereDate('created_at', today())->count(),
        ];

        return view('notificaciones.index', compact('notificaciones', 'stats'));
    }

    /**
     * Obtener notificaciones del usuario actual (para el header)
     */
    public function getNotificacionesUsuario()
    {
        $notificaciones = Notificacion::where('usuario_id', auth()->id())
            ->where('leida', false)
            ->orderBy('fecha_notificacion', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'total_no_leidas' => Notificacion::where('usuario_id', auth()->id())->where('leida', false)->count(),
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        Notificacion::where('leida', false)->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return redirect()->route('notificaciones.index')
            ->with('success', 'Todas las notificaciones han sido marcadas como leídas');
    }

    /**
     * Enviar notificaciones manualmente
     */
    public function enviarManualmente()
    {
        try {
            Artisan::call('notificaciones:enviar');
            $output = Artisan::output();

            return redirect()->route('notificaciones.index')
                ->with('success', 'Notificaciones generadas y enviadas exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('notificaciones.index')
                ->with('error', 'Error al enviar notificaciones: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $notificacion = Notificacion::with(['socio', 'usuario'])
            ->findOrFail($id);

        // Marcar como leída automáticamente al ver
        if (!$notificacion->leida) {
            $notificacion->update([
                'leida' => true,
                'fecha_lectura' => now(),
            ]);
        }

        return view('notificaciones.show', compact('notificacion'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notificación eliminada exitosamente'
            ]);
        }

        return redirect()->route('notificaciones.index')
            ->with('success', 'Notificación eliminada exitosamente');
    }

    /**
     * Limpiar notificaciones antiguas (más de 60 días)
     */
    public function limpiarAntiguas()
    {
        $eliminadas = Notificacion::where('created_at', '<', now()->subDays(60))->delete();

        return redirect()->route('notificaciones.index')
            ->with('success', "Se eliminaron {$eliminadas} notificaciones antiguas");
    }
}
