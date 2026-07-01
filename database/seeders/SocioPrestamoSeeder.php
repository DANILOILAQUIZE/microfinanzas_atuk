<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Socio;
use App\Models\Prestamo;
use App\Models\TipoPrestamo;
use Carbon\Carbon;

class SocioPrestamoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 10 socios de prueba
        $socios = [
            [
                'nombres' => 'Juan Carlos',
                'apellidos' => 'Pérez García',
                'cedula' => '1234567890',
                'fecha_nacimiento' => '1985-03-15',
                'genero' => 'M',
                'direccion' => 'Av. Principal 123',
                'ciudad' => 'Quito',
                'telefono' => '0998765432',
                'correo' => 'juan.perez@email.com',
                'ocupacion' => 'Comerciante',
                'ingresos_mensuales' => 1200,
                'estado' => 'ACTIVO',
            ],
            [
                'nombres' => 'María Fernanda',
                'apellidos' => 'López Martínez',
                'cedula' => '0987654321',
                'fecha_nacimiento' => '1990-07-22',
                'genero' => 'F',
                'direccion' => 'Calle Secundaria 456',
                'ciudad' => 'Guayaquil',
                'telefono' => '0987654321',
                'correo' => 'maria.lopez@email.com',
                'ocupacion' => 'Profesora',
                'ingresos_mensuales' => 900,
                'estado' => 'ACTIVO',
            ],
            [
                'nombres' => 'Pedro Antonio',
                'apellidos' => 'Rodríguez Silva',
                'cedula' => '1122334455',
                'fecha_nacimiento' => '1982-11-10',
                'genero' => 'M',
                'direccion' => 'Barrio San Juan 789',
                'ciudad' => 'Cuenca',
                'telefono' => '0991234567',
                'correo' => 'pedro.rodriguez@email.com',
                'ocupacion' => 'Agricultor',
                'ingresos_mensuales' => 600,
                'estado' => 'ACTIVO',
            ],
            [
                'nombres' => 'Ana Lucía',
                'apellidos' => 'Gómez Torres',
                'cedula' => '2233445566',
                'fecha_nacimiento' => '1995-05-18',
                'genero' => 'F',
                'direccion' => 'Urbanización Los Pinos 321',
                'ciudad' => 'Quito',
                'telefono' => '0992345678',
                'correo' => 'ana.gomez@email.com',
                'ocupacion' => 'Estilista',
                'ingresos_mensuales' => 800,
                'estado' => 'ACTIVO',
            ],
            [
                'nombres' => 'Luis Fernando',
                'apellidos' => 'Ramírez Castro',
                'cedula' => '3344556677',
                'fecha_nacimiento' => '1988-09-25',
                'genero' => 'M',
                'direccion' => 'Sector Norte 654',
                'ciudad' => 'Ambato',
                'telefono' => '0993456789',
                'correo' => 'luis.ramirez@email.com',
                'ocupacion' => 'Mecánico',
                'ingresos_mensuales' => 1000,
                'estado' => 'ACTIVO',
            ],
        ];

        foreach ($socios as $socioData) {
            Socio::create($socioData);
        }

        // Obtener los tipos de préstamo y el primer usuario (administrador)
        $tiposPrestamo = TipoPrestamo::all();
        $adminId = 1; // Usuario administrador

        // Crear algunos préstamos de ejemplo (algunos aprobados, algunos pendientes)
        $prestamos = [
            [
                'socio_id' => 1,
                'tipo_prestamo_id' => $tiposPrestamo->where('nombre', 'Microcrédito')->first()->id,
                'monto' => 2000,
                'plazo' => 12,
                'estado_aprobacion' => 'PENDIENTE',
                'observaciones' => 'Préstamo para capital de trabajo',
            ],
            [
                'socio_id' => 2,
                'tipo_prestamo_id' => $tiposPrestamo->where('nombre', 'Consumo')->first()->id,
                'monto' => 5000,
                'plazo' => 24,
                'estado_aprobacion' => 'PENDIENTE',
                'observaciones' => 'Para compra de electrodomésticos',
            ],
            [
                'socio_id' => 3,
                'tipo_prestamo_id' => $tiposPrestamo->where('nombre', 'Microcrédito')->first()->id,
                'monto' => 1500,
                'plazo' => 6,
                'estado_aprobacion' => 'APROBADO',
                'estado' => 'ACTIVO',
                'fecha_aprobacion' => Carbon::now()->subDays(30),
                'fecha_desembolso' => Carbon::now()->subDays(30),
                'usuario_aprobador_id' => $adminId,
                'observaciones' => 'Préstamo para insumos agrícolas',
            ],
            [
                'socio_id' => 4,
                'tipo_prestamo_id' => $tiposPrestamo->where('nombre', 'Emergencia')->first()->id,
                'monto' => 1000,
                'plazo' => 6,
                'estado_aprobacion' => 'APROBADO',
                'estado' => 'ACTIVO',
                'fecha_aprobacion' => Carbon::now()->subDays(60),
                'fecha_desembolso' => Carbon::now()->subDays(60),
                'usuario_aprobador_id' => $adminId,
                'observaciones' => 'Préstamo para emergencia médica',
            ],
        ];

        foreach ($prestamos as $prestamoData) {
            $tipoPrestamo = TipoPrestamo::find($prestamoData['tipo_prestamo_id']);
            $interes = $tipoPrestamo->interes;
            $interesTotal = ($prestamoData['monto'] * ($interes / 100) * $prestamoData['plazo']) / 12;
            $montoTotal = $prestamoData['monto'] + $interesTotal;
            $montoCuota = $montoTotal / $prestamoData['plazo'];

            $prestamo = Prestamo::create([
                'socio_id' => $prestamoData['socio_id'],
                'tipo_prestamo_id' => $prestamoData['tipo_prestamo_id'],
                'usuario_id' => $adminId,
                'fecha_solicitud' => $prestamoData['estado_aprobacion'] === 'APROBADO' 
                    ? $prestamoData['fecha_aprobacion']
                    : Carbon::now(),
                'monto' => $prestamoData['monto'],
                'monto_total' => $montoTotal,
                'monto_cuota' => $montoCuota,
                'interes' => $interes,
                'plazo' => $prestamoData['plazo'],
                'saldo' => $montoTotal,
                'estado' => $prestamoData['estado'] ?? 'PENDIENTE',
                'estado_aprobacion' => $prestamoData['estado_aprobacion'],
                'fecha_aprobacion' => $prestamoData['fecha_aprobacion'] ?? null,
                'fecha_desembolso' => $prestamoData['fecha_desembolso'] ?? null,
                'usuario_aprobador_id' => $prestamoData['usuario_aprobador_id'] ?? null,
                'observaciones' => $prestamoData['observaciones'],
            ]);

            // Si está aprobado, generar las cuotas
            if ($prestamoData['estado_aprobacion'] === 'APROBADO') {
                $this->generarCuotas($prestamo);
            }
        }
    }

    /**
     * Generar cuotas para un préstamo.
     */
    private function generarCuotas(Prestamo $prestamo)
    {
        $montoTotal = $prestamo->monto_total;
        $numeroCuotas = $prestamo->plazo;
        $montoCuota = $prestamo->monto_cuota;
        
        $interesTotal = $prestamo->monto_total - $prestamo->monto;
        $interesPorCuota = $interesTotal / $numeroCuotas;
        $capitalPorCuota = $prestamo->monto / $numeroCuotas;
        
        $saldoPendiente = $montoTotal;
        $fechaVencimiento = Carbon::parse($prestamo->fecha_desembolso);

        for ($i = 1; $i <= $numeroCuotas; $i++) {
            $fechaVencimiento = $fechaVencimiento->addMonth();
            
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
}
