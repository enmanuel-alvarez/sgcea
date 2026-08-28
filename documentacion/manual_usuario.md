# Manual de Usuario - SGCEA
### Sistema de Gestión y Control Escolar-Académico
**Versión:** 1.0.0  
**Fecha:** Agosto 2026  

---

## 📋 Tabla de Contenidos
1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Módulo Administrador](#3-módulo-administrador)
   - [3.1 Dashboard y Estadísticas](#31-dashboard-y-estadísticas)
   - [3.2 Gestión de Usuarios y Permisos (ACL)](#32-gestión-de-usuarios-y-permisos-acl)
   - [3.3 Gestión de Estudiantes e Inscripciones](#33-gestión-de-estudiantes-e-inscripciones)
   - [3.4 Gestión de Docentes y Asignaciones](#34-gestión-de-docentes-y-asignaciones)
   - [3.5 Gestión de Materias, Grados y Secciones](#35-gestión-de-materias-grados-y-secciones)
   - [3.6 Solicitudes de Constancias](#36-solicitudes-de-constancias)
   - [3.7 Configuración, Respaldos y Seguridad](#37-configuración-respaldos-y-seguridad)
4. [Módulo Docente](#4-módulo-docente)
   - [4.1 Dashboard Docente](#41-dashboard-docente)
   - [4.2 Registro de Asistencias](#42-registro-de-asistencias)
   - [4.3 Gestión de Planes de Evaluación](#43-gestión-de-planes-de-evaluación)
   - [4.4 Carga y Edición de Calificaciones](#44-carga-y-edición-de-calificaciones)
5. [Módulo Estudiante](#5-módulo-estudiante)
   - [5.1 Dashboard Estudiante](#51-dashboard-estudiante)
   - [5.2 Consulta de Boletín y Calificaciones](#52-consulta-de-boletín-y-calificaciones)
   - [5.3 Historial de Asistencias](#53-historial-de-asistencias)
   - [5.4 Solicitud y Descarga de Constancias](#54-solicitud-y-descarga-de-constancias)
   - [5.5 Perfil de Usuario](#55-perfil-de-usuario)
6. [Preguntas Frecuentes y Solución de Problemas](#6-preguntas-frecuentes-y-solución-de-problemas)

---

## 1. Introducción
El **SGCEA** (Sistema de Gestión y Control Escolar-Académico) es una plataforma web integral diseñada para la automatización, control y administración de los procesos educativos de instituciones de educación primaria, secundaria y bachillerato.

El sistema contempla 3 perfiles de acceso claramente diferenciados mediante un control de acceso basado en listas de permisos (ACL):
- 👑 **Administrador**: Control operativo, institucional y técnico total del sistema.
- 👨‍🏫 **Docente**: Gestión pedagógica, registro de asistencia y evaluación continua de estudiantes.
- 🎓 **Estudiante**: Consulta académica, seguimiento de asistencias y autoservicio de constancias.

---

## 2. Acceso al Sistema

### 2.1 Iniciar Sesión
1. Abra su navegador web e ingrese a la dirección asignada por la institución (ej: `http://localhost/sgcea/public`).
2. En el formulario de login, ingrese su **Cédula de Identidad** o **Correo Electrónico**.
3. Ingrese su **Contraseña**.
4. Haga clic en el botón **"Iniciar Sesión"**.

> [!NOTE]
> Por motivos de seguridad, el sistema bloqueará temporalmente los intentos tras 5 fallos consecutivos mediante un sistema automático de *Rate Limiting*.

### 2.2 Cerrar Sesión
Para cerrar su sesión de forma segura:
- Haga clic en la foto o nombre de usuario ubicado en la barra superior derecha.
- Seleccione **"Cerrar Sesión"**.

---

## 3. Módulo Administrador

El perfil Administrador cuenta con acceso total a los módulos estratégicos e institucionales.

### 3.1 Dashboard y Estadísticas
Al ingresar, el administrador visualiza:
- **Métricas globales**: Total de usuarios, docentes activos, estudiantes inscritos, materias y secciones registradas.
- **Solicitudes de constancia pendientes**: Acceso rápido para aprobar o rechazar solicitudes de estudiantes.
- **Acciones rápidas**: Accesos a la creación de usuarios, reportes y respaldo del sistema.

### 3.2 Gestión de Usuarios y Permisos (ACL)
- **Ver Usuarios**: Lista paginada con filtros por nombre, cédula, correo o tipo de usuario (`admin`, `docente`, `estudiante`).
- **Crear Usuario**: Permite registrar un nuevo usuario especificando sus datos personales, credenciales iniciales y rol.
- **Asignar Permisos (Matriz ACL)**: El administrador puede activar o desactivar dinámicamente cualquiera de los 48 permisos del catálogo del sistema a cualquier usuario individual.

### 3.3 Gestión de Estudiantes e Inscripciones
- **Registro de Estudiantes**: Extiende la información personal con fecha de nacimiento, género, dirección, datos del representante y teléfono de contacto.
- **Inscripción en Secciones**: Asigna al estudiante a una sección y grado académico en el año escolar vigente.

### 3.4 Gestión de Docentes y Asignaciones
- **Registro de Docentes**: Añade especialidad, título profesional y fecha de ingreso.
- **Asignación de Cátedra**: Vincula al docente con una materia específica y una sección académica activa.

### 3.5 Gestión de Materias, Grados y Secciones
- **Materias**: Creación y edición de materias con su código único y créditos.
- **Grados y Secciones**: Definición de la oferta académica por niveles (Primaria, Secundaria, Bachillerato) y capacidad/cupos por sección.

### 3.6 Solicitudes de Constancias
- **Revisión de Solicitudes**: Listado de solicitudes de constancia de estudio, conducta o notas.
- **Aprobar / Rechazar**: El administrador puede aprobar la emisión o rechazarla adjuntando el motivo oficial de la decisión.

### 3.7 Configuración, Respaldos y Seguridad
- **Datos de la Institución**: Modificación del nombre del plantel, año académico activo, escala de notas y límites del sistema.
- **Módulo de Respaldo**:
  - **Exportar Respaldo**: Descarga una copia completa en formato JSON o SQL de la base de datos.
  - **Importar Respaldo**: Permite restaurar el estado del sistema desde una copia previamente guardada.

---

## 4. Módulo Docente

### 4.1 Dashboard Docente
El docente visualiza únicamente las asignaciones, secciones y materias que tiene encomendadas para el período académico en curso.

### 4.2 Registro de Asistencias
1. Seleccione la sección y materia asignada.
2. Seleccione la fecha de la sesión de clase.
3. Marque el estado de cada estudiante: `Presente`, `Ausente`, `Tarde` o `Justificado`.
4. Agregue observaciones opcionales y presione **"Guardar Asistencia"**.

### 4.3 Gestión de Planes de Evaluación
1. Acceda a la asignación deseada.
2. Defina las actividades o evaluaciones del lapso (ej: Examen Parcial, Taller, Proyecto).
3. Asigne el porcentaje de ponderación correspondiente (la suma de evaluaciones por lapso debe totalizar el 100%).

### 4.4 Carga y Edición de Calificaciones
1. Ingrese a la opción de cargar notas del plan de evaluación.
2. Registre la nota cuantitativa de cada estudiante en la escala oficial de 0 a 20 puntos.
3. El sistema recalcula automáticamente el promedio ponderado acumulado del estudiante.

---

## 5. Módulo Estudiante

### 5.1 Dashboard Estudiante
Resumen del estado académico del alumno: sección inscrita, promedio acumulado general y resumen del porcentaje de asistencia.

### 5.2 Consulta de Boletín y Calificaciones
- Visualización detallada de las calificaciones obtenidas en cada materia clasificadas por lapso académico.
- Estado final de cada asignatura (`Aprobada` / `Reprobada`).

### 5.3 Historial de Asistencias
- Reporte detallado de inasistencias y tardanzas registradas por fecha y materia.

### 5.4 Solicitud y Descarga de Constancias
1. Haga clic en **"Solicitar Constancia"**.
2. Seleccione el tipo de constancia (`Estudio`, `Conducta`, `Notas`, etc.) e indique el motivo.
3. Una vez aprobada por la administración, aparecerá el botón **"Imprimir / Descargar"** para obtener el documento oficial con su código de verificación.

---

## 6. Preguntas Frecuentes y Solución de Problemas

**¿Qué hago si olvidé mi contraseña?**  
Comuníquese con el administrador del sistema en su institución para que genere una clave temporal o restablezca sus credenciales.

**¿Por qué no puedo solicitar una nueva constancia?**  
El sistema limita las solicitudes a un máximo de 5 solicitudes pendientes en simultáneo para evitar saturación operativa. Debe esperar la aprobación de las solicitudes previas.

**¿Por qué no veo mis materias asignadas como docente?**  
Asegúrese de que el administrador del sistema le haya creado la **Asignación de Cátedra** formal para el año académico activo.
