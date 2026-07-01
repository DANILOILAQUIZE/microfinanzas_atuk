<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro;

class ParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parametros = [
            // Parámetros de Mora
            [
                'clave' => 'dias_gracia_mora',
                'nombre' => 'Días de Gracia para Mora',
                'valor' => '3',
                'descripcion' => 'Número de días de gracia antes de considerar un pago en mora',
                'tipo' => 'numero',
                'grupo' => 'mora',
            ],
            [
                'clave' => 'tasa_mora_mensual',
                'nombre' => 'Tasa de Mora Mensual',
                'valor' => '2.5',
                'descripcion' => 'Porcentaje de interés moratorio que se aplica mensualmente',
                'tipo' => 'porcentaje',
                'grupo' => 'mora',
            ],
            [
                'clave' => 'mora_sobre_capital',
                'nombre' => 'Aplicar Mora sobre Capital',
                'valor' => 'true',
                'descripcion' => 'Determina si la mora se calcula sobre el capital pendiente o solo sobre la cuota',
                'tipo' => 'booleano',
                'grupo' => 'mora',
            ],
            
            // Parámetros del Sistema
            [
                'clave' => 'nombre_cooperativa',
                'nombre' => 'Nombre de la Cooperativa',
                'valor' => 'Caja de Ahorro ATUK',
                'descripcion' => 'Nombre oficial de la cooperativa que aparece en reportes y documentos',
                'tipo' => 'texto',
                'grupo' => 'sistema',
            ],
            [
                'clave' => 'ruc_cooperativa',
                'nombre' => 'RUC de la Cooperativa',
                'valor' => '1234567890001',
                'descripcion' => 'Registro Único de Contribuyentes de la cooperativa',
                'tipo' => 'texto',
                'grupo' => 'sistema',
            ],
            [
                'clave' => 'email_soporte',
                'nombre' => 'Email de Soporte',
                'valor' => 'soporte@atuk.com',
                'descripcion' => 'Email principal para soporte y contacto',
                'tipo' => 'texto',
                'grupo' => 'sistema',
            ],
            [
                'clave' => 'telefono_contacto',
                'nombre' => 'Teléfono de Contacto',
                'valor' => '(02) 123-4567',
                'descripcion' => 'Teléfono principal de la cooperativa',
                'tipo' => 'texto',
                'grupo' => 'sistema',
            ],
            
            // Parámetros de Transacciones
            [
                'clave' => 'monto_minimo_ahorro',
                'nombre' => 'Monto Mínimo de Ahorro Inicial',
                'valor' => '50.00',
                'descripcion' => 'Monto mínimo requerido para aperturar una cuenta de ahorro',
                'tipo' => 'numero',
                'grupo' => 'transacciones',
            ],
            [
                'clave' => 'saldo_minimo_cuenta',
                'nombre' => 'Saldo Mínimo en Cuenta',
                'valor' => '10.00',
                'descripcion' => 'Saldo mínimo que debe mantener una cuenta de ahorro',
                'tipo' => 'numero',
                'grupo' => 'transacciones',
            ],
            [
                'clave' => 'monto_maximo_retiro',
                'nombre' => 'Monto Máximo de Retiro Diario',
                'valor' => '5000.00',
                'descripcion' => 'Monto máximo permitido para retiros en un día',
                'tipo' => 'numero',
                'grupo' => 'transacciones',
            ],
            
            // Parámetros de Notificaciones
            [
                'clave' => 'dias_antes_aviso_cuota',
                'nombre' => 'Días de Anticipación para Aviso de Cuota',
                'valor' => '5',
                'descripcion' => 'Días antes del vencimiento para enviar notificación de pago',
                'tipo' => 'numero',
                'grupo' => 'notificaciones',
            ],
            [
                'clave' => 'enviar_email_notificaciones',
                'nombre' => 'Enviar Notificaciones por Email',
                'valor' => 'true',
                'descripcion' => 'Habilitar el envío de notificaciones por correo electrónico',
                'tipo' => 'booleano',
                'grupo' => 'notificaciones',
            ],
        ];

        foreach ($parametros as $parametro) {
            Parametro::firstOrCreate(
                ['clave' => $parametro['clave']],
                $parametro
            );
        }
    }
}
