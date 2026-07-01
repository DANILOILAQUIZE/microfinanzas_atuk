# Configuración del Sistema de Detección de Mora

## Descripción

El sistema de detección automática de mora revisa diariamente las cuotas vencidas y calcula las penalizaciones correspondientes según los parámetros configurados.

## Parámetros Configurables

Los siguientes parámetros se pueden ajustar desde el módulo de **Administración → Parámetros**:

1. **Días de Gracia para Mora** (`dias_gracia_mora`)
   - Valor por defecto: 3 días
   - Descripción: Número de días de gracia antes de considerar un pago en mora

2. **Tasa de Mora Mensual** (`tasa_mora_mensual`)
   - Valor por defecto: 2.5%
   - Descripción: Porcentaje de interés moratorio que se aplica mensualmente

3. **Aplicar Mora sobre Capital** (`mora_sobre_capital`)
   - Valor por defecto: true
   - Descripción: Si es `true`, la mora se calcula sobre el capital pendiente; si es `false`, se calcula sobre el monto total de la cuota

## Fórmula de Cálculo

```
Base de Cálculo = mora_sobre_capital ? capital_cuota : monto_cuota
Días de Mora = días_transcurridos - dias_gracia
Mora = Base × (tasa_mora_mensual / 100 / 30) × dias_mora
```

## Ejecución Automática (Producción)

### Paso 1: Configurar el Cron de Laravel

En el servidor de producción, agrega la siguiente línea al crontab:

```bash
* * * * * cd /ruta/del/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Para editar el crontab:

```bash
crontab -e
```

### Paso 2: Verificar que el Comando está Programado

El comando `mora:detectar` está programado para ejecutarse diariamente a las 00:01 AM.

Para verificar los comandos programados:

```bash
php artisan schedule:list
```

## Ejecución Manual

Los administradores pueden ejecutar la detección de mora manualmente de dos formas:

### 1. Desde la Interfaz Web

- Ir a **Operaciones → Préstamos**
- Clic en el botón **"Detectar Mora"**
- Confirmar la ejecución

### 2. Desde la Línea de Comandos

```bash
php artisan mora:detectar
```

## Comportamiento del Sistema

1. **Detección**: El sistema busca cuotas en estado `PENDIENTE` cuya fecha de vencimiento sea anterior a (fecha_actual - dias_gracia)

2. **Cálculo**: Para cada cuota vencida, calcula la penalización por mora según la fórmula establecida

3. **Actualización**:
   - Cambia el estado de la cuota de `PENDIENTE` a `VENCIDA`
   - Registra el monto de mora calculado
   - Marca el préstamo como `VENCIDO` si tiene cuotas vencidas

4. **Logging**: Registra la ejecución en los logs de Laravel (`storage/logs/laravel.log`)

## Ejemplo de Salida del Comando

```
Iniciando detección de mora...
Parámetros: Días gracia=3, Tasa mora=2.5%, Sobre capital=Sí
Se encontraron 5 cuotas vencidas
Cuota #12 - Préstamo #3: 15 días mora, penalización: $4.50
Cuota #15 - Préstamo #5: 8 días mora, penalización: $2.40
Cuota #18 - Préstamo #7: 22 días mora, penalización: $6.60
Cuota #21 - Préstamo #9: 5 días mora, penalización: $1.50
Cuota #25 - Préstamo #11: 30 días mora, penalización: $9.00
Préstamo #3 marcado como VENCIDO
Préstamo #5 marcado como VENCIDO
Préstamo #7 marcado como VENCIDO
Préstamo #9 marcado como VENCIDO
Préstamo #11 marcado como VENCIDO
✓ Proceso completado: 5 cuotas actualizadas, 5 préstamos marcados como vencidos
```

## Monitoreo y Alertas

El sistema registra cada ejecución en:
- **Logs de Laravel**: `storage/logs/laravel.log`
- **Logs del Sistema**: Visible en el módulo de Auditoría (próxima implementación)

## Consideraciones

- El comando está protegido contra ejecuciones simultáneas con `withoutOverlapping()`
- Se ejecuta en un solo servidor con `onOneServer()` (importante en ambientes con múltiples servidores)
- Las operaciones son transaccionales: si ocurre un error, se revierte todo el proceso
- El cálculo de mora es acumulativo: si ya existe mora previa, se sumará el nuevo cálculo

## Soporte

Para cualquier duda o problema con la detección de mora, contactar al administrador del sistema o revisar los logs en `storage/logs/laravel.log`.
