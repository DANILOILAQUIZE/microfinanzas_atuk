# Guía de Implementación - Sistema ATUK

## 📊 Estado del Proyecto

**COMPLETADO: 22/28 tareas (79%)**

### ✅ SEMANA 1: Fundamentos (6/6 - 100%)
- 1.1 Sistema de roles y permisos
- 1.2 CRUD de Roles con modales
- 1.3 CRUD de Usuarios con modales
- 1.4 CRUD de Socios con modales XL
- 1.5 CRUD de Tipos de Préstamo
- 1.6 Módulo de Parámetros

### ✅ SEMANA 2: Operaciones Core (5/5 - 100%)
- 2.1 CRUD de Préstamos con flujo de aprobación
- 2.2 Generación automática de cuotas
- 2.3 Módulo de Pagos
- 2.4 CRUD de Garantías
- 2.5 Detección automática de mora

### ✅ SEMANA 3: Ahorro y BI (6/6 - 100%)
- 3.1 CRUD de Cuentas de Ahorro
- 3.2 Módulo de Movimientos de Ahorro
- 3.3 Dashboard BI completo con KPIs
- 3.4 Gráficos interactivos (Chart.js)
- 3.5 Tablas de análisis
- 3.6 Población del Data Warehouse

### ✅ SEMANA 4: Finalización (5/11 - 45%)
- 4.1 ✅ Sistema de Alertas de Riesgo
- 4.2 ✅ Sistema de Notificaciones
- 4.3 ✅ Módulo de Reportes BI
- 4.4 ✅ Auditoría Básica
- 4.9 ✅ Documentación (README)

---

## 🎯 Funcionalidades Implementadas

### Sistema de Gestión Operacional

#### Socios
- CRUD completo con modales XL (diseño 2 columnas)
- Campos: personales, contacto, laborales, financieros
- Validaciones completas
- Búsqueda y filtros
- Vista de detalle con historial

#### Cuentas de Ahorro
- Generación automática de número de cuenta (CA-YYYYMMDD-####)
- Validación de depósito mínimo
- Control de un solo cuenta activa por socio
- Estados: ACTIVA, INACTIVA, BLOQUEADA
- Saldos disponibles y bloqueados

#### Movimientos de Ahorro
- Tipos: DEPOSITO y RETIRO
- Métodos: EFECTIVO, TRANSFERENCIA, CHEQUE, TARJETA
- Validaciones automáticas:
  - Retiros: saldo disponible, monto máximo, saldo mínimo remanente
  - Depósitos: sin límite
- Comprobantes imprimibles
- Anulación (solo del mismo día)

#### Préstamos
- Flujo completo: Solicitud → Aprobación → Desembolso
- Estados de aprobación: PENDIENTE, APROBADO, RECHAZADO
- Estados operativos: ACTIVO, VENCIDO, CANCELADO
- Cálculo automático de intereses (simple anual)
- Generación automática de cuotas al aprobar
- Plan de pagos completo
- Garantías opcionales
- Validación de montos y plazos según tipo de préstamo

#### Cuotas
- Generación automática mensual
- Distribución proporcional de capital e interés
- Saldo pendiente decreciente
- Estados: PENDIENTE, PAGADA, VENCIDA
- Cálculo de mora automático

#### Pagos
- Registro con aplicación automática a cuotas
- Actualización de saldos de préstamo
- Marca préstamo como CANCELADO cuando saldo = 0
- Recibos imprimibles
- Anulación (solo del mismo día)
- Métodos de pago múltiples

#### Garantías
- Tipos: VEHICULO, INMUEBLE, MAQUINARIA, EQUIPOS, OTROS
- Estados: ACTIVA, LIBERADA, EJECUTADA
- Liberación automática al cancelar préstamo
- Documentos de soporte
- Valoración y observaciones

### Sistema de Business Intelligence

#### Dashboard Ejecutivo
- 16 KPIs principales en widgets modernos
- 4 gráficos interactivos:
  1. Evolución de Cartera (línea, 6 meses)
  2. Distribución por Tipo (donut)
  3. Morosidad Mensual (barras)
  4. Crecimiento de Ahorro (área)
- 4 tablas analíticas:
  1. Top 10 Socios por Saldo
  2. Cuotas Próximas a Vencer (7 días)
  3. Préstamos Recientes (5)
  4. Movimientos Recientes (5)
- Actualización en tiempo real

#### Data Warehouse
**Tablas Fact:**
- `hecho_cartera`: Snapshots diarios (global, por tipo, por socio)
- `hecho_morosidad`: Snapshots con rangos de mora (1-30, 31-60, 61-90, 90+)
- `kpi_historicos`: 17 KPIs históricos

**Tablas Dimension:**
- `dimension_temporal`: Dimensión de fechas (±1 año configurable)

**Comandos DW:**
```bash
php artisan dw:poblar-dimension-temporal    # Genera dimensión
php artisan dw:actualizar-cartera           # Snapshot cartera
php artisan dw:actualizar-morosidad         # Snapshot morosidad
php artisan dw:actualizar-kpis              # Snapshot KPIs
php artisan dw:actualizar-todo              # Ejecuta todos
```

**Programación:** Diaria a las 00:30 AM

#### Reportes BI (5 Reportes Completos)

1. **Evolución de Cartera**
   - Gráfico de líneas histórico (cartera total, vigente, vencida)
   - Gráfico donut de distribución por tipo
   - Top 10 socios por saldo
   - Estadísticas: cartera total, préstamos activos/vencidos, desembolsos mes
   - Filtros por rango de fechas

2. **Análisis de Morosidad**
   - Evolución del índice de morosidad
   - Rangos de mora (cuotas y montos)
   - Top 10 socios morosos
   - Estadísticas: índice %, cartera vencida, préstamos vencidos, mora promedio
   - Filtros por rango de fechas

3. **Rentabilidad y Pagos**
   - Gráfico de pagos mensuales
   - Ingresos por concepto (capital, interés, mora)
   - Tipos de préstamo más rentables
   - Estadísticas: ingresos período, pagos realizados, promedio, recuperación mes
   - Filtros por rango de fechas

4. **KPIs Históricos**
   - Evolución de 17 KPIs en el tiempo
   - KPIs actuales vs históricos
   - Gráficos de tendencias
   - Filtros por rango de fechas

5. **Análisis de Socios**
   - Distribución por estado
   - Top 10 socios más activos
   - Crecimiento mensual de socios
   - Estadísticas: total, activos, con préstamos, con ahorro

**Características de Reportes:**
- Gráficos interactivos (Chart.js 4.4)
- Filtros por rango de fechas
- Función de impresión (window.print)
- Diseño responsive
- Datos desde DW + operacional

#### Sistema de Alertas de Riesgo

**5 Tipos de Alertas Automáticas:**

1. **Mora Temprana**
   - Detección: Cuotas próximas a vencer (5 días)
   - Nivel: MEDIO
   - Frecuencia: Diaria, no duplicar en 24h

2. **Concentración de Crédito**
   - Detección: Socios con 3+ préstamos activos
   - Nivel: ALTO
   - Frecuencia: Semanal

3. **Capacidad de Pago**
   - Detección: Cuota mensual > 40% de ingresos
   - Nivel: ALTO
   - Frecuencia: Mensual

4. **Morosidad Recurrente**
   - Detección: 2+ cuotas vencidas en 6 meses
   - Nivel: ALTO
   - Frecuencia: Mensual

5. **Índice de Morosidad Alto**
   - Detección: Cartera global > 10%
   - Nivel: ALTO (>10%) o CRITICO (>20%)
   - Frecuencia: Diaria

**Características:**
- Generación automática diaria (00:15 AM)
- Niveles: BAJO, MEDIO, ALTO, CRITICO
- Estados: LEIDA, NO LEIDA
- Filtros múltiples
- Vista de detalle completa
- Gestión manual disponible

**Comando:**
```bash
php artisan alertas:generar
```

#### Sistema de Notificaciones

**3 Tipos de Notificaciones Automáticas:**

1. **Cuotas Próximas a Vencer**
   - Envío: 5 días antes (configurable)
   - Destinatario: Socios
   - Frecuencia: Una vez por cuota

2. **Cuotas Vencidas**
   - Envío: Cada 7 días
   - Destinatario: Socios
   - Incluye: Monto mora calculado

3. **Préstamos Aprobados**
   - Envío: Mismo día de aprobación
   - Destinatario: Socios
   - Incluye: Fecha de desembolso

**Canales:**
- SISTEMA: Notificaciones internas (activo)
- EMAIL: Envío por correo (simulado, configurable)
- SMS: Para implementación futura

**Características:**
- Generación automática 2x día (08:00, 18:00)
- Estados: LEIDA/NO LEIDA, ENVIADA/PENDIENTE
- Filtros por tipo, canal, estado
- Gestión manual disponible
- Parámetro para activar/desactivar emails

**Comando:**
```bash
php artisan notificaciones:enviar
```

### Sistema de Auditoría

**Observer Pattern:**
- Observer de Préstamos registra automáticamente:
  - Creación de préstamos
  - Actualizaciones importantes (estado, aprobación, saldo)
  - Eliminación

**Trait Auditable:**
- Reutilizable en cualquier modelo
- Uso: `use App\Traits\Auditable;`
- Registra automáticamente CRUD completo

**Datos Auditados:**
- Usuario que ejecutó la acción
- Tipo de acción (CREAR, ACTUALIZAR, ELIMINAR)
- Entidad y ID afectado
- Valores anteriores y nuevos (JSON)
- IP y User Agent
- Timestamp preciso

**Modelo:** `AuditoriaLog`
**Tabla:** `auditoria_logs`

### Sistema de Autenticación y Permisos

#### 4 Roles Predefinidos:

1. **Administrador**
   - Acceso total
   - Gestión de usuarios, roles, permisos
   - Configuración del sistema

2. **Gerente**
   - Operaciones completas
   - Aprobación de préstamos
   - Acceso a reportes BI
   - NO gestión de usuarios

3. **Cajero**
   - Operaciones básicas
   - Registro de pagos
   - Consultas
   - SIN aprobaciones ni reportes

4. **Auditor**
   - Solo lectura
   - Acceso a reportes
   - SIN modificación de datos

#### 24 Permisos en 8 Módulos:

**Socios:** ver, crear, editar, eliminar  
**Préstamos:** ver, crear, editar, eliminar, aprobar  
**Pagos:** ver, registrar  
**Ahorro:** (heredan de socios)  
**Reportes:** ver  
**Parámetros:** gestionar  
**Usuarios:** gestionar  
**Auditoría:** ver  

**Implementación:**
- Middleware: `CheckRole`, `CheckPermission`
- Helper: `hasPermission()`, `hasRole()`, `hasAnyRole()`
- Usa campo `slug` para verificación

### Parámetros Configurables

**12 Parámetros en 4 Grupos:**

1. **Mora** (3)
   - `dias_gracia_mora`: 3 días
   - `tasa_mora_mensual`: 2.5%
   - `mora_sobre_capital`: true/false

2. **Sistema** (4)
   - `nombre_cooperativa`
   - `ruc`
   - `email_soporte`
   - `telefono_soporte`

3. **Transacciones** (3)
   - `monto_minimo_ahorro`: $50
   - `saldo_minimo_cuenta`: $10
   - `monto_maximo_retiro`: $5000

4. **Notificaciones** (2)
   - `dias_anticipacion_notificacion`: 5
   - `enviar_email_notificaciones`: true/false

### Tipos de Préstamo Predefinidos

**5 Tipos Configurados:**

1. **Microcrédito**
   - Tasa: 18.5% anual
   - Monto: $500 - $5,000
   - Plazo: 6 - 24 meses
   - Garantía: No requerida

2. **Consumo**
   - Tasa: 16% anual
   - Monto: $1,000 - $10,000
   - Plazo: 12 - 36 meses
   - Garantía: No requerida

3. **Vivienda**
   - Tasa: 12% anual
   - Monto: $5,000 - $50,000
   - Plazo: 24 - 120 meses
   - Garantía: **Requerida**

4. **Emergencia**
   - Tasa: 20% anual
   - Monto: $200 - $2,000
   - Plazo: 3 - 12 meses
   - Garantía: No requerida

5. **Educación**
   - Tasa: 14% anual
   - Monto: $500 - $8,000
   - Plazo: 12 - 48 meses
   - Garantía: No requerida

---

## 🎨 Diseño y UX

### Plantilla: Tabler UI
- Framework: Bootstrap 5
- Componentes modernos
- Responsive design
- Iconos: Tabler Icons

### Color Corporativo
- Principal: `#0d2d5e` (azul oscuro)
- Degradado sidebar: `#0d2d5e` a `#0a1f44`

### Uso de Modales
**TODOS los formularios usan modales:**
- Roles: Modal estándar
- Usuarios: Modal estándar
- Socios: Modal XL (2 columnas)
- Parámetros: Modal estándar
- Préstamos: Modal con cálculo en tiempo real
- Garantías: Modal integrado en préstamo
- Cuentas Ahorro: Modal estándar
- Movimientos: Modal con panel lateral de info

**Ventajas:**
- Look más profesional
- No recarga página
- Mejor UX
- Validación en tiempo real

---

## ⏰ Tareas Programadas (Laravel Scheduler)

```php
// 00:01 AM - Detección de Mora
Schedule::command('mora:detectar')
    ->daily()->at('00:01')
    ->withoutOverlapping()->onOneServer();

// 00:15 AM - Generación de Alertas
Schedule::command('alertas:generar')
    ->daily()->at('00:15')
    ->withoutOverlapping()->onOneServer();

// 08:00 AM y 18:00 PM - Envío de Notificaciones
Schedule::command('notificaciones:enviar')
    ->dailyAt('08:00')
    ->withoutOverlapping()->onOneServer();

Schedule::command('notificaciones:enviar')
    ->dailyAt('18:00')
    ->withoutOverlapping()->onOneServer();

// 00:30 AM - Actualización Data Warehouse
Schedule::command('dw:actualizar-todo')
    ->daily()->at('00:30')
    ->withoutOverlapping()->onOneServer();
```

**Activar en Producción:**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📁 Estructura de Base de Datos

### Tablas Operacionales (17)
- `usuarios`, `roles`, `permisos`, `rol_permiso`
- `socios`, `tipos_prestamo`, `prestamos`, `cuotas`, `garantias`
- `cuenta_ahorros`, `movimiento_ahorros`
- `pagos`
- `parametros`
- `notificaciones`, `alerta_riesgos`
- `auditoria_logs`, `historial_cambios`

### Tablas Data Warehouse (4)
- `dimension_temporal`
- `hecho_cartera`
- `hecho_morosidad`
- `kpi_historicos`

### Tablas Adicionales (3)
- `polizas`, `detalle_polizas` (estructura básica)
- `reportes` (metadata de reportes)

---

## 🚀 Comandos Disponibles

### Operacionales
```bash
# Detectar mora manualmente
php artisan mora:detectar

# Generar alertas manualmente
php artisan alertas:generar

# Enviar notificaciones manualmente
php artisan notificaciones:enviar
```

### Data Warehouse
```bash
# Poblar dimensión temporal
php artisan dw:poblar-dimension-temporal [años]

# Actualizar snapshots individuales
php artisan dw:actualizar-cartera [--date=Y-m-d]
php artisan dw:actualizar-morosidad [--date=Y-m-d]
php artisan dw:actualizar-kpis [--date=Y-m-d]

# Actualizar todo el DW
php artisan dw:actualizar-todo [--date=Y-m-d]
```

### Mantenimiento
```bash
# Limpiar cache
php artisan optimize:clear

# Recargar datos de prueba
php artisan migrate:fresh --seed

# Ver tareas programadas
php artisan schedule:list

# Ejecutar scheduler manualmente
php artisan schedule:work
```

---

## ✅ Checklist de Implementación

### Desarrollo
- [x] Configurar entorno local
- [x] Instalar dependencias
- [x] Ejecutar migraciones y seeders
- [x] Compilar assets
- [x] Verificar autenticación
- [x] Probar módulos operacionales
- [x] Verificar dashboard BI
- [x] Probar comandos DW
- [x] Verificar alertas y notificaciones
- [x] Probar reportes

### Producción
- [ ] Configurar servidor (PHP 8.2, MySQL 8.0, Composer)
- [ ] Clonar repositorio
- [ ] Configurar `.env` (DB, APP_KEY, URL)
- [ ] Instalar dependencias (`composer install --optimize-autoloader --no-dev`)
- [ ] Ejecutar migraciones (`php artisan migrate --force`)
- [ ] Ejecutar seeders (`php artisan db:seed --force`)
- [ ] Compilar assets (`npm run build`)
- [ ] Configurar crontab para scheduler
- [ ] Configurar permisos de storage
- [ ] Configurar SSL
- [ ] Optimizar Laravel (`php artisan optimize`)
- [ ] Configurar backups automáticos

---

## 📖 Documentos Adicionales

- `README.md`: Guía de instalación y uso
- `IMPLEMENTACION.md`: Este documento (guía técnica)
- `CONFIGURACION_MORA.md`: Detalles del sistema de mora

---

## 🎓 Información Académica

**Proyecto de Tesis:** Sistema de Microfinanzas con Business Intelligence  
**Año:** 2026  
**Tecnología:** Laravel 11.x + Tabler UI  
**Base de Datos:** MySQL 8.0  
**Completitud:** 22/28 tareas (79%)  

**Funcionalidades Core:** ✅ 100%  
**Business Intelligence:** ✅ 100%  
**Alertas y Notificaciones:** ✅ 100%  
**Auditoría:** ✅ Implementada  
**Documentación:** ✅ Completa  

---

**Última Actualización:** Julio 2026
