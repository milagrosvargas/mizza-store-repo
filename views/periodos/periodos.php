<style>
    /* === CONTENEDOR PRINCIPAL === */
    .periodos-container {
        background-color: #ffffff;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(139, 69, 90, 0.1);
        border: 1px solid #f4e5e8;
        font-family: 'Poppins', sans-serif;
    }

    /* === ENCABEZADO === */
    .periodos-container h4 {
        font-weight: 600;
        color: #8b4356;
        padding-left: 10px;
        margin-bottom: 30px;
        position: relative;
        font-size: 1.6rem;
    }

    .periodos-container h4::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 60%;
        background: linear-gradient(to bottom, #8b4356, #d98c8c);
        border-radius: 3px;
    }

    /* === BOTONES === */
    .btn-group button {
        min-width: 170px;
        margin-right: 10px;
        border-radius: 30px;
        font-weight: 500;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(139, 69, 90, 0.15);
    }

    .btn-success {
        background-color: #b8526c;
        border-color: #b8526c;
    }
    .btn-success:hover {
        background-color: #8b4356;
        border-color: #8b4356;
    }

    .btn-primary {
        background-color: #703b44;
        border-color: #703b44;
    }
    .btn-primary:hover {
        background-color: #502a32;
        border-color: #502a32;
    }

    .btn-warning {
        background-color: #f9d7d7;
        border-color: #f9d7d7;
        color: #8b4356;
    }
    .btn-warning:hover {
        background-color: #f4c2c2;
        border-color: #f4c2c2;
        color: #703b44;
    }

    .btn-outline-danger {
        border-color: #d16c6c;
        color: #d16c6c;
    }
    .btn-outline-danger:hover {
        background-color: #d16c6c;
        color: white;
    }

    .btn-info {
        background-color: #7f9baa;
        border-color: #7f9baa;
    }
    .btn-info:hover {
        background-color: #5e7c8b;
        border-color: #5e7c8b;
    }

    .btn-secondary {
        background-color: #d0b49f;
        border-color: #d0b49f;
    }
    .btn-secondary:hover {
        background-color: #b39987;
        border-color: #b39987;
    }

    /* === SELECT === */
    #selectPeriodo {
        max-width: 380px;
        border-radius: 30px;
        border: 1px solid #f5dadd;
        box-shadow: 0 2px 6px rgba(139, 69, 90, 0.05);
        padding: 10px 15px;
    }

    #selectPeriodo:focus {
        border-color: #b8526c;
        box-shadow: 0 0 10px rgba(139, 69, 90, 0.3);
    }

    /* === TABLAS DINÁMICAS === */
    #resultados-periodo table {
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
    }

    #resultados-periodo th {
        background-color: #8b4356;
        color: white;
        font-weight: 600;
        text-align: center;
        padding: 10px;
    }

    #resultados-periodos td {
        padding: 8px 12px;
        text-align: center;
        border-bottom: 1px solid #f3dbdf;
        color: #444;
    }

    /* === MODAL === */
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(139, 69, 90, 0.15);
        border: 1px solid #f1dee2;
    }

    .modal-title {
        font-weight: 600;
        color: #8b4356;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #f1d6da;
        font-family: 'Poppins', sans-serif;
    }

    .form-control:focus {
        border-color: #b8526c;
        box-shadow: 0 0 10px rgba(139, 69, 90, 0.25);
    }

    /* === CONTENEDOR DEL GRÁFICO === */
    #graficoVentas {
        max-width: 650px;
        width: 90%;
        margin: 30px auto;
        display: block;
        padding: 18px;
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(139, 69, 90, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    #graficoVentas:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(139, 69, 90, 0.15);
    }
</style>

<div class="card periodos-container">

    <h4>Gráficos de estadísticas - Seleccione... </h4>

    <div class="btn-group mb-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoPeriodo">
            + Nuevo período
        </button>
        <button class="btn btn-primary" onclick="listarPeriodos()">
            Ver períodos
        </button>
        <button class="btn btn-warning" onclick="consultarEstadisticas()">
            Productos más vendidos
        </button>
        <button class="btn btn-outline-danger" onclick="consultarMenosVendidos()">
            Productos menos vendidos
        </button>
        <button class="btn btn-info" onclick="consultarCategorias()">
            Categorías más vendidas
        </button>
        <button class="btn btn-secondary" onclick="consultarSubcategorias()">
            Subcategorías más vendidas
        </button>
    </div>

    <div class="mb-4">
        <label for="selectPeriodo" class="form-label fw-bold">Seleccionar período:</label>
        <select id="selectPeriodo" class="form-select">
            <option value="">Por favor, seleccione período</option>
            <?php if (isset($periodos) && !empty($periodos)): ?>
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= $p['id_periodo'] ?>">
                        <?= $p['nombre_periodo'] ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div id="resultados-periodo" class="mt-3"></div>

    <canvas id="graficoVentas" class="mt-4" width="400" height="200" style="display:none;"></canvas>
</div>

<!-- MODAL NUEVO PERÍODO -->
<div class="modal fade" id="modalNuevoPeriodo" tabindex="-1" aria-labelledby="labelNuevoPeriodo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-3">
            <h5 class="modal-title" id="labelNuevoPeriodo">Crear nuevo período</h5>

            <div class="modal-body">
                <form id="formNuevoPeriodo">
                    <div class="mb-3">
                        <label class="form-label">Nombre del período</label>
                        <input type="text" name="nombre_periodo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                </form>
                <div id="mensajePeriodo"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarPeriodo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="/MizzaStore/assets/js/estadisticas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>