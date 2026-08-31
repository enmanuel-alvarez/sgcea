<?php
return [
    'GET' => [
        '/login' => ['AuthController@mostrarLogin', null],
        '/logout' => ['AuthController@cerrarSesion', null],
        '/admin' => ['AdminController@index', 'admin.dashboard'],
        '/admin/dashboard' => ['AdminController@index', 'admin.dashboard'],
        '/admin/usuarios' => ['AdminController@listarUsuarios', 'admin.usuarios.ver'],
        '/admin/usuarios/crear' => ['AdminController@mostrarCrearUsuario', 'admin.usuarios.crear'],
        '/admin/usuarios/editar/{id}' => ['AdminController@mostrarEditarUsuario', 'admin.usuarios.editar'],
        '/admin/usuarios/eliminar/{id}' => ['AdminController@eliminarUsuario', 'admin.usuarios.eliminar'],
        
        '/admin/estudiantes' => ['AdminController@listarEstudiantes', 'admin.estudiantes.ver'],
        '/admin/estudiantes/crear' => ['AdminController@mostrarCrearEstudiante', 'admin.estudiantes.crear'],
        '/admin/estudiantes/editar/{id}' => ['AdminController@mostrarEditarEstudiante', 'admin.estudiantes.editar'],
        '/admin/estudiantes/eliminar/{id}' => ['AdminController@eliminarEstudiante', 'admin.estudiantes.eliminar'],
        '/admin/estudiantes/inscribir/{id}' => ['AdminController@mostrarInscribirEstudiante', 'admin.estudiantes.inscribir'],
        
        '/admin/docentes' => ['AdminController@listarDocentes', 'admin.docentes.ver'],
        '/admin/docentes/crear' => ['AdminController@mostrarCrearDocente', 'admin.docentes.crear'],
        '/admin/docentes/editar/{id}' => ['AdminController@mostrarEditarDocente', 'admin.docentes.editar'],
        '/admin/docentes/eliminar/{id}' => ['AdminController@eliminarDocente', 'admin.docentes.eliminar'],
        
        '/admin/materias' => ['AdminController@listarMaterias', 'admin.materias.ver'],
        '/admin/materias/crear' => ['AdminController@mostrarCrearMateria', 'admin.materias.crear'],
        '/admin/materias/editar/{id}' => ['AdminController@mostrarEditarMateria', 'admin.materias.editar'],
        '/admin/materias/eliminar/{id}' => ['AdminController@eliminarMateria', 'admin.materias.eliminar'],
        
        '/admin/secciones' => ['AdminController@listarSecciones', 'admin.secciones.ver'],
        '/admin/secciones/crear' => ['AdminController@mostrarCrearSeccion', 'admin.secciones.crear'],
        '/admin/secciones/editar/{id}' => ['AdminController@mostrarEditarSeccion', 'admin.secciones.editar'],
        '/admin/secciones/eliminar/{id}' => ['AdminController@eliminarSeccion', 'admin.secciones.eliminar'],
        
        '/admin/asignaciones' => ['AdminController@listarAsignaciones', 'admin.asignaciones.ver'],
        '/admin/asignaciones/crear' => ['AdminController@mostrarCrearAsignacion', 'admin.asignaciones.crear'],
        '/admin/asignaciones/eliminar/{id}' => ['AdminController@eliminarAsignacion', 'admin.asignaciones.eliminar'],
        
        '/admin/constancias' => ['AdminController@gestionarConstancias', 'admin.constancias.ver'],
        '/admin/constancias/aprobar/{id}' => ['AdminController@aprobarConstancia', 'admin.constancias.aprobar'],
        '/admin/constancias/rechazar/{id}' => ['AdminController@rechazarConstancia', 'admin.constancias.aprobar'],
        
        '/admin/permisos/asignar/{id}' => ['AdminController@mostrarAsignarPermisos', 'admin.permisos.asignar'],
        
        '/admin/configuracion' => ['ConfiguracionController@index', 'admin.configuracion.ver'],
        '/admin/backup' => ['BackupController@index', 'admin.backup.ver'],
        '/admin/auditoria' => ['AdminController@listarAuditoria', 'admin.auditoria.ver'],

        '/docente' => ['DocenteController@index', 'docente.dashboard'],
        '/docente/dashboard' => ['DocenteController@index', 'docente.dashboard'],
        '/docente/perfil' => ['DocenteController@perfil', null],
        '/docente/calificaciones' => ['DocenteController@calificaciones', 'docente.calificaciones.ver'],
        '/docente/calificaciones/registrar/{id_asignacion}' => ['DocenteController@registrarCalificaciones', 'docente.calificaciones.registrar'],
        '/docente/asistencia' => ['DocenteController@asistencia', 'docente.asistencia.ver'],
        '/docente/asistencia/registrar/{id_asignacion}' => ['DocenteController@registrarAsistencia', 'docente.asistencia.registrar'],
        '/docente/planevaluacion/{id_asignacion}' => ['DocenteController@planEvaluacion', 'docente.planevaluacion.gestionar'],
        '/docente/plan-evaluacion/{id_asignacion}' => ['DocenteController@planEvaluacion', 'docente.planevaluacion.gestionar'],
        '/docente/planevaluacion/eliminar/{id}' => ['DocenteController@eliminarActividad', 'docente.planevaluacion.gestionar'],
        '/docente/plan-evaluacion/eliminar/{id}' => ['DocenteController@eliminarActividad', 'docente.planevaluacion.gestionar'],
        '/docente/revisiones' => ['DocenteController@revisiones', 'docente.calificaciones.ver'],

        '/estudiante' => ['EstudianteController@index', 'estudiante.dashboard'],
        '/estudiante/dashboard' => ['EstudianteController@index', 'estudiante.dashboard'],
        '/estudiante/boletin' => ['EstudianteController@boletin', 'estudiante.boletin.ver'],
        '/estudiante/asistencia' => ['EstudianteController@asistencia', 'estudiante.asistencia.ver'],
        '/estudiante/revisiones' => ['EstudianteController@revisiones', 'estudiante.boletin.ver'],
        '/estudiante/constancias/solicitar' => ['EstudianteController@solicitarConstancia', 'estudiante.constancias.solicitar'],
        '/estudiante/constancias/historial' => ['EstudianteController@historialConstancias', 'estudiante.constancias.ver'],
        '/estudiante/constancias/descargar/{id}' => ['EstudianteController@descargarConstancia', 'estudiante.constancias.ver'],
        '/estudiante/perfil' => ['EstudianteController@perfil', 'estudiante.perfil.ver'],
        '/constancias/imprimir/{id}' => ['ConstanciaController@imprimir', 'estudiante.constancias.ver'],

        '/carnet/estudiante' => ['CarnetController@estudiante', null],
        '/carnet/estudiante/{id}' => ['CarnetController@estudiante', null],
        '/carnet/docente' => ['CarnetController@docente', null],
        '/carnet/docente/{id}' => ['CarnetController@docente', null],

        '/reportes' => ['ReportesController@index', 'reportes.ver'],
        '/reportes/ficha360/{id}' => ['ReportesController@ficha360', 'reportes.ver'],
        '/reportes/rendimiento' => ['ReportesController@rendimiento', 'reportes.ver'],
        '/reportes/asistencia' => ['ReportesController@asistencia', 'reportes.ver'],
        '/reportes/api/estudiantes/{id}' => ['ReportesController@obtenerEstudiante', 'reportes.ver'],
        '/reportes/api/secciones/{id}/estudiantes' => ['ReportesController@estudiantesPorSeccion', 'reportes.ver'],
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
        
        '/admin/constancias/aprobar/{id}' => ['AdminController@aprobarConstancia', 'admin.constancias.aprobar'],
        '/admin/constancias/rechazar/{id}' => ['AdminController@rechazarConstancia', 'admin.constancias.aprobar'],
        
        '/admin/permisos/guardar/{id}' => ['AdminController@guardarPermisos', 'admin.permisos.asignar'],
        
        '/admin/configuracion/guardar' => ['ConfiguracionController@guardar', 'admin.configuracion.editar'],
        '/admin/configuracion/reiniciar' => ['ConfiguracionController@reiniciarSistema', 'admin.configuracion.reiniciar'],
        
        '/admin/backup/exportar' => ['BackupController@exportar', 'admin.backup.exportar'],
        '/admin/backup/importar' => ['BackupController@importar', 'admin.backup.importar'],

        '/docente/calificaciones/guardar' => ['DocenteController@guardarCalificaciones', 'docente.calificaciones.registrar'],
        '/docente/asistencia/guardar' => ['DocenteController@guardarAsistencia', 'docente.asistencia.registrar'],
        '/docente/planevaluacion/guardar' => ['DocenteController@crearActividad', 'docente.planevaluacion.gestionar'],
        '/docente/planevaluacion/guardar-lote' => ['DocenteController@crearActividadesLote', 'docente.planevaluacion.gestionar'],
        '/docente/revisiones/responder' => ['DocenteController@responderRevision', 'docente.calificaciones.registrar'],
        '/docente/perfil/actualizar' => ['DocenteController@actualizarPerfil', null],
        '/estudiante/revisiones/guardar' => ['EstudianteController@guardarRevision', 'estudiante.boletin.ver'],
        '/estudiante/constancias/guardar' => ['EstudianteController@guardarSolicitud', 'estudiante.constancias.solicitar'],
        '/estudiante/perfil/actualizar' => ['EstudianteController@actualizarPerfil', 'estudiante.perfil.editar'],
        '/reportes/api/calificaciones' => ['ReportesController@calificacionesPorPeriodo', 'reportes.ver'],
        '/cambiar-password' => ['AuthController@cambiarPassword', null],
    ]
];


