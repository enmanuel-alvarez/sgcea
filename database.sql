-- ============================================
-- SGCEA - Sistema de Gestión y Control Escolar-Académico
-- Script de Base de Datos MySQL (Estructura y Datos Esenciales)
-- Version 1.0.0
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- TABLA: instituciones
-- ============================================
CREATE TABLE IF NOT EXISTS `instituciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `codigo_dependencia` VARCHAR(50) DEFAULT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `director_nombre` VARCHAR(150) DEFAULT NULL,
  `director_cedula` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: roles (Catálogo de roles del sistema)
-- ============================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: permisos (Catálogo de permisos ACL)
-- ============================================
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `descripcion` TEXT DEFAULT NULL,
  `modulo` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: rol_permiso (Permisos base heredados por cada Rol)
-- ============================================
CREATE TABLE IF NOT EXISTS `rol_permiso` (
  `rol_id` INT NOT NULL,
  `permiso_id` INT NOT NULL,
  PRIMARY KEY (`rol_id`, `permiso_id`),
  FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `rol_id` INT NOT NULL DEFAULT 3,
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
  INDEX idx_cedula (`cedula`),
  FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuario_permisos (Excepciones ACL por usuario: CONCEDER / REVOCAR)
-- ============================================
CREATE TABLE IF NOT EXISTS `usuario_permisos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `permiso_id` INT NOT NULL,
  `tipo` ENUM('CONCEDER', 'REVOCAR') NOT NULL DEFAULT 'CONCEDER',
  `asignado_por` INT DEFAULT NULL,
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
  `especialidad` VARCHAR(100) DEFAULT NULL,
  `titulo` VARCHAR(100) DEFAULT NULL,
  `fecha_ingreso` DATE DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `estado` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: estudiantes (Extensión de usuarios tipo estudiante)
-- ============================================
CREATE TABLE IF NOT EXISTS `estudiantes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL UNIQUE,
  `fecha_nacimiento` DATE DEFAULT NULL,
  `genero` ENUM('M', 'F') DEFAULT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `nombre_representante` VARCHAR(100) DEFAULT NULL,
  `telefono_representante` VARCHAR(20) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `estado` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: grados
-- ============================================
CREATE TABLE IF NOT EXISTS `grados` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL,
  `nivel` VARCHAR(50) DEFAULT 'Secundaria',
  `orden` INT DEFAULT 1,
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: secciones
-- ============================================
CREATE TABLE IF NOT EXISTS `secciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(10) NOT NULL,
  `grado_id` INT NOT NULL,
  `ano_academico` VARCHAR(20) DEFAULT '2024-2025',
  `cupo_maximo` INT DEFAULT 35,
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: materias
-- ============================================
CREATE TABLE IF NOT EXISTS `materias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(20) NOT NULL UNIQUE,
  `descripcion` TEXT DEFAULT NULL,
  `creditos` INT DEFAULT 3,
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: inscripciones
-- ============================================
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `seccion_id` INT NOT NULL,
  `ano_academico` VARCHAR(20) NOT NULL,
  `fecha_inscripcion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('activo', 'retirado', 'graduado', 'suspendido') DEFAULT 'activo',
  UNIQUE KEY unique_inscripcion (`estudiante_id`, `seccion_id`, `ano_academico`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: asignaciones (Profesor -> Materia -> Sección)
-- ============================================
CREATE TABLE IF NOT EXISTS `asignaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `profesor_id` INT NOT NULL,
  `materia_id` INT NOT NULL,
  `seccion_id` INT NOT NULL,
  `ano_academico` VARCHAR(20) NOT NULL,
  `periodo` VARCHAR(20) DEFAULT '1',
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_asignacion (`profesor_id`, `materia_id`, `seccion_id`, `ano_academico`),
  FOREIGN KEY (`profesor_id`) REFERENCES `profesores`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`materia_id`) REFERENCES `materias`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: planes_evaluacion
-- ============================================
CREATE TABLE IF NOT EXISTS `planes_evaluacion` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `asignacion_id` INT NOT NULL,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `porcentaje` DECIMAL(5,2) NOT NULL,
  `fecha_evaluacion` DATE DEFAULT NULL,
  `lapso` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`asignacion_id`) REFERENCES `asignaciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: calificaciones
-- ============================================
CREATE TABLE IF NOT EXISTS `calificaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `estudiante_id` INT NOT NULL,
  `plan_evaluacion_id` INT NOT NULL,
  `nota` DECIMAL(4,2) NOT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_calificacion (`estudiante_id`, `plan_evaluacion_id`),
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_evaluacion_id`) REFERENCES `planes_evaluacion`(`id`) ON DELETE CASCADE
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
  `observaciones` TEXT DEFAULT NULL,
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
  `fecha_resolucion` TIMESTAMP NULL DEFAULT NULL,
  `resolucion_motivo` TEXT DEFAULT NULL,
  `resuelto_por` INT DEFAULT NULL,
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`resuelto_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuraciones
-- ============================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` TEXT DEFAULT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: auditoria
-- ============================================
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT DEFAULT NULL,
  `accion` VARCHAR(100) NOT NULL,
  `tabla` VARCHAR(50) DEFAULT NULL,
  `registro_id` INT DEFAULT NULL,
  `detalles` JSON DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
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
  `email` VARCHAR(100) DEFAULT NULL,
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
-- DATOS INICIALES ESECIALES: Catálogo de Permisos ACL
-- ============================================
INSERT IGNORE INTO `permisos` (`id`, `nombre`, `descripcion`, `modulo`) VALUES
-- Módulo Admin
(1, 'admin.dashboard', 'Acceso al dashboard de administrador', 'admin'),
(2, 'admin.usuarios.ver', 'Ver listado de usuarios', 'admin'),
(3, 'admin.usuarios.crear', 'Crear nuevos usuarios', 'admin'),
(4, 'admin.usuarios.editar', 'Editar usuarios existentes', 'admin'),
(5, 'admin.usuarios.eliminar', 'Eliminar usuarios', 'admin'),
(6, 'admin.estudiantes.ver', 'Ver listado de estudiantes', 'admin'),
(7, 'admin.estudiantes.crear', 'Registrar estudiantes', 'admin'),
(8, 'admin.estudiantes.editar', 'Editar estudiantes', 'admin'),
(9, 'admin.estudiantes.eliminar', 'Eliminar estudiantes', 'admin'),
(10, 'admin.estudiantes.inscribir', 'Inscribir estudiantes en secciones', 'admin'),
(11, 'admin.docentes.ver', 'Ver listado de docentes', 'admin'),
(12, 'admin.docentes.crear', 'Registrar docentes', 'admin'),
(13, 'admin.docentes.editar', 'Editar docentes', 'admin'),
(14, 'admin.docentes.eliminar', 'Eliminar docentes', 'admin'),
(15, 'admin.materias.ver', 'Ver materias', 'admin'),
(16, 'admin.materias.crear', 'Crear materias', 'admin'),
(17, 'admin.materias.editar', 'Editar materias', 'admin'),
(18, 'admin.materias.eliminar', 'Eliminar materias', 'admin'),
(19, 'admin.secciones.ver', 'Ver secciones', 'admin'),
(20, 'admin.secciones.crear', 'Crear secciones', 'admin'),
(21, 'admin.secciones.editar', 'Editar secciones', 'admin'),
(22, 'admin.secciones.eliminar', 'Eliminar secciones', 'admin'),
(23, 'admin.asignaciones.ver', 'Ver asignaciones', 'admin'),
(24, 'admin.asignaciones.crear', 'Crear asignaciones', 'admin'),
(25, 'admin.asignaciones.eliminar', 'Eliminar asignaciones', 'admin'),
(26, 'admin.constancias.ver', 'Ver solicitudes de constancias', 'admin'),
(27, 'admin.constancias.aprobar', 'Aprobar/rechazar constancias', 'admin'),
(28, 'admin.permisos.asignar', 'Asignar permisos a usuarios', 'admin'),
(29, 'admin.configuracion.ver', 'Ver configuración del sistema', 'admin'),
(30, 'admin.configuracion.editar', 'Editar configuración', 'admin'),
(31, 'admin.configuracion.reiniciar', 'Reiniciar datos del sistema (Zona de Peligro)', 'admin'),
(32, 'admin.backup.ver', 'Acceso al módulo de respaldo (Importar/Exportar)', 'admin'),
(33, 'admin.backup.exportar', 'Exportar datos del sistema en JSON', 'admin'),
(34, 'admin.backup.importar', 'Importar datos al sistema desde JSON', 'admin'),
-- Módulo Docente
(35, 'docente.dashboard', 'Acceso al dashboard de docente', 'docente'),
(36, 'docente.calificaciones.ver', 'Ver calificaciones de sus asignaciones', 'docente'),
(37, 'docente.calificaciones.registrar', 'Registrar calificaciones', 'docente'),
(38, 'docente.asistencia.ver', 'Ver asistencia de sus asignaciones', 'docente'),
(39, 'docente.asistencia.registrar', 'Registrar asistencia', 'docente'),
(40, 'docente.planevaluacion.gestionar', 'Gestionar plan de evaluación', 'docente'),
-- Módulo Estudiante
(41, 'estudiante.dashboard', 'Acceso al dashboard de estudiante', 'estudiante'),
(42, 'estudiante.boletin.ver', 'Ver boletín de calificaciones', 'estudiante'),
(43, 'estudiante.asistencia.ver', 'Ver su historial de asistencia', 'estudiante'),
(44, 'estudiante.constancias.solicitar', 'Solicitar constancias', 'estudiante'),
(45, 'estudiante.constancias.ver', 'Ver historial de constancias', 'estudiante'),
(46, 'estudiante.perfil.ver', 'Ver su perfil', 'estudiante'),
(47, 'estudiante.perfil.editar', 'Editar su perfil', 'estudiante'),
-- Reportes
(48, 'reportes.ver', 'Acceso a reportes generales', 'reportes');

-- ============================================
-- DATOS INICIALES ESECIALES: Configuración del Sistema
-- ============================================
INSERT IGNORE INTO `configuraciones` (`clave`, `valor`, `descripcion`) VALUES
('nombre_sistema', 'SGCEA - Sistema de Gestión y Control Escolar-Académico', 'Nombre completo del sistema'),
('nombre_institucion', 'Institución Educativa SGCEA', 'Nombre de la institución escolar'),
('ano_academico_actual', '2024-2025', 'Año académico en curso'),
('nota_minima_aprobacion', '10', 'Nota mínima para aprobar (escala 0-20)'),
('periodos_academicos', '4', 'Número de períodos o lapsos académicos'),
('max_solicitudes_constancia', '5', 'Máximo de solicitudes de constancia pendientes por estudiante');

-- ============================================
-- DATOS INICIALES ESECIALES: Institución Base
-- ============================================
INSERT IGNORE INTO `instituciones` (`id`, `nombre`, `codigo_dependencia`, `direccion`, `telefono`, `email`, `director_nombre`, `director_cedula`) VALUES
(1, 'Unidad Educativa SGCEA', 'DEP-001', 'Av. Principal, Sector Centro', '0212-5550000', 'contacto@sgcea.edu', 'Prof. Director General', '12345678');

-- ============================================
-- DATOS INICIALES ESPECIALES: Roles del Sistema
-- ============================================
INSERT IGNORE INTO `roles` (`id`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Administrador', 'admin', 'Acceso completo de gestión administrativa'),
(2, 'Docente', 'docente', 'Acceso a carga académica, asistencias y evaluaciones'),
(3, 'Estudiante', 'estudiante', 'Acceso a consultas de boletines y trámites de constancias');

-- ============================================
-- DATOS INICIALES ESPECIALES: Permisos por defecto por Rol (rol_permiso)
-- ============================================
-- Rol 1 (Admin): Todos los permisos admin + reportes
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 1, id FROM `permisos` WHERE modulo IN ('admin', 'reportes');

-- Rol 2 (Docente): Permisos del módulo docente
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 2, id FROM `permisos` WHERE modulo = 'docente';

-- Rol 3 (Estudiante): Permisos del módulo estudiante
INSERT IGNORE INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT 3, id FROM `permisos` WHERE modulo = 'estudiante';

-- ============================================
-- DATOS INICIALES ESPECIALES: Usuario Administrador Inicial
-- Credenciales: admin@sgcea.com / admin123
-- ============================================
INSERT IGNORE INTO `usuarios` (`id`, `rol_id`, `cedula`, `nombre`, `apellido`, `email`, `password`, `tipo`, `estado`) VALUES
(1, 1, '00000000', 'Administrador', 'Sistema', 'admin@sgcea.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Excepción explícita de otorgamiento completo para usuario Administrador principal ID 1
INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`, `tipo`)
SELECT 1, id, 'CONCEDER' FROM `permisos`;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

