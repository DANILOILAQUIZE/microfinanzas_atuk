# Sistema de Microfinanzas ATUK - Business Intelligence

Sistema integral de gestión de microfinanzas con enfoque en Business Intelligence, desarrollado con Laravel 11 y plantilla Tabler.

## 🎯 Características Principales

### Módulos Operacionales
- ✅ **Gestión de Socios**: Registro completo con datos personales y financieros
- ✅ **Cuentas de Ahorro**: Apertura, movimientos (depósitos/retiros), consultas
- ✅ **Préstamos**: Solicitud, aprobación/rechazo, desembolso, garantías
- ✅ **Pagos**: Registro de pagos, aplicación a cuotas, generación de recibos
- ✅ **Tipos de Préstamo**: Configuración de productos (tasas, plazos, montos)

### Módulos de Business Intelligence
- ✅ **Dashboard Ejecutivo**: KPIs en tiempo real, gráficos interactivos
- ✅ **Data Warehouse**: Snapshots diarios de cartera, morosidad y KPIs
- ✅ **Reportes Analíticos**:
  - Evolución de Cartera
  - Análisis de Morosidad
  - Rentabilidad y Pagos
  - KPIs Históricos
  - Análisis de Socios
- ✅ **Alertas de Riesgo**: Detección automática de situaciones críticas
- ✅ **Notificaciones**: Recordatorios de pagos, confirmaciones

### Módulos de Administración
- ✅ **Usuarios y Roles**: Sistema de permisos granular
- ✅ **Parámetros del Sistema**: Configuración flexible
- ✅ **Auditoría**: Registro de operaciones críticas

## 🚀 Tecnologías Utilizadas

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend**: Tabler UI (Bootstrap 5)
- **Base de Datos**: MySQL 8.0+
- **Gráficos**: Chart.js 4.4
- **Autenticación**: Laravel Fortify
- **Programación de Tareas**: Laravel Scheduler

## 📋 Requisitos del Sistema

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x
- NPM >= 9.x

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/microfinanzas-atuk.git
cd microfinanzas-atuk
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar base de datos
Editar `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=microfinanza_atuk
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

Esto creará:
- Estructura de base de datos completa
- 4 roles predefinidos (Administrador, Gerente, Cajero, Auditor)
- 24 permisos organizados en 8 módulos
- Usuario administrador por defecto
- 5 tipos de préstamo
- 12 parámetros del sistema
- Datos de ejemplo (5 socios, 4 préstamos, 3 cuentas de ahorro)

### 6. Compilar assets
```bash
npm run build
```

### 7. Iniciar servidor
```bash
php artisan serve
```

Accede a: `http://localhost:8000`

## 👤 Usuario por Defecto

**Email**: admin@atuk.com  
**Contraseña**: password

## ⏰ Tareas Programadas

El sistema ejecuta automáticamente las siguientes tareas:

```bash
# 00:01 AM - Detección de mora
php artisan mora:detectar

# 00:15 AM - Generación de alertas
php artisan alertas:generar

# 08:00 AM y 18:00 PM - Envío de notificaciones
php artisan notificaciones:enviar

# 00:30 AM - Actualización del Data Warehouse
php artisan dw:actualizar-todo
```

Para activar el scheduler en producción, agregar a crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Data Warehouse

El sistema incluye un Data Warehouse que almacena snapshots diarios:

### Tablas Fact (Hechos)
- `hecho_cartera`: Snapshots de cartera por fecha, tipo y socio
- `hecho_morosidad`: Snapshots de morosidad con rangos de días
- `kpi_historicos`: Todos los KPIs del sistema por fecha

### Tablas Dimension
- `dimension_temporal`: Dimensión de fechas (generada automáticamente)

### Comandos Manuales
```bash
# Poblar dimensión temporal (1 año atrás, 2 años adelante)
php artisan dw:poblar-dimension-temporal 2

# Actualizar snapshots manualmente
php artisan dw:actualizar-cartera
php artisan dw:actualizar-morosidad
php artisan dw:actualizar-kpis

# Actualizar todo el DW
php artisan dw:actualizar-todo
```

## 🔔 Sistema de Alertas

Tipos de alertas generadas automáticamente:

1. **Mora Temprana**: Cuotas próximas a vencer (5 días)
2. **Concentración de Crédito**: Socios con 3+ préstamos activos
3. **Capacidad de Pago**: Cuota > 40% de ingresos mensuales
4. **Morosidad Recurrente**: 2+ cuotas vencidas en 6 meses
5. **Índice de Morosidad Alto**: Cartera global > 10%

## 📧 Notificaciones

Tipos de notificaciones automáticas:

1. **Cuotas Próximas**: Recordatorio 5 días antes
2. **Cuotas Vencidas**: Recordatorio semanal
3. **Préstamos Aprobados**: Confirmación inmediata

## 🎨 Personalización

### Color Corporativo
El sistema usa el color azul oscuro `#0d2d5e`. Para cambiar:
- Editar `resources/views/layouts/app.blade.php`
- Buscar referencias a `#0d2d5e` y reemplazar

### Parámetros Configurables
Accede a **Administración > Parámetros** para configurar:
- Días de gracia para mora
- Tasa de mora mensual
- Base de cálculo de mora
- Montos mínimos/máximos
- Configuración de notificaciones

## 🔒 Roles y Permisos

### Roles Predefinidos

#### Administrador
- Acceso total al sistema
- Gestión de usuarios, roles y permisos
- Configuración de parámetros

#### Gerente
- Operaciones completas
- Aprobación de préstamos
- Acceso a reportes BI
- No puede gestionar usuarios

#### Cajero
- Operaciones básicas
- Registro de pagos
- Consulta de información
- Sin acceso a aprobaciones ni reportes

#### Auditor
- Solo lectura
- Acceso a reportes
- Sin modificación de datos

### Permisos Granulares (24 permisos)
Organizados en 8 módulos: Socios, Préstamos, Pagos, Ahorro, Reportes, Parámetros, Usuarios, Auditoría

## 📈 Reportes Disponibles

1. **Evolución de Cartera**
   - Cartera total, vigente y vencida histórica
   - Distribución por tipo de préstamo
   - Top 10 socios

2. **Análisis de Morosidad**
   - Evolución del índice
   - Rangos de mora (1-30, 31-60, 61-90, 90+ días)
   - Socios morosos

3. **Rentabilidad y Pagos**
   - Ingresos por concepto (capital, interés, mora)
   - Pagos mensuales
   - Tipos más rentables

4. **KPIs Históricos**
   - Evolución de 17 indicadores clave
   - Comparativas temporales

5. **Análisis de Socios**
   - Distribución por estado
   - Socios más activos
   - Crecimiento mensual

## 🛠️ Auditoría

El sistema registra automáticamente:
- Creación, modificación y eliminación de préstamos
- Usuario que realizó la acción
- Valores anteriores y nuevos
- IP y User Agent
- Fecha y hora exacta

Para agregar auditoría a otros modelos:
```php
use App\Traits\Auditable;

class MiModelo extends Model
{
    use Auditable;
    // ...
}
```

## 📖 Estructura del Proyecto

```
microfinanzas-atuk/
├── app/
│   ├── Console/Commands/      # Comandos artisan
│   ├── Http/Controllers/      # Controladores
│   ├── Models/                # Modelos Eloquent
│   ├── Observers/             # Observadores para auditoría
│   └── Traits/                # Traits reutilizables
├── database/
│   ├── migrations/            # Migraciones
│   └── seeders/               # Seeders
├── resources/
│   └── views/                 # Vistas Blade
├── routes/
│   ├── web.php               # Rutas web
│   └── console.php           # Tareas programadas
└── public/                    # Assets públicos
```

## 🐛 Solución de Problemas

### Error de migraciones
```bash
php artisan migrate:fresh --seed
```

### Assets no se cargan
```bash
npm run build
php artisan optimize:clear
```

### Tareas programadas no se ejecutan
```bash
# Verificar que el scheduler esté configurado
php artisan schedule:list

# Ejecutar manualmente
php artisan schedule:work
```

## 📝 Notas de Desarrollo

- Todos los formularios usan **modales** para un look profesional
- Los préstamos requieren **aprobación** antes de desembolso
- Las **cuotas** se generan automáticamente al aprobar
- El sistema usa **interesse simple anual**
- La **mora** se calcula automáticamente cada día
- El **Data Warehouse** se actualiza a medianoche

## 🤝 Contribuciones

Este es un proyecto de tesis. Para consultas contactar al autor.

## 📄 Licencia

Proyecto académico - Universidad [Nombre] - 2026

## 👨‍💻 Autor

Sistema desarrollado como proyecto de tesis para la carrera de Ingeniería en Sistemas.

---

**Versión**: 1.0.0  
**Fecha**: Julio 2026  
**Laravel**: 11.x  
**PHP**: 8.2+
