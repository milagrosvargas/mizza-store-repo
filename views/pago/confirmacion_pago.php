<?php
// Validación de datos
if (!isset($pedido) || !isset($pago)) {
?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información del pago.',
            confirmButtonColor: '#d33'
        });
    </script>
    <div class="text-center mt-5">
        <a href="index.php?controller=Home&action=index" class="btn btn-primary">Volver al inicio</a>
    </div>
<?php
    return;
}
?>

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">Pago confirmado</h2>
        <p>Gracias por tu compra, <strong><?= $pedido['nombre_persona'] . ' ' . $pedido['apellido_persona']; ?></strong>.</p>
    </div>

    <!-- Datos del pedido -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-3">Detalles del Pedido</h5>
        <p><strong>Pedido Nº:</strong> <?= $pedido['id_pedido']; ?></p>
        <p><strong>Fecha:</strong> <?= $pedido['fecha_pedido']; ?></p>
        <p><strong>Estado del pago:</strong> <?= ucfirst($pago['estado_pago']); ?></p>
        <p><strong>Método de pago:</strong> <?= $pago['nombre_metodo_pago']; ?></p>
        <p><strong>Monto pagado:</strong> $<?= number_format($pago['monto_pago'], 2); ?></p>
    </div>

    <!-- Productos comprados -->
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold" style="color:#880e4f;">Productos comprados</h5>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedido['detalles'] as $item): ?>
                        <tr>
                            <td><?= $item['nombre_producto']; ?></td>
                            <td><?= $item['cantidad_producto']; ?></td>
                            <td>$<?= number_format($item['precio_unitario'], 2); ?></td>
                            <td>$<?= number_format($item['precio_unitario'] * $item['cantidad_producto'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botones -->
    <div class="text-center mt-4">

        <?php
        $rutaPDF = "views/pago/pdf_generados/comprobante_{$pedido['id_pedido']}.pdf";
        if (file_exists($rutaPDF)): ?>
            <a href="index.php?controller=Pago&action=descargarComprobante&id_pedido=<?= $pedido['id_pedido']; ?>"
                class="btn btn-success">
                Descargar comprobante PDF
            </a>
        <?php else: ?>
            <button class="btn btn-secondary" onclick="mostrarErrorPDF()">
                Comprobante no disponible
            </button>
            <script>
                function mostrarErrorPDF() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'PDF no generado',
                        text: 'El comprobante aún no está disponible. Intente más tarde o verifique su correo.',
                        confirmButtonColor: '#f0ad4e'
                    });
                }
            </script>
        <?php endif; ?>

        <a href="index.php?controller=Home&action=index"
            class="btn btn-outline-secondary ms-2">Volver al inicio</a>
    </div>
</div>