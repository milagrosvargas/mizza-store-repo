<?php if (!empty($periodos)): ?>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre del período</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Cantidad vendida (si aplica)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($periodos as $fila): ?>
                <tr>
                    <td><?= $fila['id_periodo'] ?></td>
                    <td><?= $fila['nombre_periodo'] ?></td>
                    <td><?= $fila['fecha_inicio'] ?></td>
                    <td><?= $fila['fecha_fin'] ?></td>
                    <td><?= $fila['cantidad_vendida'] ?? '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning mt-3">
        No se encontraron períodos registrados.
    </div>
<?php endif; ?>
