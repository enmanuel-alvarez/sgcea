# Manual Técnico, Arquitectura e Guía del Desarrollador - SGCEA
### Sistema de Gestión y Control Escolar-Académico (SGCEA)
**Versión:** 3.0.0 (Tailwind CSS v3 + PHP 8.x Clean Architecture)  
**Motor de Base de Datos:** MySQL / MariaDB (Engine InnoDB, UTF8MB4)  
**Última Actualización:** Agosto 2026  

---

## 📌 Índice de Contenidos

1. [📐 Arquitectura General del Sistema (MVC + POO)](#1-arquitectura-general-del-sistema-mvc--poo)
   - [1.1 Flujo de Ejecución e Hilo de Solicitud HTTP](#11-flujo-de-ejecución-e-hilo-de-solicitud-http)
   - [1.2 Desacoplamiento en Capas (Clean Architecture)](#12-desacoplamiento-en-capas-clean-architecture)
2. [⚙️ Componentes del Núcleo (`core/`)](#2-componentes-del-núcleo-core)
   - [2.1 Autoloader (`core/Autoloader.php`)](#21-autoloader-coreautoloaderphp)
   - [2.2 Gestor de Entorno (`core/Env.php`)](#22-gestor-de-entorno-coreenvphp)
   - [2.3 Conexión Singleton PDO (`core/Database.php`)](#23-conexión-singleton-pdo-coredatabasephp)
   - [2.4 Enrutador Dinámico (`core/Router.php`)](#24-enrutador-dinámico-corerouterphp)
   - [2.5 Controlador Base Abstracto (`core/Controller.php`)](#25-controlador-base-abstracto-corecontrollerphp)
   - [2.6 Escudo de Seguridad (`core/Security.php`)](#26-escudo-de-seguridad-coresecurityphp)
   - [2.7 Control de Fuerza Bruta (`core/RateLimiter.php`)](#27-control-de-fuerza-bruta-coreratelimiterphp)
   - [2.8 Funciones Globales de Conveniencia (`core/helpers.php`)](#28-funciones-globales-de-conveniencia-corehelpersphp)
3. [🎮 Capa de Controladores (`app/Controllers/`)](#3-capa-de-controladores-appcontrollers)
   - [3.1 AuthController](#31-authcontroller)
   - [3.2 AdminController](#32-admincontroller)
   - [3.3 DocenteController](#33-docentecontroller)
   - [3.4 EstudianteController](#34-estudiantecontroller)
   - [3.5 ReportesController](#35-reportescontroller)
   - [3.6 ConstanciaController](#36-constanciacontroller)
   - [3.7 CarnetController](#37-carnetcontroller)
   - [3.8 BackupController](#38-backupcontroller)
   - [3.9 ConfiguracionController](#39-configuracioncontroller)
4. [🏛️ Capa de Modelos: Servicios y Repositorios (`app/Models/`)](#4-capa-de-modelos-servicios-y-repositorios-appmodels)
   - [4.1 Servicios de Lógica de Negocio (`app/Models/Services/`)](#41-servicios-de-lógica-de-negocio-appmodelsservices)
   - [4.2 Repositorios de Persistencia (`app/Models/Repositories/`)](#42-repositorios-de-persistencia-appmodelsrepositories)
5. [📋 Cumplimiento de Reglas de Negocio del Sistema](#5-cumplimiento-de-reglas-de-negocio-del-sistema)
   - [RN-01: Plan de Evaluación Obligatorio del 100%](#rn-01-plan-de-evaluación-obligatorio-del-100)
   - [RN-02: Límite de Solicitudes Activas de Constancias](#rn-02-límite-de-solicitudes-activas-de-constancias)
   - [RN-03: Impresión Oficial Landscape sin Bordes ni Cuadros](#rn-03-impresión-oficial-landscape-sin-bordes-ni-cuadros)
   - [RN-04: Modales Unificados de Confirmación de Acción](#rn-04-modales-unificados-de-confirmación-de-acción)
   - [RN-05: Zona de Peligro (Danger Zone Reset) Preservando Admin y Permisos](#rn-05-zona-de-peligro-danger-zone-reset-preservando-admin-y-permisos)
   - [RN-06: Matriz de Roles y Permisos Granulares ACL (Admin, Docente, Estudiante, Custom)](#rn-06-matriz-de-roles-y-permisos-granulares-acl-admin-docente-estudiante-custom)
   - [RN-07: Métricas en Tiempo Real y Filtro del Dashboard](#rn-07-métricas-en-tiempo-real-y-filtro-del-dashboard)
   - [RN-08: Carnets Imprimibles para Docentes y Estudiantes](#rn-08-carnets-imprimibles-para-docentes-y-estudiantes)
6. [🗄️ Esquema Completo de la Base de Datos (21 Tablas)](#6-esquema-completo-de-la-base-de-datos-21-tablas)
7. [🛡️ Protocolos de Seguridad y Despliegue en Producción](#7-protocolos-de-seguridad-y-despliegue-en-producción)

---

## 📐 1. Arquitectura General del Sistema (MVC + POO)

El sistema **SGCEA** está diseñado bajo los principios de la **Arquitectura Limpia (Clean Architecture)** y el patrón **MVC (Modelo-Vista-Controlador)** desacoplado en **PHP 8.x Orientado a Objetos (POO)** puro, sin frameworks pesados, garantizando un rendimiento óptimo de respuesta (menos de 50ms por petición).

### 1.1 Flujo de Ejecución e Hilo de Solicitud HTTP

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

### 1.2 Desacoplamiento en Capas (Clean Architecture)

1. **Capa Core (`core/`)**: Infraestructura del framework interno (Autoloader, Router, PDO Singleton, Security, RateLimiter).
2. **Capa Controller (`app/Controllers/`)**: Orquestación de peticiones HTTP, validación de Tokens CSRF, verificación de permisos ACL y preparación del contexto para las Vistas.
3. **Capa Service (`app/Models/Services/`)**: Lógica de negocio pura, cálculo de promedios, validaciones de ponderación, reglas de negocio e invocación a auditoría.
4. **Capa Repository (`app/Models/Repositories/`)**: Capa de persistencia. Contiene consultas SQL preparadas (`PDO`) desacopladas de la lógica de negocio.
5. **Capa View (`app/Views/`)**: Interfaz de usuario estilizada con **Tailwind CSS v3**, layouts unificados (`header.php`, `sidebar.php`, `footer.php`) y modales reactivos.

---

## ⚙️ 2. Componentes del Núcleo (`core/`)

### 2.1 Autoloader (`core/Autoloader.php`)
- **Namespace**: `Src\Core\Autoloader`
- **Propósito**: Implementa carga automática de clases en cumplimiento de la norma PSR-4.
- **Funcionamiento**: Registra mediante `spl_autoload_register` la conversión de namespaces a rutas del sistema de archivos:
  - `Src\Core\*` $\rightarrow$ `core/*`
  - `Src\Controllers\*` $\rightarrow$ `app/Controllers/*`
  - `Src\Models\*` $\rightarrow$ `app/Models/*`

### 2.2 Gestor de Entorno (`core/Env.php`)
- **Namespace**: `Src\Core\Env`
- **Propósito**: Parsea y carga las variables del archivo `.env` en la raíz del servidor MAMP/producción.
- **Funcionamiento**: Lee parejas `CLAVE=VALOR`, omite comentarios `#`, convierte tipos primitivos (`true`, `false`, `null`) y las inyecta en `$_ENV`, `$_SERVER` y mediante `putenv()`.

### 2.3 Conexión Singleton PDO (`core/Database.php`)
- **Namespace**: `Src\Core\Database`
- **Patrón**: Singleton (Instancia única de conexión).
- **Propósito**: Provee acceso centralizado y seguro a MySQL/MariaDB a través de `PDO`.
- **Configuración de Seguridad**:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`: Dispara excepciones en errores SQL.
  - `PDO::ATTR_EMULATE_PREPARES => false`: Forzar consultas preparadas reales en el motor de la base de datos para prevenir inyecciones SQL.
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`: Retorna arrays asociativos por defecto.

### 2.4 Enrutador Dinámico (`core/Router.php`)
- **Namespace**: `Src\Core\Router`
- **Propósito**: Enrutamiento dinámico de URLs con soporte de parámetros dinámicos (`{id}`).
- **Funcionamiento**:
  - Registra rutas mapeando método HTTP (`GET`, `POST`) y expresión URI hacia una acción `Controlador@metodo`.
  - Normaliza la URI eliminando prefijos de instalación en subdirectorios (ej. `/sgcea/public`).
  - Utiliza `ReflectionMethod` para instanciar el controlador e inyectar los argumentos extraídos de la URL.

### 2.5 Controlador Base Abstracto (`core/Controller.php`)
- **Namespace**: `Src\Core\Controller`
- **Propósito**: Proveer helpers de renderizado y flujo a todos los controladores.
- **Métodos Clave**:
  - `render(string $view, array $data, bool $raw)`: Compila la vista solicitada inyectando variables en el buffer de salida (`ob_start()`) e integrando el diseño global (`header.php`, `sidebar.php`, `footer.php`).
  - `redirigir(string $url)`: Ejecuta una redirección `Header("Location: ...")` finalizando la ejecución.
  - `json(mixed $data, int $statusCode)`: Emite respuestas JSON con el encabezado `Content-Type: application/json`.
  - `verificarPermiso(string $permiso)`: Valida la sesión activa y comprueba si el usuario posee el permiso en `$_SESSION['usuario_permisos']`.

### 2.6 Escudo de Seguridad (`core/Security.php`)
- **Namespace**: `Src\Core\Security`
- **Propósito**: Prevención de vulnerabilidades web (XSS, CSRF, Password Hashing).
- **Métodos Clave**:
  - `e(?string $val)`: Sanitización XSS mediante `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
  - `generarTokenCSRF()`: Crea un token binario aleatorio (`random_bytes(32)`) almacenado en sesión.
  - `validarTokenCSRF(?string $token)`: Compara el token enviado en formularios mediante `hash_equals()` resistente a ataques de tiempo.
  - `hashPassword(string $password)`: Genera hashes de clave con `PASSWORD_BCRYPT` y costo 10.
  - `verificarPassword(string $pass, string $hash)`: Valida la contraseña enviada contra el hash persistido en la BD.

### 2.7 Control de Fuerza Bruta (`core/RateLimiter.php`)
- **Namespace**: `Src\Core\RateLimiter`
- **Propósito**: Bloqueo temporal de inicios de sesión tras múltiples intentos fallidos.
- **Funcionamiento**: Registra la dirección IP y la marca de tiempo en `intentos_login`. Si supera los 5 intentos fallidos en menos de 15 minutos, bloquea el acceso enviando un mensaje de advertencia.

### 2.8 Funciones Globales de Conveniencia (`core/helpers.php`)
- `env($key, $default)`: Wrapper global para obtener variables de entorno.
- `e($str)`: Alias rápido de sanitización contra ataques XSS.
- `url($path)`: Genera URLs absolutas respetando el dominio y subdirectorio base de la instalación.
- `asset($path)`: Construye la ruta hacia recursos estáticos (`/public/assets/...`).
- `csrf_field()`: Renderiza el campo oculto `<input type="hidden" name="csrf_token" value="...">`.

---

## 🎮 3. Capa de Controladores (`app/Controllers/`)

### 3.1 `AuthController.php`
- **Rutas**: `/login` (GET/POST), `/logout` (GET).
- **Funcionalidad**: Gestiona el inicio y cierre de sesión de usuarios. Invoca `RateLimiter` para verificar la IP, valida las credenciales a través de `UsuarioService`, carga los permisos del usuario en `$_SESSION['usuario_permisos']` y registra el acceso en `AuditoriaService`.

### 3.2 `AdminController.php`
- **Rutas**: `/admin` (GET), `/admin/usuarios` (GET/POST), `/admin/estudiantes` (GET/POST), `/admin/docentes` (GET/POST), `/admin/materias`, `/admin/secciones`, `/admin/asignaciones`, `/admin/constancias`.
- **Funcionalidad**: Controlador maestro de administración escolar:
  - `index()`: Procesa métricas reales y filtros del Dashboard en vivo (Año Lectivo y Grado).
  - `listarUsuarios()`, `guardarUsuario()`, `eliminarUsuario()`: Administración de la matriz de usuarios y asignación de permisos individuales.
  - `estudiantes()`, `docentes()`, `materias()`, `secciones()`, `asignaciones()`: Módulos CRUD con carga de fotos de perfil e integración de modales.

### 3.3 `DocenteController.php`
- **Rutas**: `/docente/dashboard`, `/docente/calificaciones`, `/docente/plan-evaluacion/{id}`, `/docente/asistencias`.
- **Funcionalidad**:
  - `planEvaluacion(int $idAsignacion)`: Visualiza las actividades del plan de evaluación, estado del porcentaje acumulado y valida que sume exactamente el 100%.
  - `crearActividad()`, `crearActividadesLote()`: Registra evaluaciones individuales o masivas validando estrictamente que el acumulado no supere el 100%.
  - `eliminarActividad(int $idPlan)`: Elimina actividades evaluativas mediante verificación de pertenencia por `obtenerProfesorId()` y modal de confirmación, redirigiendo de vuelta a `/docente/plan-evaluacion/{id}`.
  - `guardarCalificaciones()`: Guarda notas masivas únicamente si el plan suma el 100% obligatorio.

### 3.4 `EstudianteController.php`
- **Rutas**: `/estudiante/dashboard`, `/estudiante/notas`, `/estudiante/asistencias`, `/estudiante/constancias`, `/estudiante/perfil`.
- **Funcionalidad**:
  - `boletin()`: Renderiza el historial de calificaciones por período.
  - `constancias()`: Muestra la tabla de constancias solicitadas y despliega el modal para nuevas solicitudes (validando la regla de 1 solicitud activa por tipo).
  - `perfil()`: Visualiza y actualiza la información personal, foto de perfil y carnet digital.

### 3.5 `ReportesController.php`
- **Rutas**: `/reportes`, `/reportes/exportar/{tipo}`.
- **Funcionalidad**: Centro de análisis académico con visualización e impresión optimizada:
  - Genera informes de Cuadro de Honor, Riesgo Académico, Sabana por Sección, Ausentismo Crítico y Carga Horaria Docente.
  - Exportación a CSV/Excel y maquetación para impresión oficial física/PDF en orientación horizontal (Landscape) sin bordes de tarjetas.

### 3.6 `ConstanciaController.php`
- **Rutas**: `/estudiante/constancias/solicitar`, `/admin/constancias/procesar`.
- **Funcionalidad**:
  - Validar el límite de solicitudes activas antes de crear la petición.
  - Cambiar estado a `'aprobada'` o `'rechazada'` con observaciones.
  - Generar el documento oficial imprimible con membrete y código de validación.

### 3.7 `CarnetController.php`
- **Rutas**: `/carnet/estudiante/{id}`, `/carnet/docente/{id}`.
- **Funcionalidad**: Genera la vista e imprime el Carnet Institucional Inteligente en formato de credencial estándar (frente y reverso) con foto de perfil, código QR de verificación y datos del plantel.

### 3.8 `BackupController.php`
- **Rutas**: `/admin/backup`, `/admin/backup/exportar`, `/admin/backup/importar`.
- **Funcionalidad**: Exportación e importación de la base de datos en formato JSON formateado.

### 3.9 `ConfiguracionController.php`
- **Rutas**: `/admin/configuracion`, `/admin/configuracion/reiniciar`.
- **Funcionalidad**:
  - `reiniciarSistema()`: Ejecuta la Zona de Peligro (Danger Zone Reset). Trunca las 16 tablas de datos operativos, elimina cuentas de estudiantes/docentes, pero **preserva intactos a todos los usuarios administradores y la matriz de tablas de permisos de la base de datos**.

---

## 🏛️ 4. Capa de Modelos: Servicios y Repositorios (`app/Models/`)

### 4.1 Servicios de Lógica de Negocio (`app/Models/Services/`)

- **`PlanEvaluacionService.php`**: Controla el ciclo de vida del plan de evaluación. Valida que la suma de ponderaciones no exceda el 100% e impide el guardado si está incompleto.
- **`CalificacionService.php`**: Calcula los promedios ponderados por materia y lapso, valida notas en escala 1-20 y bloquea la edición si la asignación está cerrada.
- **`ConstanciaService.php`**: Valida que un estudiante solo pueda mantener 1 solicitud activa (`'pendiente'`) por cada tipo de constancia.
- **`DashboardService.php`**: Ejecuta las consultas en vivo contra la base de datos para obtener métricas reales de matrícula, personal, materias, asistencias y constancias, soportando filtrado por Año Lectivo y Grado.
- **`AuditoriaService.php`**: Escribe en la tabla `auditoria` la trazabilidad de eventos críticos (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, `REINICIAR_SISTEMA`) almacenando detalles contextuales.
- **`UsuarioService.php`**, **`EstudianteService.php`**, **`DocenteService.php`**, **`AsistenciaService.php`**, **`MateriaService.php`**, **`SeccionService.php`**, **`GradoService.php`**, **`PermisoService.php`**.

### 4.2 Repositorios de Persistencia (`app/Models/Repositories/`)

- **`UsuarioRepository.php`**: Consultas SQL de usuarios y unión con la cuenta de permisos individuales (`COUNT(up.permiso_id)`).
- **`EstudianteRepository.php`**: Consultas SQL con `INNER JOIN` hacia `usuarios`, `inscripciones`, `secciones` y `grados`.
- **`DocenteRepository.php`**: Gestión de profesores y relación con asignaciones.
- **`PlanEvaluacionRepository.php`**: Operaciones CRUD sobre la tabla `planes_evaluacion`.
- **`CalificacionRepository.php`**: Consultas de notas agrupadas por estudiante y asignación.
- **`AsistenciaRepository.php`**: Registro de inasistencias y balances estadísticos.
- **`ConstanciaRepository.php`**: Persistencia y conteo por estado de solicitudes.
- **`AsignacionRepository.php`**, **`InscripcionRepository.php`**, **`MateriaRepository.php`**, **`SeccionRepository.php`**, **`GradoRepository.php`**, **`PermisoRepository.php`**, **`DashboardCacheRepository.php`**.

---

## 📋 5. Cumplimiento de Reglas de Negocio del Sistema

| Regla de Negocio | Implementación en Código | Archivos / Métodos Responsables |
| :--- | :--- | :--- |
| **RN-01: Plan de Evaluación Obligatorio del 100%** | La ponderación total de las evaluaciones debe sumar exactamente el 100%. No se pueden registrar calificaciones masivas si el plan suma diferente de 100%. | [DocenteController.php](file:///Applications/MAMP/htdocs/sgcea/app/Controllers/DocenteController.php#L175) (`guardarCalificaciones`), [PlanEvaluacionService.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Services/PlanEvaluacionService.php) |
| **RN-02: Límite de Solicitudes Activas de Constancias** | Cada estudiante puede tener como máximo 1 solicitud activa (en estado `'pendiente'`) por cada tipo de constancia a la vez. | [ConstanciaService.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Services/ConstanciaService.php), [ConstanciaRepository.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Repositories/ConstanciaRepository.php#L40) |
| **RN-03: Impresión Oficial Landscape sin Bordes ni Cuadros** | Todos los reportes e informes impresos se maquetan obligatoriamente en orientación horizontal (`@page { size: landscape; }`) eliminando tarjetas, bordes, sombras y recuadros contenedores. | [print.css](file:///Applications/MAMP/htdocs/sgcea/public/assets/css/print.css#L7), [reportes/index.php](file:///Applications/MAMP/htdocs/sgcea/app/Views/reportes/index.php) |
| **RN-04: Modales Unificados de Confirmación de Acción** | Ninguna acción destructiva o crítica utiliza popups `confirm()` nativos del navegador; todas utilizan modales flotantes Tailwind CSS (`#modalConfirmar...`). | [planevaluacion.php](file:///Applications/MAMP/htdocs/sgcea/app/Views/docente/planevaluacion.php#L155), [usuarios/index.php](file:///Applications/MAMP/htdocs/sgcea/app/Views/admin/usuarios/index.php#L530) |
| **RN-05: Danger Zone Reset Preservando Admin y Permisos** | El botón de la Zona de Peligro vacía toda la data operacional (16 tablas), pero mantiene intactos a los usuarios administradores (`tipo_usuario = 'admin'`) y las tablas de permisos de la BD. | [ConfiguracionController.php](file:///Applications/MAMP/htdocs/sgcea/app/Controllers/ConfiguracionController.php#L131) |
| **RN-06: Modelo RBAC Híbrido (Roles Principales + Excepciones Directas: CONCEDER / REVOCAR)** | Clasificación basada en 3 roles principales (`Administrador`, `Docente`, `Estudiante`). Los usuarios heredan automáticamente la matriz de permisos de su rol principal. El administrador puede definir excepciones individuales concediendo (`+ CONCEDER`) permisos extra o revocando (`- REVOCAR`) accesos específicos. | [usuarios/index.php](file:///Applications/MAMP/htdocs/sgcea/app/Views/admin/usuarios/index.php), [PermisoService.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Services/PermisoService.php), [PermisoRepository.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Repositories/PermisoRepository.php) |
| **RN-07: Métricas en Tiempo Real y Filtro del Dashboard** | El Dashboard calcula métricas 100% reales directamente desde la base de datos (mostrando 0 si la BD está limpia) e incluye una barra de filtros dinámicos por Año Lectivo y Grado. | [DashboardService.php](file:///Applications/MAMP/htdocs/sgcea/app/Models/Services/DashboardService.php#L42), [admin/dashboard.php](file:///Applications/MAMP/htdocs/sgcea/app/Views/admin/dashboard.php#L38) |
| **RN-08: Carnets Imprimibles para Docentes y Estudiantes** | Tanto docentes como estudiantes pueden imprimir su carnet digital desde su perfil y el administrador desde las acciones del módulo. | [CarnetController.php](file:///Applications/MAMP/htdocs/sgcea/app/Controllers/CarnetController.php), `carnet_modal.php` |

---

## 🗄️ 6. Esquema Completo de la Base de Datos (23 Tablas)

1. **`instituciones`**: Configuración del plantel (`nombre`, `codigo_dea`, `logo`, `direccion`).
2. **`roles`**: Catálogo de roles principales (`id`, `nombre`, `slug`, `descripcion`, `created_at`).
3. **`permisos`**: Catálogo de permisos granulares por módulo y vista (`id`, `nombre`, `descripcion`, `modulo`).
4. **`rol_permiso`**: Matriz de permisos heredados por defecto por cada rol (`rol_id`, `permiso_id`).
5. **`usuarios`**: Cuentas de acceso con referencia de rol (`id`, `rol_id`, `cedula`, `nombre`, `apellido`, `email`, `password`, `tipo`, `estado`, `created_at`).
6. **`usuario_permisos`**: Excepciones de permisos directas por usuario (`id`, `usuario_id`, `permiso_id`, `tipo`, `created_at`), donde `tipo` es ENUM (`'CONCEDER'`, `'REVOCAR'`).
7. **`profesores`**: Perfil docente (`id`, `usuario_id`, `especialidad`, `telefono`, `foto`).
8. **`estudiantes`**: Perfil estudiante (`id`, `usuario_id`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `representante`, `foto`).
9. **`grados`**: Niveles escolares (`id`, `nombre`, `nivel_educativo`).
10. **`secciones`**: Aulas por grado (`id`, `grado_id`, `nombre`, `capacidad`).
11. **`materias`**: Asignaturas (`id`, `codigo`, `nombre`, `horas_semanales`).
12. **`inscripciones`**: Matrícula activa (`id`, `estudiante_id`, `seccion_id`, `ano_academico`, `estado`, `fecha_inscripcion`).
13. **`asignaciones`**: Cátedras docentes (`id`, `profesor_id`, `materia_id`, `seccion_id`, `ano_academico`).
14. **`planes_evaluacion`**: Actividades por lapso (`id`, `asignacion_id`, `nombre`, `tipo`, `ponderacion`, `fecha_programada`).
15. **`calificaciones`**: Evaluaciones individuales (`id`, `plan_evaluacion_id`, `estudiante_id`, `nota`, `observacion`).
16. **`asistencias`**: Control diario (`id`, `asignacion_id`, `estudiante_id`, `fecha`, `estado`).
17. **`solicitudes_constancia`**: Trámites de documentos (`id`, `estudiante_id`, `tipo_constancia`, `estado`, `fecha_solicitud`).
18. **`configuraciones`**: Parámetros globales en clave-valor.
19. **`auditoria`**: Bitácora histórica (`id`, `usuario_id`, `accion`, `tabla`, `registro_id`, `detalles`, `fecha`).
20. **`notificaciones`**: Bandeja interna de avisos.
21. **`intentos_login`**: Control de Rate Limiting por IP (`id`, `ip_address`, `intentos`, `ultimo_intento`).
22. **`cache_dashboard_admin`**: Caché de estadísticas globales.
23. **`cache_dashboard_docente`**: Caché de estadísticas de profesores.

---

## 🛡️ 7. Protocolos de Seguridad y Despliegue en Producción

1. **Configuración `.env`**: Asegurar `APP_ENV=production` y `APP_DEBUG=false` para prevenir la fuga de stack traces en pantalla.
2. **Prepared Statements Obligatorios**: Toda consulta debe usar `PDO::prepare()`. No concatenar variables en cadenas SQL.
3. **Escapado XSS**: Utilizar siempre el helper `e($variable)` al imprimir contenido dinámico en vistas PHP.
4. **Validación CSRF**: Incluir `<?= csrf_field() ?>` en todo formulario con método `POST`.
5. **Permisos de Archivos**: Configurar permisos de lectura en `storage/` y restringir escritura en el directorio raíz.

