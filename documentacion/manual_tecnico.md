# Manual Técnico y de Desarrollador - SGCEA
### Sistema de Gestión y Control Escolar-Académico
**Arquitectura:** MVC Nativo en Vanilla PHP 8.x  
**Base de Datos:** MySQL / MariaDB (Engine InnoDB, Charset UTF8MB4)  
**Fecha de Documentación:** Agosto 2026  

---

## 🛠️ 1. Arquitectura General y Patrón de Diseño

El sistema **SGCEA** está construido siguiendo el patrón de diseño **Modelo-Vista-Controlador (MVC)** desacoplado en PHP orientada a objetos (POO), implementando los siguientes principios de ingeniería de software:
- **Clean Architecture & Separation of Concerns**: La lógica de negocio está aislada en servicios (`app/Models/Services/`), el acceso a datos en repositorios (`app/Models/Repositories/`), la orquestación HTTP en controladores (`app/Controllers/`) y la presentación en vistas PHP nativas (`app/Views/`).
- **Singleton Pattern**: Conexión centralizada a base de datos mediante `Src\Core\Database`.
- **Front Controller Pattern**: Punto de entrada único a través de `public/index.php` gestionado por `Src\Core\Router`.
- **Inyección de Variables de Entorno**: Carga dinámica mediante `Src\Core\Env` e integración con helper `env()`.

```
                  ┌─────────────────────────────────────────┐
                  │          Punto de Entrada               │
                  │          public/index.php               │
                  └────────────────────┬────────────────────┘
                                       │
                                       ▼
                  ┌─────────────────────────────────────────┐
                  │    Inicializador (config/inicializador) │
                  │  Carga .env, Autoloader, Sesiones, Core  │
                  └────────────────────┬────────────────────┘
                                       │
                                       ▼
                  ┌─────────────────────────────────────────┐
                  │          Router (core/Router.php)       │
                  │   Mapea URI + Método HTTP a Controller  │
                  └────────────────────┬────────────────────┘
                                       │
                                       ▼
                  ┌─────────────────────────────────────────┐
                  │      Controladores (app/Controllers/)   │
                  │ Valida permisos ACL, sanitiza entrada   │
                  └──────────┬───────────────────┬──────────┘
                             │                   │
                             ▼                   ▼
           ┌──────────────────────────┐  ┌──────────────────────────┐
           │ Servicios / Repositorios │  │  Vistas (app/Views/)     │
           │    (app/Models/)         │  │   Renderizado HTML       │
           └─────────────┬────────────┘  └──────────────────────────┘
                         │
                         ▼
           ┌──────────────────────────┐
           │ Base de Datos PDO        │
           │ (core/Database.php)      │
           └──────────────────────────┘
```

---

## 💻 2. Componentes del Núcleo (`core/`)

Los componentes del directorio `core/` proveen la infraestructura básica sobre la cual opera la aplicación:

### 2.1 `Autoloader.php`
- **Namespace**: `Src\Core\Autoloader`
- **Función**: Registra una función con `spl_autoload_register` para cargar automáticamente clases bajo el namespace de raíz `Src\`.
- **Mapeo**:
  - `Src\Core\*` $\rightarrow$ `core/*`
  - `Src\Controllers\*` $\rightarrow$ `app/Controllers/*`
  - `Src\Models\*` $\rightarrow$ `app/Models/*`

### 2.2 `Env.php`
- **Namespace**: `Src\Core\Env`
- **Función**: Parsea el archivo `.env` en la raíz del proyecto.
- **Detalles**:
  - Lee variables key=value.
  - Registra las claves en `$_ENV`, `$_SERVER` y mediante `putenv()`.
  - Soporta valores booleanos (`true`/`false`), `null` y comillas.

### 2.3 `Database.php`
- **Namespace**: `Src\Core\Database`
- **Patrón**: Singleton.
- **Función**: Administra la conexión nativa a la base de datos a través de `PDO`.
- **Configuración**: Lee de `config/database.php` (el cual consume `env()`).
- **Seguridad**: Desactiva emulación de prepared statements (`PDO::ATTR_EMULATE_PREPARES => false`) e impone `PDO::ERRMODE_EXCEPTION`.

### 2.4 `Router.php`
- **Namespace**: `Src\Core\Router`
- **Función**: Enrutador adaptativo con soporte para comodines como `{id}`.
- **Detalles**:
  - Registra rutas para los métodos HTTP `GET` y `POST`.
  - Normaliza la URI eliminando prefijos de instalación local como `/sgcea` o `/sgcea/public`.
  - Invoca dinámicamente métodos de controladores mediante `ReflectionMethod`.

### 2.5 `Controller.php`
- **Namespace**: `Src\Core\Controller`
- **Función**: Clase base abstracta que heredan todos los controladores.
- **Métodos Clave**:
  - `render($view, $data, $raw)`: Procesa y compila vistas PHP envolviéndolas en el layout correspondiente (`header.php`, `footer.php`).
  - `redirigir($path)`: Ejecuta redirección HTTP enviando encabezado `Location`.
  - `json($data, $code)`: Retorna respuestas serializadas en JSON para llamadas AJAX.
  - `verificarPermiso($permiso)`: Valida la matriz ACL del usuario autenticado en sesión.

### 2.6 `Security.php`
- **Namespace**: `Src\Core\Security`
- **Función**: Proveedor de capas defensivas contra vulnerabilidades web.
- **Métodos Clave**:
  - `e(?string)`: Sanitización contra Cross-Site Scripting (XSS) usando `htmlspecialchars`.
  - `generarTokenCSRF()` / `validarTokenCSRF()`: Protección contra Cross-Site Request Forgery (CSRF).
  - `hashPassword()` / `verificarPassword()`: Cifrado seguro de contraseñas mediante `password_hash` (`PASSWORD_BCRYPT`).

### 2.7 `RateLimiter.php`
- **Namespace**: `Src\Core\RateLimiter`
- **Función**: Limita los intentos fallidos de inicio de sesión por IP en la tabla `intentos_login` para mitigar ataques de fuerza bruta.

### 2.8 `helpers.php`
- **Función**: Funciones globales de conveniencia disponibles en todo el proyecto:
  - `env($key, $default)`: Wrapper para `Env::get()`.
  - `e($str)`: Alias corto para sanitización HTML.
  - `url($path)` / `asset($path)`: Generación de rutas absolutas dinámicas.
  - `set_flash($type, $msg)` / `get_flash($type)`: Manejo de mensajes flash de sesión.

---

## 🎮 3. Controladores (`app/Controllers/`)

Los controladores coordinan la entrada del usuario, ejecutan la validación y comunican el resultado a las vistas.

| Controlador | Descripción y Funciones Principales |
| :--- | :--- |
| `AdminController.php` | Administra la vista global del dashboard, gestión de usuarios, roles, catálogo de materias, secciones, asignaciones de profesores y revisión de solicitudes de constancias. |
| `DocenteController.php` | Controla la experiencia pedagógica del profesor: dashboard docente, lista de asignaciones, registro masivo de asistencias, gestión del plan de evaluación y carga de calificaciones. |
| `EstudianteController.php` | Administra el área del alumno: consulta de boletín de notas, reporte de inasistencias, solicitudes de constancias y edición del perfil personal. |
| `ConstanciaController.php` | Gestiona el ciclo de vida y la generación del contenido HTML/PDF imprimible de constancias de estudio, conducta y notas. |
| `AuthController.php` | Maneja el ciclo de autenticación: login con control de intentos, logout y registro de auditoría de acceso. |
| `ConfiguracionController.php` | Gestiona los parámetros institucionales del sistema y permite reiniciar los datos (Zona de Peligro). |
| `BackupController.php` | Permite realizar volcados completos del sistema en formato JSON/SQL e importar respaldos. |

---

## 🏛️ 4. Modelos: Servicios y Repositorios (`app/Models/`)

La capa de datos está dividida en dos subcapas:

### 4.1 Repositorios (`app/Models/Repositories/`)
Interactúan directamente con la base de datos mediante sentencias SQL preparadas con PDO:
- `UsuarioRepository.php`: CRUD de usuarios y consultas por email/cédula.
- `EstudianteRepository.php`: Consultas avanzadas de estudiantes con sus relaciones.
- `DocenteRepository.php`: Gestión de profesores y especialidades.
- `ConstanciaRepository.php`: Persistencia de solicitudes de constancias.
- `InscripcionRepository.php`: Control de estudiantes inscritos por sección.
- `AsignacionRepository.php`: Relación entre profesor, materia y sección.
- `AsistenciaRepository.php`: Registro e historial de asistencia.
- `CalificacionRepository.php`: Almacenamiento de notas por plan de evaluación.
- `MateriaRepository.php`, `GradoRepository.php`, `SeccionRepository.php`, `PermisoRepository.php`, `InstitucionRepository.php`.

### 4.2 Servicios (`app/Models/Services/`)
Contienen la lógica de negocio pura, validaciones de reglas y registro de auditoría:
- `AuditoriaService.php`: Método `registrar($usuarioId, $accion, $tabla, $registroId, $detalles)` con soporte flexible para arreglos, cadenas o valores nulos.
- `UsuarioService.php`: Reglas de negocio para creación/edición de usuarios y gestión de claves.
- `EstudianteService.php`: Lógica de estudiantes e inscripciones.
- `DocenteService.php`: Validaciones y asignación pedagógica.
- `ConstanciaService.php`: Control del límite de solicitudes (máx. 5 pendientes por estudiante) y aprobaciones.
- `AsistenciaService.php`, `CalificacionService.php`, `PlanEvaluacionService.php`, `MateriaService.php`, `GradoService.php`, `SeccionService.php`, `PermisoService.php`, `InstitucionService.php`.

---

## 🗄️ 5. Esquema de Base de Datos y Sistema de Permisos ACL

### 5.1 Lista de Tablas del Sistema (21 Tablas)

1. `instituciones`: Datos institucionales del plantel educativo.
2. `permisos`: Catálogo de 48 permisos ACL organizados por módulos (`admin`, `docente`, `estudiante`, `reportes`).
3. `usuarios`: Tabla principal de autenticación (`cedula`, `email`, `password`, `tipo`).
4. `usuario_permisos`: Tabla intermedia para la matriz de permisos de cada usuario.
5. `profesores`: Datos extendidos del perfil docente.
6. `estudiantes`: Datos extendidos del perfil estudiante y representante.
7. `grados`: Niveles académicos (1er Grado a 6to Grado, 1er Año a 5to Año).
8. `secciones`: Secciones por grado y año escolar (ej: A, B, C).
9. `materias`: Catálogo de asignaturas con código y créditos.
10. `inscripciones`: Asignación del estudiante a una sección para un año académico.
11. `asignaciones`: Cátedra asignada a un docente (Profesor + Materia + Sección + Año).
12. `planes_evaluacion`: Actividades de evaluación programadas por el docente por lapso.
13. `calificaciones`: Notas individuales obtenidas por estudiante en cada plan.
14. `asistencias`: Control diario de asistencia (`presente`, `ausente`, `tarde`, `justificado`).
15. `solicitudes_constancia`: Solicitudes tramitadas por estudiantes y resueltas por admins.
16. `configuraciones`: Clave-valor para la configuración global.
17. `auditoria`: Registro detallado de acciones del sistema con detalles en JSON.
18. `notificaciones`: Mensajería interna para usuarios.
19. `intentos_login`: Registro de IPs y fechas para control de Rate Limiting.
20. `cache_dashboard_admin`: Almacenamiento en caché de métricas administrativas.
21. `cache_dashboard_docente`: Caché de estadísticas para docentes.

---

## 🛡️ 6. Seguridad y Buenas Prácticas

1. **Variables de Entorno**: Ninguna credencial sensible debe alojarse en código fuente. Todo lee desde `.env`.
2. **Prepared Statements**: Todas las consultas SQL utilizan marcadores de parámetros (`?` o `:nombre`) previniendo Inyección SQL.
3. **Escapado XSS**: Todos los datos renderizados en vistas pasan por `e()` / `htmlspecialchars()`.
4. **Protección CSRF**: Formularios mutation (POST) incluyen token anti-CSRF validado por `Security::validarTokenCSRF()`.
5. **Cifrado de Claves**: BCRYPT con costo 10 nativo mediante `password_hash()`.
