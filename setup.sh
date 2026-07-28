#!/bin/bash
# ============================================
# Script de Instalación Automatizada SGCEA
# ============================================

echo "============================================"
echo "  SGCEA - Sistema de Gestión Escolar"
echo "  Script de Instalación"
echo "============================================"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
show_message() {
    echo -e "${GREEN}[OK]${NC} $1"
}

show_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

show_warning() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

# Verificar que se está ejecutando como root o con permisos adecuados
if [ "$EUID" -ne 0 ]; then 
    show_warning "Algunas operaciones pueden requerir permisos de administrador"
fi

# Paso 1: Verificar PHP
echo ""
echo "Paso 1: Verificando PHP..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    show_message "PHP instalado: $PHP_VERSION"
else
    show_error "PHP no está instalado. Por favor instale PHP 8.0 o superior."
    exit 1
fi

# Paso 2: Verificar extensiones PHP requeridas
echo ""
echo "Paso 2: Verificando extensiones PHP..."
REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "mbstring" "json")
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -qi "$ext"; then
        show_message "Extensión $ext disponible"
    else
        show_error "Extensión $ext no encontrada"
    fi
done

# Paso 3: Crear archivo de configuración de base de datos
echo ""
echo "Paso 3: Configurando archivo de base de datos..."
CONFIG_FILE="config/database.php"
TEMPLATE_FILE="config/database.template.php"

if [ ! -f "$CONFIG_FILE" ]; then
    if [ -f "$TEMPLATE_FILE" ]; then
        cp "$TEMPLATE_FILE" "$CONFIG_FILE"
        show_message "Archivo $CONFIG_FILE creado desde plantilla"
        echo ""
        echo "IMPORTANTE: Edite $CONFIG_FILE y configure:"
        echo "  - host (por defecto: localhost)"
        echo "  - database (por defecto: sgcea)"
        echo "  - username (por defecto: root)"
        echo "  - password (su contraseña de MySQL)"
        echo ""
        read -p "¿Desea configurar las credenciales ahora? (s/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            read -p "Host de MySQL [localhost]: " DB_HOST
            read -p "Nombre de la base de datos [sgcea]: " DB_NAME
            read -p "Usuario de MySQL [root]: " DB_USER
            read -sp "Contraseña de MySQL: " DB_PASS
            echo
            
            DB_HOST=${DB_HOST:-localhost}
            DB_NAME=${DB_NAME:-sgcea}
            DB_USER=${DB_USER:-root}
            
            cat > "$CONFIG_FILE" << EOF
<?php
/**
 * Configuración de la base de datos
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
            show_message "Archivo de configuración actualizado"
        fi
    else
        show_error "Plantilla $TEMPLATE_FILE no encontrada"
        exit 1
    fi
else
    show_message "Archivo $CONFIG_FILE ya existe"
fi

# Paso 4: Crear base de datos y tablas
echo ""
echo "Paso 4: Configurando base de datos..."
read -p "¿Desea crear/configurar la base de datos ahora? (s/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    read -p "Usuario de MySQL: " MYSQL_USER
    read -sp "Contraseña de MySQL: " MYSQL_PASS
    echo
    
    # Leer configuración del archivo
    DB_NAME=$(grep "'database'" "$CONFIG_FILE" | cut -d"'" -f4)
    DB_NAME=${DB_NAME:-sgcea}
    
    echo ""
    echo "Creando base de datos $DB_NAME..."
    
    mysql -u"$MYSQL_USER" -p"$MYSQL_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        show_message "Base de datos creada/existe"
        
        echo "Importando script SQL..."
        mysql -u"$MYSQL_USER" -p"$MYSQL_PASS" "$DB_NAME" < database.sql 2>/dev/null
        
        if [ $? -eq 0 ]; then
            show_message "Tablas e datos iniciales importados correctamente"
        else
            show_error "Error al importar script SQL"
        fi
    else
        show_error "Error al crear base de datos"
    fi
fi

# Paso 5: Configurar permisos de carpetas
echo ""
echo "Paso 5: Configurando permisos de carpetas..."

# Crear directorios si no existen
mkdir -p storage/logs
mkdir -p storage/sessions

# Configurar permisos
chmod -R 755 .
chmod -R 777 storage/logs
chmod -R 777 storage/sessions

show_message "Permisos configurados correctamente"

# Paso 6: Crear archivos .gitkeep
touch storage/logs/.gitkeep
touch storage/sessions/.gitkeep

# Paso 7: Verificación final
echo ""
echo "============================================"
echo "  Instalación completada"
echo "============================================"
echo ""
echo "Credenciales por defecto:"
echo "  Email: admin@sgcea.com"
echo "  Contraseña: admin123"
echo ""
echo "Para acceder al sistema:"
echo "  1. Asegúrese de que Apache esté ejecutándose"
echo "  2. Abra su navegador en: http://localhost/sgcea/public"
echo ""
echo "Recomendaciones de seguridad:"
echo "  - Cambie la contraseña del administrador"
echo "  - En producción, use HTTPS"
echo "  - Configure correctamente los permisos del servidor web"
echo ""
show_message "¡Instalación completada!"
