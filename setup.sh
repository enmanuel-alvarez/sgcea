#!/usr/bin/env bash
# ==============================================================================
# SGCEA - Sistema de Gestión de Control de Estudios Académicos
# Script de Instalación Automatizada y Configuración del Sistema
# ==============================================================================

set -eo pipefail

# ------------------------------------------------------------------------------
# Colores y Estilos para Consola
# ------------------------------------------------------------------------------
BOLD='\033[1m'
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

show_banner() {
    echo -e "${CYAN}${BOLD}"
    echo "========================================================================"
    echo "       SGCEA - SISTEMA DE GESTIÓN DE CONTROL DE ESTUDIOS ACADÉMICOS"
    echo "                      Instalación y Configuración"
    echo "========================================================================"
    echo -e "${NC}"
}

show_message() {
    echo -e "${GREEN}[OK]${NC} $1"
}

show_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

show_warning() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

show_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# Banner inicial
show_banner

# ------------------------------------------------------------------------------
# Paso 1: Detección y Verificación del Entorno PHP
# ------------------------------------------------------------------------------
echo -e "${BOLD}Paso 1: Verificando Entorno PHP...${NC}"

PHP_BIN=""
if command -v php &> /dev/null; then
    PHP_BIN="php"
elif [ -f "/Applications/MAMP/bin/php/php8.3.30/bin/php" ]; then
    PHP_BIN="/Applications/MAMP/bin/php/php8.3.30/bin/php"
elif [ -f "/Applications/MAMP/bin/php/php8.2.0/bin/php" ]; then
    PHP_BIN="/Applications/MAMP/bin/php/php8.2.0/bin/php"
fi

if [ -n "$PHP_BIN" ]; then
    PHP_VER=$($PHP_BIN -r 'echo PHP_VERSION;')
    show_message "PHP detectado ($PHP_BIN): v$PHP_VER"
else
    show_error "PHP no fue encontrado en el sistema ni en MAMP. Por favor instale PHP 8.0+."
    exit 1
fi

# ------------------------------------------------------------------------------
# Paso 2: Verificación de Extensiones PHP Requeridas
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}Paso 2: Verificando Extensiones PHP Requeridas...${NC}"
REQUIRED_EXTS=("pdo" "pdo_mysql" "mbstring" "json")
MISSING_EXTS=0

for ext in "${REQUIRED_EXTS[@]}"; do
    if $PHP_BIN -m | grep -qi "^$ext$"; then
        show_message "Extensión PHP disponible: $ext"
    else
        show_error "Extensión PHP faltante: $ext"
        MISSING_EXTS=$((MISSING_EXTS + 1))
    fi
done

if [ $MISSING_EXTS -gt 0 ]; then
    show_error "Faltan $MISSING_EXTS extensiones PHP necesarias. Instálelas antes de continuar."
    exit 1
fi

# ------------------------------------------------------------------------------
# Paso 3: Configuración del Archivo de Base de Datos (config/database.php)
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}Paso 3: Configurando Archivo de Credenciales (config/database.php)...${NC}"
CONFIG_FILE="config/database.php"
TEMPLATE_FILE="config/database.template.php"

if [ ! -f "$CONFIG_FILE" ]; then
    if [ -f "$TEMPLATE_FILE" ]; then
        cp "$TEMPLATE_FILE" "$CONFIG_FILE"
        show_message "Archivo $CONFIG_FILE creado exitosamente desde plantilla."
    fi

    echo ""
    read -p "¿Desea ingresar las credenciales de MySQL ahora? (s/N): " -n 1 -r REPLY_CONF
    echo
    if [[ $REPLY_CONF =~ ^[Ss]$ ]]; then
        read -p "Host de MySQL [localhost]: " DB_HOST
        read -p "Nombre de la Base de Datos [sgcea]: " DB_NAME
        read -p "Usuario de MySQL [root]: " DB_USER
        read -sp "Contraseña de MySQL: " DB_PASS
        echo

        DB_HOST=${DB_HOST:-localhost}
        DB_NAME=${DB_NAME:-sgcea}
        DB_USER=${DB_USER:-root}

        cat > "$CONFIG_FILE" << EOF
<?php
/**
 * Configuración de la base de datos SGCEA
 */

return [
    'host' => '$DB_HOST',
    'database' => '$DB_NAME',
    'username' => '$DB_USER',
    'password' => '$DB_PASS',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];
EOF
        show_message "Archivo $CONFIG_FILE actualizado correctamente."
    else
        show_warning "Recuerde editar $CONFIG_FILE manualmente antes de ejecutar la aplicación."
    fi
else
    show_message "El archivo $CONFIG_FILE ya existe."
fi

# ------------------------------------------------------------------------------
# Paso 4: Detección e Importación de Base de Datos MySQL
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}Paso 4: Inicialización de la Base de Datos...${NC}"

MYSQL_BIN=""
if command -v mysql &> /dev/null; then
    MYSQL_BIN="mysql"
elif [ -f "/Applications/MAMP/Library/bin/mysql" ]; then
    MYSQL_BIN="/Applications/MAMP/Library/bin/mysql"
elif [ -f "/usr/local/mysql/bin/mysql" ]; then
    MYSQL_BIN="/usr/local/mysql/bin/mysql"
fi

read -p "¿Desea crear e importar el esquema inicial de la base de datos ahora? (s/N): " -n 1 -r REPLY_DB
echo
if [[ $REPLY_DB =~ ^[Ss]$ ]]; then
    if [ -z "$MYSQL_BIN" ]; then
        show_error "El ejecutable de MySQL no fue encontrado en PATH. Instale o configure MySQL/MAMP."
    else
        read -p "Usuario administrador MySQL [root]: " MYSQL_USER
        read -sp "Contraseña de MySQL: " MYSQL_PASS
        echo
        MYSQL_USER=${MYSQL_USER:-root}

        DB_NAME_TARGET="sgcea"
        if [ -f "$CONFIG_FILE" ]; then
            EXTRACTED_NAME=$(grep "'database'" "$CONFIG_FILE" | cut -d"'" -f4 || true)
            if [ -n "$EXTRACTED_NAME" ]; then
                DB_NAME_TARGET="$EXTRACTED_NAME"
            fi
        fi

        # Modificadores para socket MAMP si existe
        MYSQL_CONN_ARGS=("-u$MYSQL_USER")
        if [ -n "$MYSQL_PASS" ]; then
            MYSQL_CONN_ARGS+=("-p$MYSQL_PASS")
        fi
        if [ -S "/Applications/MAMP/tmp/mysql/mysql.sock" ]; then
            MYSQL_CONN_ARGS+=("-S" "/Applications/MAMP/tmp/mysql/mysql.sock")
        fi

        show_info "Creando base de datos '$DB_NAME_TARGET'..."
        if "$MYSQL_BIN" "${MYSQL_CONN_ARGS[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME_TARGET\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null; then
            show_message "Base de datos '$DB_NAME_TARGET' creada/verificada."

            if [ -f "database.sql" ]; then
                show_info "Importando esquema y datos iniciales (database.sql)..."
                if "$MYSQL_BIN" "${MYSQL_CONN_ARGS[@]}" "$DB_NAME_TARGET" < database.sql 2>/dev/null; then
                    show_message "Esquema de tablas y seeders iniciales importados con éxito."
                else
                    show_error "Falló la importación del archivo database.sql."
                fi
            else
                show_warning "No se encontró el archivo database.sql en el directorio actual."
            fi
        else
            show_error "No se pudo conectar a MySQL con el usuario e información provista."
        fi
    fi
fi

# ------------------------------------------------------------------------------
# Paso 5: Estructura de Almacenamiento y Permisos
# ------------------------------------------------------------------------------
echo ""
echo -e "${BOLD}Paso 5: Configurando Directorios y Permisos de Almacenamiento...${NC}"

mkdir -p storage/logs
mkdir -p storage/sessions
mkdir -p public/uploads

chmod -R 755 .
chmod -R 777 storage/logs
chmod -R 777 storage/sessions
chmod -R 777 public/uploads

touch storage/logs/.gitkeep
touch storage/sessions/.gitkeep
touch public/uploads/.gitkeep

show_message "Directorios storage/logs, storage/sessions y public/uploads listos con permisos 777."

# ------------------------------------------------------------------------------
# Resumen Final e Instrucciones de Uso
# ------------------------------------------------------------------------------
echo ""
echo -e "${CYAN}${BOLD}"
echo "========================================================================"
echo "                   ¡INSTALACIÓN DE SGCEA COMPLETADA!"
echo "========================================================================"
echo -e "${NC}"

echo -e "${BOLD}Credenciales de Acceso Administrador por Defecto:${NC}"
echo -e "  - ${BOLD}Correo:${NC}     admin@sgcea.com"
echo -e "  - ${BOLD}Contraseña:${NC} admin123"
echo ""
echo -e "${BOLD}Instrucciones de Acceso:${NC}"
echo -e "  1. Inicie su servidor web (Apache + MySQL / MAMP)."
echo -e "  2. Visite en su navegador: ${CYAN}http://localhost/sgcea/public${NC}"
echo ""
echo -e "${YELLOW}Recomendaciones de Seguridad:${NC}"
echo "  - Modifique la contraseña del usuario Administrador inmediatamente al ingresar."
echo "  - Verifique que la carpeta public/ sea la única expuesta como DocumentRoot en producción."
echo ""
show_message "SGCEA está listo para ser utilizado."

