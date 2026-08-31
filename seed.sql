-- ============================================
-- SGCEA - Datos de Prueba / Poblado de Prueba (seed.sql)
-- Ejecutar después de importar database.sql
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Insertar Materias de Prueba
INSERT IGNORE INTO `materias` (`id`, `nombre`, `codigo`, `descripcion`, `creditos`, `estado`) VALUES
(1, 'Matemáticas', 'MAT-101', 'Matemáticas Básicas y Álgebra', 3, 1),
(2, 'Física', 'FIS-101', 'Física General y Cinemática', 4, 1),
(3, 'Química', 'QUI-101', 'Química General y Orgánica', 3, 1),
(4, 'Programación I', 'PRO-101', 'Lógica y Algoritmos de Programación', 4, 1),
(5, 'Castellano y Literatura', 'CAS-101', 'Gramática, Redacción y Literatura', 3, 1),
(6, 'Historia de Venezuela', 'HIS-101', 'Historia Contemporánea', 2, 1);

-- 2. Insertar Secciones de Prueba (Asociadas a Grados 7 y 8)
INSERT IGNORE INTO `secciones` (`id`, `nombre`, `grado_id`, `ano_academico`, `cupo_maximo`, `estado`) VALUES
(1, 'A', 7, '2024-2025', 35, 1),
(2, 'B', 7, '2024-2025', 35, 1),
(3, 'A', 8, '2024-2025', 30, 1);

-- 3. Insertar Usuarios de Prueba (Docentes y Estudiantes)
-- Contraseña predeterminada para todos los usuarios de prueba: password123 ($2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
INSERT IGNORE INTO `usuarios` (`id`, `cedula`, `nombre`, `apellido`, `email`, `password`, `tipo`, `estado`) VALUES
(2, '11111111', 'Carlos', 'Docente', 'carlos@docente.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 1),
(3, '22222222', 'María', 'Docente', 'maria@docente.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 1),
(4, '33333333', 'Juan', 'Estudiante', 'juan@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1),
(5, '44444444', 'Ana', 'Estudiante', 'ana@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1),
(6, '55555555', 'Pedro', 'Estudiante', 'pedro@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1);

-- Permisos ACL para Docentes (IDs 2 y 3)
INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 2, id FROM `permisos` WHERE `modulo` IN ('docente', 'reportes');

INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 3, id FROM `permisos` WHERE `modulo` IN ('docente', 'reportes');

-- Permisos ACL para Estudiantes (IDs 4, 5 y 6)
INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 4, id FROM `permisos` WHERE `modulo` = 'estudiante';

INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 5, id FROM `permisos` WHERE `modulo` = 'estudiante';

INSERT IGNORE INTO `usuario_permisos` (`usuario_id`, `permiso_id`)
SELECT 6, id FROM `permisos` WHERE `modulo` = 'estudiante';

-- 4. Registrar en Profesores
INSERT IGNORE INTO `profesores` (`id`, `usuario_id`, `especialidad`, `titulo`, `fecha_ingreso`, `estado`) VALUES
(1, 2, 'Matemáticas y Física', 'Licenciado en Educación', '2018-09-15', 1),
(2, 3, 'Ciencias Químicas y Biología', 'Ingeniera Química', '2020-01-10', 1);

-- 5. Registrar en Estudiantes
INSERT IGNORE INTO `estudiantes` (`id`, `usuario_id`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `nombre_representante`, `telefono_representante`, `estado`) VALUES
(1, 4, '2008-03-15', 'M', 'Calle 10, Sector Centro', '0416-1234567', 'José Pérez', '0416-9876543', 1),
(2, 5, '2008-07-22', 'F', 'Av. Bolívar, Res. La Floresta', '0414-7654321', 'Carmen Gómez', '0414-1112233', 1),
(3, 6, '2007-11-05', 'M', 'Urb. Los Naranjos, Casa 45', '0412-9876543', 'Roberto Silva', '0412-4445566', 1);

-- 6. Insertar Asignaciones Docente -> Materia -> Sección
INSERT IGNORE INTO `asignaciones` (`id`, `profesor_id`, `materia_id`, `seccion_id`, `ano_academico`, `periodo`, `estado`) VALUES
(1, 1, 1, 1, '2024-2025', '1', 1), -- Prof. Carlos -> Matemáticas -> 1er Año A
(2, 1, 2, 1, '2024-2025', '1', 1), -- Prof. Carlos -> Física -> 1er Año A
(3, 2, 3, 1, '2024-2025', '1', 1), -- Prof. María -> Química -> 1er Año A
(4, 2, 4, 2, '2024-2025', '1', 1); -- Prof. María -> Programación -> 1er Año B

-- 7. Insertar Inscripciones Estudiantes en Secciones
INSERT IGNORE INTO `inscripciones` (`id`, `estudiante_id`, `seccion_id`, `ano_academico`, `estado`) VALUES
(1, 1, 1, '2024-2025', 'activo'), -- Juan -> 1er Año A
(2, 2, 1, '2024-2025', 'activo'), -- Ana -> 1er Año A
(3, 3, 2, '2024-2025', 'activo'); -- Pedro -> 1er Año B

-- 8. Insertar Planes de Evaluación
INSERT IGNORE INTO `planes_evaluacion` (`id`, `asignacion_id`, `titulo`, `descripcion`, `porcentaje`, `fecha_evaluacion`, `lapso`) VALUES
(1, 1, 'Examen Parcial I - Álgebra', 'Evaluación de ecuaciones de 1er grado', 30.00, '2024-10-15', 1),
(2, 1, 'Taller Grupal - Geometría', 'Resolución de ejercicios prácticos', 20.00, '2024-11-02', 1),
(3, 1, 'Examen Final Lapso I', 'Evaluación acumulativa del lapso', 50.00, '2024-12-10', 1),
(4, 2, 'Laboratorio I - Movimiento', 'Práctica de laboratorio sobre MRU', 40.00, '2024-10-20', 1);

-- 9. Insertar Calificaciones de Prueba
INSERT IGNORE INTO `calificaciones` (`id`, `estudiante_id`, `plan_evaluacion_id`, `nota`, `observaciones`) VALUES
(1, 1, 1, 16.50, 'Excelente desempeño'),
(2, 1, 2, 18.00, 'Trabajo en equipo destacado'),
(3, 2, 1, 14.00, 'Buen rendimiento'),
(4, 2, 2, 15.00, 'Completado');

-- 10. Insertar Registros de Asistencia
INSERT IGNORE INTO `asistencias` (`id`, `estudiante_id`, `asignacion_id`, `fecha`, `estado`, `observaciones`, `profesor_id`) VALUES
(1, 1, 1, CURDATE(), 'presente', 'A tiempo', 1),
(2, 2, 1, CURDATE(), 'presente', 'A tiempo', 1),
(3, 1, 2, CURDATE(), 'tarde', 'Llegó 10 min tarde', 1),
(4, 2, 2, CURDATE(), 'ausente', 'Falta con justificativo médico', 1);

-- 11. Insertar Solicitudes de Constancias de Prueba
INSERT IGNORE INTO `solicitudes_constancia` (`id`, `estudiante_id`, `tipo`, `motivo`, `estado`, `fecha_solicitud`) VALUES
(1, 1, 'estudio', 'Trámite de beca escolar', 'aprobada', NOW()),
(2, 2, 'conducta', 'Requisito de inscripción deportiva', 'pendiente', NOW());

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;


