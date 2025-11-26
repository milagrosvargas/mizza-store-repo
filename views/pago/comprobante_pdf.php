<?php
// --- Validaciones mínimas ---
if (!isset($pedido) || !isset($pago) || empty($pedido['detalles'])) {
    echo "<h3>No hay datos suficientes para generar el comprobante.</h3>";
    return;
}

/*
  Este archivo solo genera el HTML del comprobante.
  El renderizado, guardado y descarga del PDF se hace desde PagoController.
*/
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            max-width: 120px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 20px;
            color: #2e4053;
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .info,
        .payment,
        .details {
            width: 100%;
            margin-top: 10px;
        }

        .info td,
        .payment td {
            padding: 4px 6px;
        }

        .details th,
        .details td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }

        .details th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            margin: 15px 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="https://i.ibb.co/Wg9LZQp/LogoMizza.png" alt="MizzaStore">
    </div>

    <div class="title">Comprobante de Pago</div>

    <table class="info">
        <tr>
            <td><strong>Pedido N°:</strong> <?= $pedido['id_pedido']; ?></td>
            <td><strong>Fecha:</strong> <?= $pedido['fecha_pedido']; ?></td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong> <?= $pedido['nombre_persona'] . ' ' . $pedido['apellido_persona']; ?></td>
            <td><strong>Correo:</strong> <?= $pedido['email_usuario']; ?></td>
        </tr>
    </table>

    <hr>

    <table class="payment">
        <tr>
            <td><strong>Método de pago:</strong> <?= $pago['nombre_metodo_pago']; ?></td>
            <td><strong>Estado:</strong> <?= ucfirst($pago['estado_pago']); ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Monto pagado:</strong> $<?= number_format($pago['monto_pago'], 2); ?></td>
        </tr>
    </table>

    <h3>Productos comprados</h3>
    <table class="details">
        <thead>
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

    <p class="total">Total pagado: $<?= number_format($pago['monto_pago'], 2); ?></p>

    <hr>

    <p style="text-align:center; font-size:11px;">
        Este comprobante es válido como constancia digital de pago.<br>
        Gracias por elegir <strong>MizzaStore</strong>.
    </p>

</body>

</html>