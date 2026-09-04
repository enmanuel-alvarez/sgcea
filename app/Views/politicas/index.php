<?php
/**
 * Vista: Políticas de Uso del Sistema SGCEA
 * Presentación oficial interactiva para todos los perfiles de usuario.
 */
?>

<!-- Header de Sección -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-3xl text-white shadow-xl border border-slate-800">
    <div class="space-y-1">
        <div class="flex items-center space-x-3">
            <span class="p-2.5 bg-amber-500/20 text-amber-400 rounded-2xl border border-amber-400/30">
                <i class="bi bi-shield-lock-fill text-2xl"></i>
            </span>
            <div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">POLÍTICAS Y NORMATIVA DE USO DEL SISTEMA</h1>
                <p class="text-xs text-slate-300 font-medium">Unidad Educativa Institucional • Plataforma SGCEA v3.0</p>
            </div>
        </div>
    </div>
    <div class="flex items-center space-x-3 shrink-0">
        <a href="javascript:window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition-all flex items-center space-x-2">
            <i class="bi bi-printer"></i>
            <span>Imprimir Políticas</span>
        </a>
    </div>
</div>

<!-- Tarjetas Resumen de Principios -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-md">
        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl mb-3">
            <i class="bi bi-key-fill"></i>
        </div>
        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mb-1">Acceso y Credenciales</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Cuentas estrictamente personales e intransferibles. Obligatoriedad de claves seguras y 2FA en personal autorizado.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-md">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl mb-3">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mb-1">Modelo RBAC Híbrido</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Seguridad por roles base más matriz de excepciones directas (+ Conceder / - Revocar) monitoreadas por enrutador.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-md">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mb-3">
            <i class="bi bi-journal-check"></i>
        </div>
        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mb-1">Integridad Académica</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Toda nota o asistencia registrada constituye una Declaración Jurada Institucional. Falsificaciones sancionadas.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-md">
        <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl mb-3">
            <i class="bi bi-card-heading"></i>
        </div>
        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white mb-1">Carnet Institucional</h3>
        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Documento de identificación oficial con código único QR. Portación obligatoria para trámites y evaluaciones.</p>
    </div>
</div>

<!-- CONTENIDO COMPLETO DE CAPÍTULOS -->
<div class="space-y-6">

    <!-- CAPÍTULO 1 -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-md border border-slate-200 dark:border-slate-700">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center space-x-3 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <span class="px-2.5 py-1 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-mono">01</span>
            <span>OBJETIVO Y ÁMBITO DE APLICACIÓN</span>
        </h2>
        <div class="text-xs text-slate-700 dark:text-slate-300 space-y-3 leading-relaxed">
            <p>
                El presente reglamento establece los términos y condiciones de uso obligatorio del <strong>Sistema de Gestión y Control Educativo - Académico (SGCEA)</strong>. Se aplica de manera vinculante a toda la comunidad educativa: personal directivo, administrativo, docente, estudiantes y representantes legales.
            </p>
            <p class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border-l-4 border-blue-500 font-medium">
                La utilización de cualquier módulo del SGCEA confirma la lectura, comprensión y aceptación incondicional de estas políticas.
            </p>
        </div>
    </div>

    <!-- CAPÍTULO 2 -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-md border border-slate-200 dark:border-slate-700">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center space-x-3 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <span class="px-2.5 py-1 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 text-xs font-mono">02</span>
            <span>AUTENTICACIÓN, CREDENCIALES Y SEGURIDAD</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-700 dark:text-slate-300">
            <div class="space-y-2 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                <h4 class="font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                    <i class="bi bi-shield-check text-indigo-500"></i>
                    <span>2.1 Confidencialidad de Cuentas</span>
                </h4>
                <p>Las credenciales asignadas (usuario, contraseña y 2FA) son de uso estrictamente confidencial. Se prohíbe el préstamo, transferencia o divulgación de acceso a terceros bajo cualquier circunstancia.</p>
            </div>

            <div class="space-y-2 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                <h4 class="font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                    <i class="bi bi-lock-fill text-indigo-500"></i>
                    <span>2.2 Estándares de Seguridad y 2FA</span>
                </h4>
                <p>El personal administrativo y docente debe activar el Factor de Doble Autenticación (TOTP). El sistema aplica suspensión temporal tras 5 intentos fallidos consecutivos (Rate Limiting).</p>
            </div>
        </div>
    </div>

    <!-- CAPÍTULO 3 -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-md border border-slate-200 dark:border-slate-700">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center space-x-3 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <span class="px-2.5 py-1 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300 text-xs font-mono">03</span>
            <span>MODELO DE GESTIÓN DE PERMISOS (HYBRID RBAC)</span>
        </h2>
        <div class="space-y-3 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
            <p>SGCEA opera con un control de acceso basado en roles híbrido que combina la asignación de perfil institucional con matriz de excepciones específicas:</p>
            <ul class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 font-medium">
                <li class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-800">
                    <strong class="text-blue-700 dark:text-blue-300 block mb-1">👑 Rol Administrador (ID 1):</strong>
                    Acceso integral al sistema, auditoría, respaldos y gestor de excepciones de permisos.
                </li>
                <li class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200 dark:border-indigo-800">
                    <strong class="text-indigo-700 dark:text-indigo-300 block mb-1">👨‍🏫 Rol Docente (ID 2):</strong>
                    Gestión académica de asignaciones, carga de planes de evaluación, calificaciones y asistencias.
                </li>
                <li class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <strong class="text-emerald-700 dark:text-emerald-300 block mb-1">🎓 Rol Estudiante (ID 3):</strong>
                    Consulta de boletines, historial de asistencia, solicitud de constancias y carnets.
                </li>
            </ul>
        </div>
    </div>

    <!-- CAPÍTULO 4 -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-md border border-slate-200 dark:border-slate-700">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center space-x-3 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <span class="px-2.5 py-1 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-xs font-mono">04</span>
            <span>PROTECCIÓN DE DATOS Y PRIVACIDAD (LOPNNA)</span>
        </h2>
        <div class="text-xs text-slate-700 dark:text-slate-300 space-y-3 leading-relaxed">
            <p>
                Los datos personales, académicos y de salud de los estudiantes están resguardados en estricto cumplimiento con la normativa de protección integral de niños, niñas y adolescentes. Se prohíbe la extracción indebida o divulgación no autorizada de listados escolares.
            </p>
        </div>
    </div>

    <!-- CAPÍTULO 5 -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-md border border-slate-200 dark:border-slate-700">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center space-x-3 border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">
            <span class="px-2.5 py-1 rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300 text-xs font-mono">05</span>
            <span>RÉGIMEN DISCIPLINARIO Y AUDITORÍA</span>
        </h2>
        <div class="text-xs text-slate-700 dark:text-slate-300 space-y-3 leading-relaxed">
            <p>
                Todas las operaciones de creación, modificación o supresión de datos son registradas automáticamente en la <strong>Bitácora de Auditoría</strong> con marca de tiempo e IP. Toda falta ética, suplantación o intento de vulneración derivará en la revocación inmediata de permisos y sanciones administrativas o legales.
            </p>
        </div>
    </div>

</div>
