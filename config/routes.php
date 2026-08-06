<?php
return [
    'GET' => [
        '/login' => ['AuthController@mostrarLogin', null],
        '/logout' => ['AuthController@cerrarSesion', null],
        '/admin' => ['AdminController@index', 'admin.dashboard'],
        '/admin/usuarios' => ['AdminController@listarUsuarios', 'admin.usuarios.ver'],
        '/admin/usuarios/crear' => ['AdminController@mostrarCrearUsuario', 'admin.usuarios.crear'],
        '/admin/usuarios/editar/{id}' => ['AdminController@editarUsuario', 'admin.usuarios.editar'],
        '/admin/usuarios/eliminar/{id}' => ['AdminController@eliminarUsuario', 'admin.usuarios.eliminar'],
        
        '/admin/estudiantes' => ['AdminController@listarEstudiantes', 'admin.estudiantes.ver'],
        '/admin/estudiantes/crear' => ['AdminController@mostrarCrearEstudiante', 'admin.estudiantes.crear'],
        '/admin/estudiantes/editar/{id}' => ['AdminController@editarEstudiante', 'admin.estudiantes.editar'],
        '/admin/estudiantes/eliminar/{id}' => ['AdminController@eliminarEstudiante', 'admin.estudiantes.eliminar'],
        '/admin/estudiantes/inscribir/{id}' => ['AdminController@mostrarInscribirEstudiante', 'admin.estudiantes.inscribir'],
        
        '/admin/docentes' => ['AdminController@listarDocentes', 'admin.docentes.ver'],
        '/admin/docentes/crear' => ['AdminController@mostrarCrearDocente', 'admin.docentes.crear'],
        '/admin/docentes/editar/{id}' => ['AdminController@editarDocente', 'admin.docentes.editar'],
        '/admin/docentes/eliminar/{id}' => ['AdminController@eliminarDocente', 'admin.docentes.eliminar'],
        
        '/admin/materias' => ['AdminController@listarMaterias', 'admin.materias.ver'],
        '/admin/materias/crear' => ['AdminController@mostrarCrearMateria', 'admin.materias.crear'],
        '/admin/materias/editar/{id}' => ['AdminController@editarMateria', 'admin.materias.editar'],
        '/admin/materias/eliminar/{id}' => ['AdminController@eliminarMateria', 'admin.materias.eliminar'],
        
        '/admin/secciones' => ['AdminController@listarSecciones', 'admin.secciones.ver'],
        '/admin/secciones/crear' => ['AdminController@mostrarCrearSeccion', 'admin.secciones.crear'],
        '/admin/secciones/editar/{id}' => ['AdminController@editarSeccion', 'admin.secciones.editar'],
        '/admin/secciones/eliminar/{id}' => ['AdminController@eliminarSeccion', 'admin.secciones.eliminar'],
        
        '/admin/asignaciones' => ['AdminController@listarAsignaciones', 'admin.asignaciones.ver'],
        '/admin/asignaciones/crear' => ['AdminController@mostrarCrearAsignacion', 'admin.asignaciones.crear'],
        '/admin/asignaciones/eliminar/{id}' => ['AdminController@eliminarAsignacion', 'admin.asignaciones.eliminar'],
        
        // FIX #1: listarConstancias → gestionarConstancias
        '/admin/constancias' => ['AdminController@gestionarConstancias', 'admin.constancias.ver'],
        '/admin/constancias/aprobar/{id}' => ['AdminController@aprobarConstancia', 'admin.constancias.aprobar'],
        '/admin/constancias/rechazar/{id}' => ['AdminController@rechazarConstancia', 'admin.constancias.aprobar'],
        
        // FIX #4: asignarPermisos → mostrarAsignarPermisos
        '/admin/permisos/asignar/{id}' => ['AdminController@mostrarAsignarPermisos', 'admin.permisos.asignar'],
        
        '/admin/configuracion' => ['ConfiguracionController@index', 'admin.configuracion.ver'],
        '/docente' => ['DocenteController@index', 'docente.dashboard'],
        '/docente/calificaciones' => ['DocenteController@calificaciones', 'docente.calificaciones.ver'],
        '/docente/calificaciones/registrar/{id_asignacion}' => ['DocenteController@registrarCalificaciones', 'docente.calificaciones.registrar'],
        '/docente/asistencia' => ['DocenteController@asistencia', 'docente.asistencia.ver'],
        '/docente/asistencia/registrar/{id_asignacion}' => ['DocenteController@registrarAsistencia', 'docente.asistencia.registrar'],
        '/docente/planevaluacion/{id_asignacion}' => ['DocenteController@planEvaluacion', 'docente.planevaluacion.gestionar'],
        // Alias con guión para compatibilidad con redirects del controller
        '/docente/plan-evaluacion/{id_asignacion}' => ['DocenteController@planEvaluacion', 'docente.planevaluacion.gestionar'],
        // NUEVO: Ruta para eliminar actividad del plan de evaluación
        '/docente/planevaluacion/eliminar/{id}' => ['DocenteController@eliminarActividad', 'docente.planevaluacion.gestionar'],
        '/docente/plan-evaluacion/eliminar/{id}' => ['DocenteController@eliminarActividad', 'docente.planevaluacion.gestionar'],

        '/estudiante' => ['EstudianteController@index', 'estudiante.dashboard'],
        '/estudiante/boletin' => ['EstudianteController@boletin', 'estudiante.boletin.ver'],
        '/estudiante/asistencia' => ['EstudianteController@asistencia', 'estudiante.asistencia.ver'],
        '/estudiante/constancias/solicitar' => ['EstudianteController@solicitarConstancia', 'estudiante.constancias.solicitar'],
        '/estudiante/constancias/historial' => ['EstudianteController@historialConstancias', 'estudiante.constancias.ver'],
        // NUEVO: Ruta para descargar constancia
        '/estudiante/constancias/descargar/{id}' => ['EstudianteController@descargarConstancia', 'estudiante.constancias.ver'],
        '/estudiante/perfil' => ['EstudianteController@perfil', 'estudiante.perfil.ver'],
        '/constancias/imprimir/{id}' => ['ConstanciaController@imprimir', null],

        '/reportes' => ['ReportesController@index', 'reportes.ver'],
        '/reportes/rendimiento' => ['ReportesController@rendimiento', 'reportes.ver'],
        '/reportes/asistencia' => ['ReportesController@asistencia', 'reportes.ver'],
        // NUEVO: Rutas para API de reportes (ex-ApiController)
        '/reportes/api/estudiantes/{id}' => ['ReportesController@obtenerEstudiante', 'reportes.ver'],
        '/reportes/api/secciones/{id}/estudiantes' => ['ReportesController@estudiantesPorSeccion', 'reportes.ver'],
        // NUEVO: Ruta para exportar CSV
        '/reportes/exportar/{tipo}' => ['ReportesController@exportarCSV', 'reportes.ver'],
    ],
    'POST' => [
        '/login' => ['AuthController@autenticar', null],
        '/admin/usuarios/guardar' => ['AdminController@guardarUsuario', 'admin.usuarios.crear'],
        '/admin/usuarios/actualizar/{id}' => ['AdminController@actualizarUsuario', 'admin.usuarios.editar'],
        
        '/admin/estudiantes/guardar' => ['AdminController@guardarEstudiante', 'admin.estudiantes.crear'],
        '/admin/estudiantes/actualizar/{id}' => ['AdminController@actualizarEstudiante', 'admin.estudiantes.editar'],
        '/admin/estudiantes/inscribir' => ['AdminController@inscribirEstudiante', 'admin.estudiantes.inscribir'],
        
        '/admin/docentes/guardar' => ['AdminController@guardarDocente', 'admin.docentes.crear'],
        '/admin/docentes/actualizar/{id}' => ['AdminController@actualizarDocente', 'admin.docentes.editar'],
        
        '/admin/materias/guardar' => ['AdminController@guardarMateria', 'admin.materias.crear'],
        '/admin/materias/actualizar/{id}' => ['AdminController@actualizarMateria', 'admin.materias.editar'],
        
        '/admin/secciones/guardar' => ['AdminController@guardarSeccion', 'admin.secciones.crear'],
        '/admin/secciones/actualizar/{id}' => ['AdminController@actualizarSeccion', 'admin.secciones.editar'],
        
        '/admin/asignaciones/guardar' => ['AdminController@guardarAsignacion', 'admin.asignaciones.crear'],
        
        // FIX #2/#3: POST con {id} apuntando a métodos correctos
        '/admin/constancias/aprobar/{id}' => ['AdminController@aprobarConstancia', 'admin.constancias.aprobar'],
        '/admin/constancias/rechazar/{id}' => ['AdminController@rechazarConstancia', 'admin.constancias.aprobar'],
        
        // FIX #8: Agregar {id} al parámetro de guardarPermisos
        '/admin/permisos/guardar/{id}' => ['AdminController@guardarPermisos', 'admin.permisos.asignar'],
        
        '/admin/configuracion/guardar' => ['ConfiguracionController@guardar', 'admin.configuracion.editar'],
        '/docente/calificaciones/guardar' => ['DocenteController@guardarCalificaciones', 'docente.calificaciones.registrar'],
        '/docente/asistencia/guardar' => ['DocenteController@guardarAsistencia', 'docente.asistencia.registrar'],
        // FIX #9: guardarPlanEvaluacion → crearActividad
        '/docente/planevaluacion/guardar' => ['DocenteController@crearActividad', 'docente.planevaluacion.gestionar'],
        '/estudiante/constancias/guardar' => ['EstudianteController@guardarSolicitud', 'estudiante.constancias.solicitar'],
        // FIX #10: actualizarPerfil ahora existe en EstudianteController
        '/estudiante/perfil/actualizar' => ['EstudianteController@actualizarPerfil', 'estudiante.perfil.editar'],
        // NUEVO: API de calificaciones por período
        '/reportes/api/calificaciones' => ['ReportesController@calificacionesPorPeriodo', 'reportes.ver'],
    ]
];
