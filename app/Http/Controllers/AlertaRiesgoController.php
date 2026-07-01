<?php

namespace App\Http\Controllers;

use App\Models\AlertaRiesgo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AlertaRiesgoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AlertaRiesgo::with(['socio', 'prestamo'])
            ->orderBy('fecha_alerta', 'desc');

        // Filtro por nivel
        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        // Filtro por tipo de alerta
        if ($request->filled('tipo_alerta')) {
            $query->where('tipo_alerta', $request->tipo_alerta);
        }

        // Filtro por estado (leída/no leída)
        if ($request->filled('leida')) {
            $query->where('leida', $request->leida === '1');
        }

        // Búsqueda por socio
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('socio', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
            });
        }

        $alertas = $query->paginate(15);

        // Estadísticas
        $stats = [
            'total' => AlertaRiesgo::count(),
            'no_leidas' => AlertaRiesgo::where('leida', false)->count(),
            'criticas' => AlertaRiesgo::where('nivel', 'CRITICO')->where('leida', false)->count(),
            'altas' => AlertaRiesgo::where('nivel', 'ALTO')->where('leida', false)->count(),
            'medias' => AlertaRiesgo::where('nivel', 'MEDIO')->where('leida', false)->count(),
        ];

        return view('alertas.index', compact('alertas', 'stats'));
    }

    /**
     * Marcar alerta como leída
     */
    public function marcarLeida($id)
    {
        $alerta = AlertaRiesgo::findOrFail($id);
        $alerta->update(['leida' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Alerta marcada como leída'
        ]);
    }

    /**
     * Marcar todas las alertas como leídas
     */
    public function marcarTodasLeidas()
    {
        AlertaRiesgo::where('leida', false)->update(['leida' => true]);

        return redirect()->route('alertas.index')
            ->with('success', 'Todas las alertas han sido marcadas como leídas');
    }

    /**
     * Eliminar alertas antiguas (más de 30 días)
     */
    public function limpiarAntiguas()
    {
        $eliminadas = AlertaRiesgo::where('created_at', '<', now()->subDays(30))->delete();

        return redirect()->route('alertas.index')
            ->with('success', "Se eliminaron {$eliminadas} alertas antiguas");
    }

    /**
     * Generar alertas manualmente
     */
    public function generarManualmente()
    {
        try {
            Artisan::call('alertas:generar');
            $output = Artisan::output();

            return redirect()->route('alertas.index')
                ->with('success', 'Alertas generadas exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('alertas.index')
                ->with('error', 'Error al generar alertas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $alerta = AlertaRiesgo::with(['socio', 'prestamo.tipoPrestamo', 'prestamo.cuotas'])
            ->findOrFail($id);

        // Marcar como leída automáticamente al ver
        if (!$alerta->leida) {
            $alerta->update(['leida' => true]);
        }

        return view('alertas.show', compact('alerta'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $alerta = AlertaRiesgo::findOrFail($id);
        $alerta->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Alerta eliminada exitosamente'
            ]);
        }

        return redirect()->route('alertas.index')
            ->with('success', 'Alerta eliminada exitosamente');
    }
}
