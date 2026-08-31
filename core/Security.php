<?php

namespace Src\Core;

/**
 * Security - Funciones de seguridad: CSRF, sanitización, headers
 */

class Security
{
    /**
     * Genera un token CSRF único para la sesión actual
     */
    public static function generarTokenCSRF(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token CSRF enviado en un formulario
     */
    public static function validarTokenCSRF(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Regenera el token CSRF (usar después de validaciones críticas)
     */
    public static function regenerarTokenCSRF(): string
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanitiza una entrada de usuario
     */
    public static function sanitizar(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $input;
    }

    /**
     * Sanitiza múltiples entradas
     */
    public static function sanitizarArray(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[self::sanitizar($key)] = self::sanitizarArray($value);
            } else {
                $result[self::sanitizar($key)] = self::sanitizar((string) $value);
            }
        }
        return $result;
    }

    /**
     * Establece headers de seguridad HTTP
     */
    public static function establecerHeadersSeguridad(): void
    {
        // Prevenir clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Prevenir MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com cdn.tailwindcss.com code.jquery.com cdn.datatables.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com cdn.datatables.net; font-src 'self' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.gstatic.com data:; img-src 'self' data:;");
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // Cache control para páginas sensibles
        if (isset($_SESSION['usuario_id'])) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
    }

    /**
     * Limpia y valida un email
     */
    public static function limpiarEmail(string $email): ?string
    {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        return null;
    }

    /**
     * Limpia y valida un entero
     */
    public static function limpiarInt($valor): ?int
    {
        $valor = filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
        if (filter_var($valor, FILTER_VALIDATE_INT) !== false) {
            return (int) $valor;
        }
        return null;
    }

    /**
     * Escapa output para HTML
     */
    public static function e(?string $string): string
    {
        return $string !== null ? htmlspecialchars($string ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
    }

    /**
     * Valida que un string no esté vacío después de sanitizar
     */
    public static function validarNoVacio(?string $valor): bool
    {
        return !empty(trim($valor ?? ''));
    }
}


