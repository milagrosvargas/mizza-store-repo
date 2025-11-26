<style>
.detalle-contenedor {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 25px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2b1a1f;
}

.detalle-contenedor h2 {
    color: #7a1c4b;
    margin-bottom: 5px;
    font-size: 1.7rem;
}

.detalle-contenedor .subtitulo {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 20px;
}

.card-detalle {
    background: #f9e2ec;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid #f0c8d8;
}

.card-detalle h3 {
    margin-top: 0;
    color: #7a1c4b;
    margin-bottom: 15px;
}

.grid-info {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap: 8px 20px;
    font-size: 14px;
}

.tabla-contenedor-detalle {
    overflow-x: auto;
}

.tabla-detalle {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

.tabla-detalle th {
    background: #7a1c4b;
    color: white;
    padding: 10px;
    text-align: left;
}

.tabla-detalle td {
    padding: 10px;
    border-bottom: 1px solid #f0c8d8;
}

.img-producto {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #ddd;
}

.acciones-detalle {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

/* Botones reutilizables */
.btn-primario,
.btn-secundario {
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}

.btn-primario {
    background: linear-gradient(135deg, #d94b8c, #7a1c4b);
    color: white;
}

.btn-secundario {
    background: #f9e2ec;
    border: 1px solid #d94b8c;
    color: #7a1c4b;
}

.btn-secundario:hover {
    background:#d94b8c;
    color:#fff;
}
</style>


<div class="detalle-contenedor">
    <h2>Detalle del Pedido #<?php echo $pedido['id_pedido']; ?></h2>
    <p class="subtitulo">Visualización completa del pedido, incluyendo productos, pago, envío y estados del proceso.</p>

    <!-- Datos generales del pedido -->
    <div class="card-detalle">
        <h3>Información del Pedido</h3>
        <div class="grid-info">
            <p><strong>Fecha de pedido:</strong> <?php echo $pedido['fecha_pedido']; ?></p>
            <p><strong>Estado del pedido:</strong> <?php echo $pedido['estado_pedido']; ?></p>
            <p><strong>Estado del pago:</strong> <?php echo $pedido['estado_pago']; ?></p>
            <p><strong>Método de pago:</strong> <?php echo $pedido['nombre_metodo_pago'] ?? 'No registrado'; ?></p>
            <p><strong>Monto total:</strong> $<?php echo number_format($pedido['monto_total'], 2); ?></p>
        </div>
    </div>

    <!-- Información de envío (si existe) -->
    <?php if ($envio): ?>
    <div class="card-detalle">
        <h3>Información de envío</h3>
        <div class="grid-info">
            <p><strong>Estado del envío:</strong> <?php echo $envio['estado_envio']; ?></p>
            <p><strong>Fecha de envío:</strong> <?php echo $envio['fecha_envio'] ?? 'No enviado aún'; ?></p>
            <p><strong>Fecha de entrega:</strong> <?php echo $envio['fecha_entrega'] ?? 'Pendiente'; ?></p>
            <p><strong>Dirección:</strong> 
                <?php 
                    echo $envio['calle_direccion'] . ' ' . $envio['numero_direccion'] . 
                        ', ' . $envio['nombre_barrio'] . ', ' . $envio['nombre_localidad'] . 
                        ', ' . $envio['nombre_provincia'] . ', ' . $envio['nombre_pais'];
                ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Productos comprados -->
    <div class="card-detalle">
        <h3>Productos del pedido</h3>
        <div class="tabla-contenedor-detalle">
            <table class="tabla-detalle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td><?php echo $prod['nombre_producto']; ?></td>
                        <td>
                            <?php if (!empty($prod['imagen_producto'])): ?>
                                <img src="<?php echo $prod['imagen_producto']; ?>" alt="Imagen producto" class="img-producto">
                            <?php else: ?>
                                <span>Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $prod['cantidad_producto']; ?></td>
                        <td>$<?php echo number_format($prod['precio_unitario'], 2); ?></td>
                        <td>$<?php echo number_format($prod['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Acciones -->
    <div class="acciones-detalle">
        <a href="index.php?controller=cliente&action=verSeccionHistorial" class="btn-secundario">Volver al historial</a>
        <a href="index.php?controller=cliente&action=descargarComprobante&id=<?php echo $pedido['id_pedido']; ?>" class="btn-primario">Descargar comprobante</a>
        <!-- Futuro: agregar botones seguimiento, repetir pedido, solicitar devolución -->
    </div>
</div>
