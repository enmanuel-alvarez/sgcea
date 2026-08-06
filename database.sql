-- ============================================
-- SGCEA - Sistema de Gestión y Control Escolar-Académico
-- Script de Base de Datos MySQL
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- TABLA: instituciones
-- ============================================
CREATE TABLE IF NOT EXISTS `instituciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `direccion` VARCHAR(255),
  `telefono` VARCHAR(20),
  `email` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: permisos (Catálogo de permisos ACL)
-- ============================================
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `descripcion` TEXT,
  `modulo` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cedula` VARCHAR(20) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `tipo` ENUM('admin', 'docente', 'estudiante') NOT NULL DEFAULT 'estudiante',
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (`email`),
  INDEX idx_cedula (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuario_permisos (ACL por usuario)
-- ============================================
CREATE TABLE IF NOT EXISTS `usuario_permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `permiso_id` INT NOT NULL,
  `asignado_por` INT,
  `fecha_asignacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_usuario_permiso (`usuario_id`, `permiso_id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: profesores (Extensión de usuarios tipo docente)
-- ============================================
CREATE TABLE IF NOT EXISTS `profesores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL UNIQUE,
  `especialidad` VARCHAR(100),
  `titulo` VARCHAR(100),
  `fecha_ingreso` DATE,
  `estado` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: estudiantes (Extensión de usuarios tipo estudiante)
-- ============================================
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL UNIQUE,
  `fecha_nacimiento` DATE,
  `genero` ENUM('M', 'F'),
  `direccion` VARCHAR(255),
  `telefono` VARCHAR(20),
  `nombre_representante` VARCHAR(100),
  `telefono_representante` VARCHAR(20),
  `estado` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: grados
-- ============================================
CREATE TABLE IF NOT EXISTS `grados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL,
  `nivel` ENUM('primaria', 'secundaria', 'bachillerato') NOT NULL,
  `orden` INT NOT NULL,
  `estado` TINYINT(1) DEFAULT 1,
  UNIQUE KEY unique_grado (`nombre`, `nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: secciones
-- ============================================
CREATE TABLE IF NOT EXISTS `secciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(10) NOT NULL,
  `grado_id` INT NOT NULL,
  `ano_academico` VARCHAR(9) NOT NULL,
  `cupo_maximo` INT DEFAULT 35,
  `estado` TINYINT(1) DEFAULT 1,
  UNIQUE KEY unique_seccion (`nombre`, `grado_id`, `ano_academico`),
  FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: materias
-- ============================================
CREATE TABLE IF NOT EXISTS `materias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(20) UNIQUE,
  `descripcion` TEXT,
  `creditos` INT DEFAULT 1,
  `estado` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: asignaciones (Profesor - Materia - Sección)
-- ============================================
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `profesor_id` INT NOT NULL,
  `materia_id` INT NOT NULL,
  `seccion_id` INT NOT NULL,
  `ano_academico` VARCHAR(9) NOT NULL,
  `periodo` ENUM('1', '2', '3', '4', 'anual') DEFAULT 'anual',
  `estado` TINYINT(1) DEFAULT 1,
  UNIQUE KEY unique_asignacion (`profesor_id`, `materia_id`, `seccion_id`, `ano_academico`, `periodo`),
  FOREIGN KEY (`profesor_id`) REFERENCES `profesores`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`materia_id`) REFERENCES `materias`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: inscripciones (Estudiante - Sección)
-- ============================================
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `seccion_id` INT NOT NULL,
  `ano_academico` VARCHAR(9) NOT NULL,
  `fecha_inscripcion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('activo', 'retirado', 'graduado') DEFAULT 'activo',
  UNIQUE KEY unique_inscripcion (`estudiante_id`, `ano_academico`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: planes_evaluacion (Actividades de evaluación)
-- ============================================
CREATE TABLE IF NOT EXISTS `planes_evaluacion` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `asignacion_id` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `tipo` ENUM('examen', 'tarea', 'proyecto', 'participacion', 'otro') NOT NULL,
  `ponderacion` DECIMAL(5,2) NOT NULL,
  `fecha_programada` DATE,
  `descripcion` TEXT,
  `estado` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`asignacion_id`) REFERENCES `asignaciones`(`id`) ON DELETE CASCADE,
  CHECK (`ponderacion` >= 0 AND `ponderacion` <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: calificaciones
-- ============================================
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `plan_evaluacion_id` INT NOT NULL,
  `nota` DECIMAL(5,2) NOT NULL,
  `observaciones` TEXT,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `profesor_id` INT NOT NULL,
  UNIQUE KEY unique_calificacion (`estudiante_id`, `plan_evaluacion_id`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_evaluacion_id`) REFERENCES `planes_evaluacion`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`profesor_id`) REFERENCES `profesores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: asistencias
-- ============================================
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `asignacion_id` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `estado` ENUM('presente', 'ausente', 'tarde', 'justificado') NOT NULL DEFAULT 'presente',
  `observaciones` TEXT,
  `profesor_id` INT NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_asistencia (`estudiante_id`, `asignacion_id`, `fecha`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`asignacion_id`) REFERENCES `asignaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`profesor_id`) REFERENCES `profesores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: solicitudes_constancia
-- ============================================
CREATE TABLE IF NOT EXISTS `solicitudes_constancia` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `tipo` ENUM('estudio', 'conducta', 'notas', 'inscripcion', 'otro') NOT NULL,
  `motivo` TEXT NOT NULL,
  `estado` ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
  `fecha_solicitud` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_resolucion` TIMESTAMP NULL,
  `resolucion_motivo` TEXT,
  `resuelto_por` INT,
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`resuelto_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuraciones
-- ============================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT,
  `descripcion` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: auditoria
-- ============================================
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT,
  `accion` VARCHAR(100) NOT NULL,
  `tabla` VARCHAR(50),
  `registro_id` INT,
  `detalles` JSON,
  `ip` VARCHAR(45),
  `user_agent` VARCHAR(255),
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  INDEX idx_fecha (`fecha`),
  INDEX idx_usuario (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: notificaciones
-- ============================================
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `titulo` VARCHAR(100) NOT NULL,
  `mensaje` TEXT NOT NULL,
  `leida` TINYINT(1) DEFAULT 0,
  `tipo` ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: intentos_login (Rate limiting)
-- ============================================
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100),
  `fecha_intento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip (`ip`),
  INDEX idx_fecha (`fecha_intento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: cache_dashboard_admin
-- ============================================
CREATE TABLE IF NOT EXISTS `cache_dashboard_admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` JSON NOT NULL,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: cache_dashboard_docente
-- ============================================
CREATE TABLE IF NOT EXISTS `cache_dashboard_docente` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `profesor_id` INT NOT NULL,
  `clave` VARCHAR(100) NOT NULL,
  `valor` JSON NOT NULL,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_cache_docente (`profesor_id`, `clave`),
  FOREIGN KEY (`profesor_id`) REFERENCES `profesores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES: Permisos del catálogo
-- ============================================
INSERT INTO `permisos` (`nombre`, `descripcion`, `modulo`) VALUES
-- Módulo Admin
('admin.dashboard', 'Acceso al dashboard de administrador', 'admin'),
('admin.usuarios.ver', 'Ver listado de usuarios', 'admin'),
('admin.usuarios.crear', 'Crear nuevos usuarios', 'admin'),
('admin.usuarios.editar', 'Editar usuarios existentes', 'admin'),
('admin.usuarios.eliminar', 'Eliminar usuarios', 'admin'),
('admin.estudiantes.ver', 'Ver listado de estudiantes', 'admin'),
('admin.estudiantes.crear', 'Registrar estudiantes', 'admin'),
('admin.estudiantes.editar', 'Editar estudiantes', 'admin'),
('admin.estudiantes.eliminar', 'Eliminar estudiantes', 'admin'),
('admin.estudiantes.inscribir', 'Inscribir estudiantes en secciones', 'admin'),
('admin.docentes.ver', 'Ver listado de docentes', 'admin'),
('admin.docentes.crear', 'Registrar docentes', 'admin'),
('admin.docentes.editar', 'Editar docentes', 'admin'),
('admin.docentes.eliminar', 'Eliminar docentes', 'admin'),
('admin.materias.ver', 'Ver materias', 'admin'),
('admin.materias.crear', 'Crear materias', 'admin'),
('admin.materias.editar', 'Editar materias', 'admin'),
('admin.materias.eliminar', 'Eliminar materias', 'admin'),
('admin.secciones.ver', 'Ver secciones', 'admin'),
('admin.secciones.crear', 'Crear secciones', 'admin'),
('admin.secciones.editar', 'Editar secciones', 'admin'),
('admin.secciones.eliminar', 'Eliminar secciones', 'admin'),
('admin.asignaciones.ver', 'Ver asignaciones', 'admin'),
('admin.asignaciones.crear', 'Crear asignaciones', 'admin'),
('admin.asignaciones.eliminar', 'Eliminar asignaciones', 'admin'),
('admin.constancias.ver', 'Ver solicitudes de constancias', 'admin'),
('admin.constancias.aprobar', 'Aprobar/rechazar constancias', 'admin'),
('admin.permisos.asignar', 'Asignar permisos a usuarios', 'admin'),
('admin.configuracion.ver', 'Ver configuración del sistema', 'admin'),
('admin.configuracion.editar', 'Editar configuración', 'admin'),
-- Módulo Docente
('docente.dashboard', 'Acceso al dashboard de docente', 'docente'),
('docente.calificaciones.ver', 'Ver calificaciones de sus asignaciones', 'docente'),
('docente.calificaciones.registrar', 'Registrar calificaciones', 'docente'),
('docente.asistencia.ver', 'Ver asistencia de sus asignaciones', 'docente'),
('docente.asistencia.registrar', 'Registrar asistencia', 'docente'),
('docente.planevaluacion.gestionar', 'Gestionar plan de evaluación', 'docente'),
-- Módulo Estudiante
('estudiante.dashboard', 'Acceso al dashboard de estudiante', 'estudiante'),
('estudiante.boletin.ver', 'Ver boletín de calificaciones', 'estudiante'),
('estudiante.asistencia.ver', 'Ver su historial de asistencia', 'estudiante'),
('estudiante.constancias.solicitar', 'Solicitar constancias', 'estudiante'),
('estudiante.constancias.ver', 'Ver historial de constancias', 'estudiante'),
('estudiante.perfil.ver', 'Ver su perfil', 'estudiante'),
('estudiante.perfil.editar', 'Editar su perfil', 'estudiante'),
-- Reportes
('reportes.ver', 'Acceso a reportes generales', 'reportes');

-- ============================================
-- DATOS INICIALES: Configuración básica
-- ============================================
INSERT INTO `configuraciones` (`clave`, `valor`, `descripcion`) VALUES
('nombre_sistema', 'SGCEA - Sistema de Gestión y Control Escolar-Académico', 'Nombre completo del sistema'),
('nombre_institucion', 'Institución Educativa Demo', 'Nombre de la institución'),
('ano_academico_actual', '2024-2025', 'Año académico en curso'),
('nota_minima_aprobacion', '10', 'Nota mínima para aprobar (escala 0-20)'),
('periodos_academicos', '4', 'Número de períodos académicos'),
('max_solicitudes_constancia', '5', 'Máximo de solicitudes de constancia pendientes por estudiante');

-- ============================================
-- DATOS INICIALES: Usuario Administrador por defecto
-- ============================================
-- Contraseña: admin123 (hash bcrypt)
INSERT INTO `usuarios` (`cedula`, `nombre`, `apellido`, `email`, `password`, `tipo`, `estado`) VALUES
('00000000', 'Administrador', 'Sistema', 'admin@sgcea.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Asignar todos los permisos al administrador
INSERT INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 1, id FROM `permisos`;

-- ============================================
-- DATOS INICIALES: Grados de ejemplo
-- ============================================
INSERT INTO `grados` (`nombre`, `nivel`, `orden`, `estado`) VALUES
('1er Grado', 'primaria', 1, 1),
('2do Grado', 'primaria', 2, 1),
('3er Grado', 'primaria', 3, 1),
('4to Grado', 'primaria', 4, 1),
('5to Grado', 'primaria', 5, 1),
('6to Grado', 'primaria', 6, 1),
('1er Año', 'secundaria', 7, 1),
('2do Año', 'secundaria', 8, 1),
('3er Año', 'secundaria', 9, 1),
('4to Año', 'bachillerato', 10, 1),
('5to Año', 'bachillerato', 11, 1);

COMMIT;
