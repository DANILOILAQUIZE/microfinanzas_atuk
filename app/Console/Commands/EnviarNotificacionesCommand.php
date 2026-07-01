<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notificacion;
use App\Models\Cuota;
use App\Models\Parametro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionesCommand extends Command
{
    protected $signature = 'notificaciones:enviar';
    protected $description = 'Generar y enviar notificaciones automáticas';

    public function handle()
    {
        $this->info("Generando notificaciones...");

        try {
            DB::beginTransaction();

            $notificacionesGeneradas = 0;

            // 1. NOTIFICACIONES DE CUOTAS PRÓXIMAS A VENCER
            $notificacionesGeneradas += $this->notificarCuotasProximas();

            // 2. NOTIFICACIONES DE CUOTAS VENCIDAS
            $notificacionesGeneradas += $this->notificarCuotasVencidas();

            // 3. NOTIFICACIONES DE PRÉSTAMOS APROBADOS
            $notificacionesGeneradas += $this->notificarPrestamosAprobados();

            DB::commit();

            $this->info("✓ Notificaciones generadas: {$notificacionesGeneradas}");
            
            // Ahora enviar las notificaciones pendientes
            $this->enviarNotificacionesPendientes();

            Log::info("Notificaciones procesadas: {$notificacionesGeneradas}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error al generar notificaciones: " . $e->getMessage());
            Log::error("Error al generar notificaciones: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Notificar a socios sobre cuotas próximas a vencer
     */
    private function notificarCuotasProximas()
    {
        $diasAnticipacion = Parametro::where('clave', 'dias_anticipacion_notificacion')->value('valor') ?? 5;
        $fechaLimite = Carbon::today()->addDays($diasAnticipacion);

        $cuotasProximas = Cuota::where('estado', 'PENDIENTE')
            ->whereBetween('fecha_vencimiento', [Carbon::today()->addDay(), $fechaLimite])
            ->with('prestamo.socio')
            ->get();

        $notificaciones = 0;
        foreach ($cuotasProximas as $cuota) {
            // Verificar si ya existe notificación para esta cuota en las últimas 24 horas
            $existe = Notificacion::where('socio_id', $cuota->prestamo->socio_id)
                ->where('tipo', 'CUOTA_PROXIMA')
                ->where('mensaje', 'like', "%Cuota #{$cuota->numero_cuota}%")
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->exists();

            if (!$existe) {
                $diasRestantes = Carbon::today()->diffInDays($cuota->fecha_vencimiento);
                
                Notificacion::create([
                    'socio_id' => $cuota->prestamo->socio_id,
                    'usuario_id' => null,
                    'titulo' => 'Recordatorio de Pago',
                    'mensaje' => "Estimado {$cuota->prestamo->socio->nombres}, le recordamos que la Cuota #{$cuota->numero_cuota} de su préstamo {$cuota->prestamo->codigo} vence en {$diasRestantes} días ({$cuota->fecha_vencimiento->format('d/m/Y')}). Monto a pagar: $" . number_format($cuota->monto, 2),
                    'tipo' => 'CUOTA_PROXIMA',
                    'canal' => 'SISTEMA',
                    'link' => route('prestamos.show', $cuota->prestamo_id),
                    'leida' => false,
                    'enviada' => false,
                    'fecha_notificacion' => Carbon::now(),
                ]);
                $notificaciones++;
            }
        }

        return $notificaciones;
    }

    /**
     * Notificar sobre cuotas ya vencidas
     */
    private function notificarCuotasVencidas()
    {
        $cuotasVencidas = Cuota::where('estado', 'VENCIDA')
            ->whereDate('fecha_vencimiento', '<=', Carbon::today())
            ->with('prestamo.socio')
            ->get();

        $notificaciones = 0;
        foreach ($cuotasVencidas as $cuota) {
            // Solo notificar una vez por semana sobre cuotas vencidas
            $existe = Notificacion::where('socio_id', $cuota->prestamo->socio_id)
                ->where('tipo', 'CUOTA_VENCIDA')
                ->where('mensaje', 'like', "%Cuota #{$cuota->numero_cuota}%")
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->exists();

            if (!$existe) {
                $diasVencidos = $cuota->fecha_vencimiento->diffInDays(Carbon::today());
                
                Notificacion::create([
                    'socio_id' => $cuota->prestamo->socio_id,
                    'usuario_id' => null,
                    'titulo' => 'Cuota Vencida',
                    'mensaje' => "Estimado {$cuota->prestamo->socio->nombres}, la Cuota #{$cuota->numero_cuota} de su préstamo {$cuota->prestamo->codigo} está vencida desde hace {$diasVencidos} días. Monto adeudado: $" . number_format($cuota->saldo_pendiente + $cuota->mora, 2) . " (incluye mora: $" . number_format($cuota->mora, 2) . "). Por favor, regularice su situación.",
                    'tipo' => 'CUOTA_VENCIDA',
                    'canal' => 'SISTEMA',
                    'link' => route('prestamos.show', $cuota->prestamo_id),
                    'leida' => false,
                    'enviada' => false,
                    'fecha_notificacion' => Carbon::now(),
                ]);
                $notificaciones++;
            }
        }

        return $notificaciones;
    }

    /**
     * Notificar a usuarios sobre préstamos aprobados recientemente
     */
    private function notificarPrestamosAprobados()
    {
        $prestamosAprobados = \App\Models\Prestamo::where('estado_aprobacion', 'APROBADO')
            ->whereDate('fecha_aprobacion', Carbon::today())
            ->with('socio')
            ->get();

        $notificaciones = 0;
        foreach ($prestamosAprobados as $prestamo) {
            $existe = Notificacion::where('socio_id', $prestamo->socio_id)
                ->where('tipo', 'PRESTAMO_APROBADO')
                ->where('mensaje', 'like', "%{$prestamo->codigo}%")
                ->exists();

            if (!$existe) {
                Notificacion::create([
                    'socio_id' => $prestamo->socio_id,
                    'usuario_id' => null,
                    'titulo' => '¡Préstamo Aprobado!',
                    'mensaje' => "Estimado {$prestamo->socio->nombres}, nos complace informarle que su préstamo {$prestamo->codigo} por $" . number_format($prestamo->monto, 2) . " ha sido aprobado. El desembolso se realizará el {$prestamo->fecha_desembolso->format('d/m/Y')}.",
                    'tipo' => 'PRESTAMO_APROBADO',
                    'canal' => 'SISTEMA',
                    'link' => route('prestamos.show', $prestamo->id),
                    'leida' => false,
                    'enviada' => false,
                    'fecha_notificacion' => Carbon::now(),
                ]);
                $notificaciones++;
            }
        }

        return $notificaciones;
    }

    /**
     * Enviar notificaciones pendientes (simulación)
     * En producción aquí se integraría con servicio de email/SMS
     */
    private function enviarNotificacionesPendientes()
    {
        $enviarEmail = Parametro::where('clave', 'enviar_email_notificaciones')->value('valor') == 'true';

        if (!$enviarEmail) {
            $this->line("  → Envío de emails deshabilitado en parámetros");
            return;
        }

        $pendientes = Notificacion::where('enviada', false)
            ->where('canal', '!=', 'SISTEMA')
            ->whereDate('created_at', Carbon::today())
            ->with('socio')
            ->get();

        $enviadas = 0;
        foreach ($pendientes as $notificacion) {
            // SIMULACIÓN: En producción aquí se enviaría email real
            // Mail::to($notificacion->socio->correo)->send(new NotificacionMail($notificacion));
            
            $notificacion->update([
                'enviada' => true,
                'fecha_envio' => Carbon::now(),
            ]);
            $enviadas++;
        }

        if ($enviadas > 0) {
            $this->line("  → Notificaciones enviadas por email: {$enviadas}");
        }
    }
}
