# SGCEA - Sistema de Gestión y Control Escolar-Académico 🎓

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-5.7%2B%20%7C%208.0%2B-4479A1?style=flat-square&logo=mysql)](https://www.mysql.com/)
[![Architecture](https://img.shields.io/badge/Architecture-Vanilla%20MVC-brightgreen?style=flat-square)](#-arquitectura-del-sistema)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

**SGCEA** es una solución web integral diseñada para la automatización, administración y control de procesos escolares y académicos en instituciones educativas (Educación Primaria, Secundaria y Bachillerato).

El sistema permite gestionar de forma eficiente usuarios, roles con matriz de permisos (**ACL**), docentes, estudiantes, materias, secciones, planes de evaluación, calificaciones, control diario de asistencias, emisión automatizada de constancias oficiales y registro de auditoría.

---

## 🌟 Características Principales

- 👑 **Módulo Administrador**: Dashboard con métricas globales, gestión de usuarios, asignación dinámica de permisos (ACL), materias, secciones, grados, solicitudes de constancia, respaldos (exportar/importar) y configuración del sistema.
- 👨‍🏫 **Módulo Docente**: Gestión de asignaciones de cátedra, creación y ponderación de planes de evaluación por lapso, carga de calificaciones cuantitativas y registro masivo de asistencias por fecha.
- 🎓 **Módulo Estudiante**: Consulta de boletines de calificaciones, desglose por lapsos, historial de asistencias, actualización de perfil y autoservicio de solicitud y descarga de constancias (estudio, conducta, notas).
- 🛡️ **Seguridad Avanzada**: Autenticación segura (hashes BCRYPT), protección contra ataques XSS y CSRF, *Rate Limiting* contra fuerza bruta en login, preparado de consultas SQL con PDO e inyección limpia de variables de entorno mediante `.env`.

---

## 📋 Requisitos del Sistema

- **Servidor Web**: Apache / Nginx (MAMP, XAMPP, Laragon o servidor Linux).
- **Lenguaje**: PHP 8.0 o superior (con extensiones `pdo`, `pdo_mysql`, `mbstring`, `json`, `session`).
- **Base de Datos**: MySQL 5.7+ / MariaDB 10.3+.

---

## 🚀 Guía de Instalación Paso a Paso

### 1. Clonar o descargar el repositorio
Ubique el proyecto en el directorio raíz de su servidor web (ej: `/Applications/MAMP/htdocs/sgcea` o `C:/xampp/htdocs/sgcea`):
```bash
git clone https://github.com/tu-usuario/sgcea.git
cd sgcea
```

### 2. Configurar el archivo de Variables de Entorno (`.env`)
Copie la plantilla `.env.example` para crear su archivo de configuración `.env`:
```bash
cp .env.example .env
```
Edite el archivo `.env` ajustando los valores de su entorno local o servidor:
```env
# Aplicación
APP_NAME="SGCEA"
APP_ENV=development
APP_DEBUG=true
APP_URL="http://localhost/sgcea/public"

# Base de Datos MySQL
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sgcea
DB_USER=root
DB_PASS=root
```

### 3. Importar la Base de Datos
1. Inicie su servidor MySQL (ej: MAMP / XAMPP / MySQL Service).
2. Cree la base de datos vacía `sgcea`:
   ```sql
   CREATE DATABASE sgcea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Importe el esquema DDL y la data base indispensable ([database.sql](file:///Applications/MAMP/htdocs/sgcea/database.sql)):
   ```bash
   mysql -u root -p sgcea < database.sql
   ```
4. **(Opcional para pruebas)**: Si desea cargar datos de demostración (docentes, estudiantes, materias, evaluaciones y constancias de prueba), ejecute el archivo ([seed.sql](file:///Applications/MAMP/htdocs/sgcea/seed.sql)):
   ```bash
   mysql -u root -p sgcea < seed.sql
   ```

### 4. Abrir la Aplicación
Navegue desde su navegador a:
`http://localhost/sgcea/public` (o la ruta configurada en su entorno).

---

## 🔑 Credenciales por Defecto

### 👑 Usuario Administrador (Creado por `database.sql`)
- **Email**: `admin@sgcea.com`
- **Contraseña**: `admin123`

### 👨‍🏫 Usuarios de Prueba (Cargados por `seed.sql`)
- **Docente**: `carlos@docente.com` / `password123`
- **Estudiante**: `juan@estudiante.com` / `password123`

---

## 📂 Estructura del Proyecto

```
sgcea/
├── app/                        # Aplicación principal (MVC)
│   ├── Controllers/            # Controladores del sistema
│   ├── Models/                 # Capa de Modelo (Services y Repositories)
│   └── Views/                  # Vistas PHP nativas y layouts
├── config/                     # Configuraciones (app, database, rutas, inicializador)
├── core/                       # Núcleo del Framework (Router, Controller, Database, Env, Security)
├── documentacion/              # Manuales completos de Usuario y Técnico
│   ├── manual_usuario.md       # Manual operativo para Admin, Docente y Estudiante
│   └── manual_tecnico.md       # Documentación de arquitectura y desarrollador
├── public/                     # Punto de entrada público (index.php, assets CSS/JS)
├── storage/                    # Almacenamiento de logs, sesiones y respaldos
├── .env                        # Archivo de credenciales (ignorado en git)
├── .env.example                # Plantilla de variables de entorno
├── database.sql                # Estructura DDL limpia y datos base del sistema
├── seed.sql                    # Datos de prueba de demostración (seeding)
└── README.md                   # Documento principal del proyecto
```

---

## 📚 Documentación Adicional

Para más información y detalle sobre el funcionamiento del sistema, consulte los manuales alojados en el directorio `documentacion/`:

- 📘 [Manual de Usuario](file:///Applications/MAMP/htdocs/sgcea/documentacion/manual_usuario.md): Guía de uso para Administradores, Docentes y Estudiantes.
- 📙 [Manual Técnico y de Desarrollador](file:///Applications/MAMP/htdocs/sgcea/documentacion/manual_tecnico.md): Guía de arquitectura MVC, explicación de clases core, servicios, repositorios, mapa de rutas y esquema de base de datos.

---

**SGCEA v1.0 Release** • *Sistema comprobado y 100% listo para despliegue en entornos de producción.*


