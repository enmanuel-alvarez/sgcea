# SGCEA - Sistema de Gestión y Control Escolar-Académico

## Descripción

Sistema web para la gestión integral de instituciones educativas, desarrollado con PHP 8.x, MySQL y arquitectura MVC.

## Requisitos del Sistema

- **PHP:** 8.0 o superior
- **MySQL:** 5.7 o superior (motor InnoDB)
- **Apache:** 2.4+ con mod_rewrite habilitado
- **Extensiones PHP requeridas:** pdo, pdo_mysql, mbstring, json

## Estructura del Proyecto

```
/sgcea/
├── bootstrap/          # Inicialización de la aplicación
├── config/             # Archivos de configuración
├── core/               # Núcleo del framework (Router, Database, Security, etc.)
├── public/             # DocumentRoot del servidor web
│   ├── assets/         # CSS, JS, imágenes
│   └── index.php       # Front Controller
├── app/                # Lógica de la aplicación
│   ├── Controllers/    # Controladores
│   ├── Models/         # Repositorios y Servicios
│   └── Views/          # Vistas HTML
├── storage/            # Logs y sesiones
└── database.sql        # Script de base de datos
```

## Instalación

### 1. Clonar o copiar el proyecto

```bash
cd /var/www/html
# Copiar archivos del proyecto
```

### 2. Configurar base de datos

```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE sgcea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar script SQL
mysql -u root -p sgcea < database.sql
```

### 3. Configurar credenciales

```bash
cd config
cp database.template.php database.php
# Editar database.php con las credenciales correctas
```

### 4. Configurar Apache

Crear un VirtualHost o usar el .htaccess incluido:

```apache
<VirtualHost *:80>
    ServerName sgcea.local
    DocumentRoot /var/www/html/sgcea/public
    
    <Directory /var/www/html/sgcea/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Permisos de carpetas

```bash
chmod -R 755 /var/www/html/sgcea
chmod -R 777 /var/www/html/sgcea/storage/logs
chmod -R 777 /var/www/html/sgcea/storage/sessions
```

### 6. Acceder al sistema

Abrir navegador e ir a: `http://localhost/sgcea/public`

**Credenciales por defecto:**
- Email: admin@sgcea.com
- Contraseña: admin123

## Uso del Sistema

### Módulos disponibles

#### Administrador
- Dashboard con estadísticas generales
- Gestión de usuarios, estudiantes, docentes
- Gestión de materias, secciones, asignaciones
- Aprobación de constancias
- Asignación de permisos ACL
- Configuración del sistema

#### Docente
- Dashboard con sus asignaciones
- Registro de calificaciones
- Control de asistencia
- Gestión de plan de evaluación

#### Estudiante
- Dashboard personal
- Consulta de boletín de calificaciones
- Historial de asistencia
- Solicitud de constancias
- Perfil personal

## Seguridad

El sistema incluye:
- Hash de contraseñas con bcrypt
- Protección CSRF en formularios
- Rate limiting en login (5 intentos/15 min)
- Headers de seguridad HTTP
- Sanitización de entradas
- Control de acceso basado en permisos (ACL)
- Auditoría de acciones críticas

## Desarrollo

### Agregar nuevos controladores

```php
namespace Src\Controllers;

class MiController extends Controller
{
    public function index()
    {
        $this->render('mi/vista', ['datos' => $valor]);
    }
}
```

### Agregar nuevas rutas

Editar `config/routes.php`:

```php
'GET' => [
    '/mi/ruta' => ['MiController@index', 'permiso.requerido'],
],
```

### Agregar permisos

Insertar en tabla `permisos`:

```sql
INSERT INTO permisos (nombre, descripcion, modulo) 
VALUES ('mi.permiso', 'Descripción', 'modulo');
```

## Soporte

Para reportar errores o solicitar funcionalidades, contactar al equipo de desarrollo.

## Licencia

Propiedad exclusiva de la institución educativa.
