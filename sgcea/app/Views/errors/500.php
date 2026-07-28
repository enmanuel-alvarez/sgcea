<?php
/**
 * Vista de error 500 - Error interno del servidor
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error interno - 500</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <i class="bi bi-x-circle-fill text-danger display-1"></i>
                        <h1 class="mt-3 text-danger">500</h1>
                        <h3>Error Interno del Servidor</h3>
                        <p class="text-muted">Ha ocurrido un error inesperado. Por favor contacte al administrador.</p>
                        <hr>
                        <a href="javascript:history.back()" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                        <a href="<?= url('/admin') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
