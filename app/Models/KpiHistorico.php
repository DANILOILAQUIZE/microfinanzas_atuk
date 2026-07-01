<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiHistorico extends Model
{
    protected $table = 'kpi_historicos';

    protected $fillable = [
        'fecha',
        'cartera_total',
        'cartera_vencida',
        'total_prestamos',
        'prestamos_pendientes',
        'prestamos_aprobados_mes',
        'socios_activos',
        'socios_totales',
        'indice_morosidad',
        'cuotas_vencidas',
        'monto_mora_total',
        'saldo_ahorro',
        'cuentas_ahorro',
        'pagos_mes',
        'monto_pagos_mes',
        'movimientos_mes',
        'depositos_mes',
        'retiros_mes',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cartera_total' => 'decimal:2',
        'cartera_vencida' => 'decimal:2',
        'total_prestamos' => 'integer',
        'prestamos_pendientes' => 'integer',
        'prestamos_aprobados_mes' => 'integer',
        'socios_activos' => 'integer',
        'socios_totales' => 'integer',
        'indice_morosidad' => 'decimal:2',
        'cuotas_vencidas' => 'integer',
        'monto_mora_total' => 'decimal:2',
        'saldo_ahorro' => 'decimal:2',
        'cuentas_ahorro' => 'integer',
        'pagos_mes' => 'integer',
        'monto_pagos_mes' => 'decimal:2',
        'movimientos_mes' => 'integer',
        'depositos_mes' => 'decimal:2',
        'retiros_mes' => 'decimal:2',
    ];
}
