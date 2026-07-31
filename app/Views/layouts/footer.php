<?php
/**
 * Layout - Footer (Bootstrap 5 Dashboard Template)
 */
$flash = flash();
?>
            </main> <!-- Fin contenido principal -->
        </div> <!-- Fin row -->
    </div> <!-- Fin container-fluid -->

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <!-- Mensajes flash ocultos para JS -->
    <?php if ($flash): ?>
        <div class="flash-message d-none" data-tipo="<?= e($flash['tipo']) ?>"><?= e($flash['mensaje']) ?></div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (necesario para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('js/datatables-config.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>"></script>

</body>
</html>
