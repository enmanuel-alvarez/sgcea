<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tipo_constancia) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .encabezado { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .contenido { line-height: 2; font-size: 14pt; }
        .firma { margin-top: 80px; text-align: center; }
        .firma-linea { border-top: 1px solid #000; width: 250px; margin: 10px auto; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 20px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i>Imprimir</button>
        <button onclick="window.close()" class="btn btn-secondary ms-2">Cerrar</button>
    </div>
    <div class="encabezado">
        <h3><?= htmlspecialchars($config['nombre_institucion'] ?? 'INSTITUCIÓN EDUCATIVA') ?></h3>
        <p class="mb-0">Sistema de Gestión y Control Escolar-Académico</p>
        <p class="mb-0">Año Lectivo: <?= htmlspecialchars($config['ano_lectivo'] ?? date('Y')) ?></p>
    </div>
    <div class="contenido">
        <h4 class="text-center mb-4"><?= htmlspecialchars($tipo_constancia) ?></h4>
        <p>Por medio de la presente se hace constar que:</p>
        <p class="fw-bold"><?= htmlspecialchars($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?></p>
        <p>Cédula: <?= htmlspecialchars($estudiante['cedula']) ?></p>
        <p>Es estudiante regular de esta institución, cursando el <strong><?= htmlspecialchars($grado) ?></strong>, sección <strong><?= htmlspecialchars($seccion) ?></strong>.</p>
        <p><?= htmlspecialchars($motivo) ?></p>
        <p>Se expide la presente a solicitud del interesado para los fines que estime conveniente.</p>
    </div>
    <div class="firma">
        <div class="firma-linea"></div>
        <p><strong>Dirección / Secretaría</strong></p>
        <p><?= htmlspecialchars($config['nombre_institucion'] ?? '') ?></p>
    </div>
    <p class="mt-5 text-muted small">Documento generado el <?= date('d/m/Y H:i:s') ?></p>
</body>
</html>
