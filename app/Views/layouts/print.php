<?php
/**
 * Layout para impresión de documentos
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Documento') ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Print CSS -->
    <link href="<?= asset('css/print.css') ?>" rel="stylesheet">
</head>
<body onload="window.print()">
    <div class="print-container">
        <?= $contenido ?? '' ?>
    </div>
    
    <?php if (isset($autoPrint) && $autoPrint): ?>
    <script>
        // Imprimir automáticamente al cargar
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
    <?php endif; ?>
</body>
</html>
