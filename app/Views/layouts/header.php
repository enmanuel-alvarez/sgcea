<?php
/**
 * Layout - Header (Bootstrap 5 Dashboard Template)
 */
$nombreCompleto = ($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '');
$tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'SGCEA') ?> - <?= config('app.nombre_sistema') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/custom.css') ?>" rel="stylesheet">
</head>
<body>

    <!-- HEADER / NAVBAR SUPERIOR -->
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="<?= url('/') ?>">
            <i class="bi bi-mortarboard-fill me-1"></i> SGCEA
        </a>
        
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" 
                data-bs-toggle="collapse" data-bs-target="#sidebarMenu" 
                aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Barra de búsqueda -->
        <input class="form-control form-control-dark w-100 m-2" type="text" id="searchGlobal" 
               placeholder="Buscar..." aria-label="Buscar">
        
        <!-- Usuario y acciones -->
        <ul class="navbar-nav px-3 flex-row align-items-center">
            <li class="nav-item text-nowrap me-3 d-none d-md-block">
                <span class="nav-link py-0 text-white-50 small">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= e($nombreCompleto) ?>
                    <span class="badge bg-secondary ms-1"><?= ucfirst(e($tipoUsuario)) ?></span>
                </span>
            </li>
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="<?= url('/logout') ?>" title="Cerrar Sesión">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline"> Salir</span>
                </a>
            </li>
        </ul>
    </header>

    <div class="container-fluid">
        <div class="row">
