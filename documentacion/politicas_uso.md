# POLÍTICAS DE USO DEL SISTEMA SGCEA
**Sistema de Gestión y Control Educativo - Académico**
*Unidad Educativa Institucional*

---

## 1. OBJETIVO Y ÁMBITO DE APLICACIÓN

El presente documento establece los términos, condiciones y políticas de uso obligatorio que rigen la utilización del **Sistema de Gestión y Control Educativo - Académico (SGCEA)**.

Estas políticas se aplican a la totalidad de la comunidad educativa, incluyendo:
- **Personal Administrativo y Directivo**
- **Personal Docente y de Coordinación**
- **Estudiantes Matriculados**
- **Padres, Madres y Representantes Legales**

El acceso, navegación o interacción con la plataforma implica la aceptación plena e incondicional de todas las normas aquí estipuladas.

---

## 2. AUTENTICACIÓN, CREDENCIALES Y SEGURIDAD

### 2.1 Confidencialidad de Cuentas
- Cada usuario es el único responsable de mantener la estricta confidencialidad de su nombre de usuario, clave de acceso y secretos de Autenticación de Doble Factor (2FA).
- Queda rotundamente prohibido compartir, ceder, transferir o alquilar credenciales de acceso a terceros, independientemente del vínculo familiar, personal o laboral.

### 2.2 Requisitos de Contraseñas y Doble Factor (2FA)
- Las contraseñas deben cumplir con los estándares de seguridad establecidos por el sistema (mínimo 8 caracteres, combinación de letras, números y símbolos).
- El personal docente y administrativo debe mantener activo y configurado el sistema de Autenticación de Doble Factor (TOTP / Google Authenticator) en sus dispositivos móviles autorizados.

### 2.3 Notificación de Vulnerabilidades e Incidentes
- Cualquier sospecha de acceso no autorizado, vulnerabilidad técnica o extravío de credenciales debe ser reportada de manera inmediata a la Administración del Sistema.

---

## 3. GESTIÓN DE PERMISOS E IDENTIDAD (MODELO RBAC HÍBRIDO)

SGCEA opera mediante un **Gestor de Permisos de Control de Acceso basado en Roles Híbrido (Hybrid RBAC)**:

1. **Roles Base del Sistema:**
   - **Administrador (Rol 1):** Acceso integral y gestión global de la plataforma, auditoría y configuraciones del plantel.
   - **Docente (Rol 2):** Gestión académica de secciones asignadas, carga de calificaciones, registro de asistencia y atención a revisiones de notas.
   - **Estudiante (Rol 3):** Consulta de calificaciones, récord de asistencia, solicitud de constancias digitales y revisiones académicas.

2. **Excepciones Granulares por Usuario (`CONCEDER` / `REVOCAR`):**
   - La Administración del Sistema puede otorgar o revocar permisos específicos a un usuario de forma directa sin alterar la matriz base de su rol.
   - Todo intento de eludir o sobrepasar los permisos asignados será detectado por el motor de enrutamiento y registrado automáticamente en la Bitácora de Auditoría.

---

## 4. USO ACEPTABLE Y ÉTICA ACADÉMICA

### 4.1 Carga de Calificaciones y Asistencias
- El personal docente está obligado a registrar las calificaciones y asistencias dentro de los lapsos académicos establecidos por la Dirección Institucional.
- Toda modificación extemporánea de notas requiere la autorización explícita de la Coordinación Académica.

### 4.2 Integridad y Veracidad de la Información
- Toda la información ingresada al sistema (datos personales, notas, reportes de conducta) posee carácter de **Declaración Jurada Institucional**.
- Queda estrictamente prohibida la alteración fraudulenta, falsificación o supresión maliciosa de datos académicos o administrativos.

### 4.3 Uso Razonable del Sistema (Rate Limiting)
- El sistema cuenta con mecanismos automáticos de protección contra ataques de fuerza bruta y peticiones masivas.
- Queda prohibida la ejecución de scripts, bots o herramientas automatizadas de scraping sobre las rutas del SGCEA.

---

## 5. PROTECCIÓN DE DATOS PERSONALES Y PRIVACIDAD

1. **Custodia de Información Estudiantil:**
   - Los datos personales, de contacto y récords académicos de los estudiantes están protegidos de acuerdo con la legislación vigente de protección a la infancia y adolescencia (LOPNNA).
2. **Confidencialidad Médica y Familiar:**
   - Los expedientes y números de emergencia de representantes son de uso exclusivo para coordinaciones autorizadas y situaciones de contingencia.
3. **Uso de Fotografías e Imagen:**
   - Las fotografías registradas en los perfiles de usuarios se utilizarán exclusivamente para la emisión del Carnet Oficial y la verificación de identidad dentro del plantel.

---

## 6. CREDENCIALES FÍSICAS Y CARNET INSTITUCIONAL

1. **Carácter Oficial:**
   - El Carnet Estudiantil / Docente generado por el sistema es el único documento oficial de identificación dentro de las instalaciones del plantel.
2. **Impresión y Formato:**
   - Debe imprimirse en papel de alta resolución o tarjeta CR80 plástica, manteniendo visibles e inalterados el código de validación QR/barcode y las firmas autorizadas.
3. **Uso Obligatorio:**
   - Es obligatorio portar el carnet para el ingreso a la institución, presentación de evaluaciones y solicitud de servicios de biblioteca o constancias.
4. **Pérdida o Extravío:**
   - El extravío del carnet debe ser reportado inmediatamente a la Coordinación para la anulación del código anterior y la emisión de una nueva credencial.

---

## 7. AUDITORÍA Y RÉGIMEN DISCIPLINARIO

### 7.1 Trazabilidad y Bitácora de Auditoría
- El sistema registra automáticamente la fecha, hora, dirección IP y acción específica de cada interacción realizada por los usuarios (creación, edición, eliminación, inicio de sesión).

### 7.2 Sanciones y Medidas Disciplinarias
El incumplimiento de las presentes políticas de uso dará lugar a las siguientes acciones, según la gravedad de la falta:
1. **Amonestación Escrita:** Por faltas leves como negligencia reiterada en el resguardo de credenciales.
2. **Revocación de Permisos:** Desactivación temporal o definitiva de módulos específicos en el sistema.
3. **Bloqueo / Inactivación de Cuenta:** Suspensión inmediata del acceso a la plataforma.
4. **Procedimiento Disciplinario Institucional / Legal:** En caso de alteración fraudulenta de notas, suplantación de identidad o ataques informáticos.

---

*Aprobado por el Consejo Directivo Institucional y la Coordinación de Tecnología y Sistemas del SGCEA.*
