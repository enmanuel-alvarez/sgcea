-- --------------------------------------------------------
-- Seed de datos para pruebas - SGCEA (Versión Corregida 100% fiel al esquema)
-- --------------------------------------------------------

-- 1. Insertar Materias
-- Columnas reales: id, nombre, codigo, descripcion, creditos, estado
INSERT IGNORE INTO `materias` (`nombre`, `codigo`, `descripcion`, `creditos`, `estado`) VALUES
('Matemáticas', 'MAT-101', 'Matemáticas Básicas', 3, 1),
('Física', 'FIS-101', 'Física General', 4, 1),
('Química', 'QUI-101', 'Química Básica', 3, 1),
('Programación I', 'PRO-101', 'Lógica de Programación', 4, 1);

-- 2. Insertar Secciones
-- Columnas reales: id, nombre, grado_id, ano_academico, cupo_maximo, estado
-- Asumimos grado_id 1 (1er Año, etc) porque no sabemos si insertamos grados en el dump base, pero en el dump hay grados pre-cargados (1 a 11)
INSERT IGNORE INTO `secciones` (`nombre`, `grado_id`, `ano_academico`, `cupo_maximo`, `estado`) VALUES
('A', 7, '2023-2024', 35, 1),
('B', 7, '2023-2024', 35, 1),
('C', 7, '2023-2024', 30, 1);

-- 3. Insertar Usuarios (Docentes y Estudiantes)
-- Columnas reales: id, cedula, nombre, apellido, email, password, tipo, estado, created_at, updated_at
INSERT IGNORE INTO `usuarios` (`cedula`, `nombre`, `apellido`, `email`, `password`, `tipo`, `estado`) VALUES
('11111111', 'Carlos', 'Docente', 'carlos@docente.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 1),
('22222222', 'Maria', 'Docente', 'maria@docente.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'docente', 1),
('33333333', 'Juan', 'Estudiante', 'juan@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1),
('44444444', 'Ana', 'Estudiante', 'ana@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1),
('55555555', 'Pedro', 'Estudiante', 'pedro@estudiante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'estudiante', 1);

-- 4. Insertar en Profesores (Docentes extendidos)
-- Columnas reales: id, usuario_id, especialidad, titulo, fecha_ingreso, estado
INSERT IGNORE INTO `profesores` (`usuario_id`, `especialidad`, `titulo`, `fecha_ingreso`) VALUES
((SELECT id FROM usuarios WHERE email = 'carlos@docente.com'), 'Matemáticas', 'Licenciado', '2015-09-01'),
((SELECT id FROM usuarios WHERE email = 'maria@docente.com'), 'Ciencias Exactas', 'Ingeniera', '2018-01-15');

-- 5. Insertar en Estudiantes (Estudiantes extendidos)
-- Columnas reales: id, usuario_id, fecha_nacimiento, genero, direccion, telefono, nombre_representante, telefono_representante, estado
INSERT IGNORE INTO `estudiantes` (`usuario_id`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `nombre_representante`, `telefono_representante`) VALUES
((SELECT id FROM usuarios WHERE email = 'juan@estudiante.com'), '2005-01-15', 'M', 'Calle 1, Ciudad', '04161234567', 'Representante Juan', '04161234568'),
((SELECT id FROM usuarios WHERE email = 'ana@estudiante.com'), '2006-05-20', 'F', 'Avenida 2, Ciudad', '04147654321', 'Representante Ana', '04147654322'),
((SELECT id FROM usuarios WHERE email = 'pedro@estudiante.com'), '2004-11-10', 'M', 'Urbanización 3, Ciudad', '04129876543', 'Representante Pedro', '04129876544');

-- 6. Insertar Asignaciones (Materias asignadas a profesores en secciones)
-- Columnas reales: id, profesor_id, materia_id, seccion_id, ano_academico, periodo, estado
INSERT IGNORE INTO `asignaciones` (`profesor_id`, `materia_id`, `seccion_id`, `ano_academico`, `periodo`, `estado`) VALUES
((SELECT id FROM profesores WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'carlos@docente.com')), (SELECT id FROM materias WHERE codigo = 'MAT-101'), (SELECT id FROM secciones WHERE nombre = 'A'), '2023-2024', '1', 1),
((SELECT id FROM profesores WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'maria@docente.com')), (SELECT id FROM materias WHERE codigo = 'FIS-101'), (SELECT id FROM secciones WHERE nombre = 'A'), '2023-2024', '1', 1),
((SELECT id FROM profesores WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'carlos@docente.com')), (SELECT id FROM materias WHERE codigo = 'PRO-101'), (SELECT id FROM secciones WHERE nombre = 'B'), '2023-2024', '1', 1);

-- 7. Insertar Inscripciones (Estudiantes inscritos en secciones)
-- Columnas reales: id, estudiante_id, seccion_id, ano_academico, fecha_inscripcion, estado
INSERT IGNORE INTO `inscripciones` (`estudiante_id`, `seccion_id`, `ano_academico`, `estado`) VALUES
((SELECT id FROM estudiantes WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'juan@estudiante.com')), (SELECT id FROM secciones WHERE nombre = 'A'), '2023-2024', 'activo'),
((SELECT id FROM estudiantes WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'ana@estudiante.com')), (SELECT id FROM secciones WHERE nombre = 'A'), '2023-2024', 'activo'),
((SELECT id FROM estudiantes WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'pedro@estudiante.com')), (SELECT id FROM secciones WHERE nombre = 'B'), '2023-2024', 'activo');
