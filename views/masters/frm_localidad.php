<style>
    /* 🎨 PALETA DE COLORES
    Bordó: #7a1c4b
    Rosa medio: #d94b8c
    Rosa claro: #f9e2ec
    Texto oscuro: #2b1a1f
    */

    /* === CONTENEDOR GENERAL === */
    .contenedor-localidades {
        width: 80%;
        margin: 40px auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #2b1a1f;
    }

    .contenedor-localidades h2 {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 600;
        color: #7a1c4b;
        letter-spacing: 0.5px;
    }

    /* === BUSCADOR Y FILTROS === */
    .buscador {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .buscador input,
    .buscador select {
        padding: 8px 10px;
        font-size: 14px;
        border: 1px solid #d94b8c;
        border-radius: 6px;
        outline: none;
        transition: all 0.2s ease-in-out;
        color: #2b1a1f;
        background-color: #fff;
    }

    .buscador input:focus,
    .buscador select:focus {
        border-color: #7a1c4b;
        box-shadow: 0 0 4px rgba(122, 28, 75, 0.4);
    }

    /* === TABLA === */
    #tablaLocalidades {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Encabezado */
    #tablaLocalidades thead {
        background-color: #7a1c4b;
        color: #fff;
    }

    #tablaLocalidades th {
        padding: 14px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    #tablaLocalidades th:last-child {
        text-align: center;
    }

    /* Celdas */
    #tablaLocalidades td {
        padding: 12px 15px;
        vertical-align: middle;
        font-size: 15px;
    }

    #tablaLocalidades td.acciones {
        text-align: center;
    }

    /* Filas */
    #tablaLocalidades tbody tr {
        border-bottom: 1px solid #f0c8d8;
        transition: background-color 0.2s ease;
    }

    #tablaLocalidades tbody tr:hover {
        background-color: #f9e2ec;
    }

        /* === PAGINADOR === */
    .paginador {
        margin-top: 20px;
        text-align: center;
    }

    .paginador button {
        margin: 2px;
        padding: 6px 12px;
        border: 1px solid #d94b8c;
        background: #fff;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
        color: #7a1c4b;
        font-weight: 500;
    }

    .paginador button:hover {
        background-color: #d94b8c;
        color: #fff;
        border-color: #d94b8c;
    }

    .paginador button.active {
        background-color: #7a1c4b;
        color: #fff;
        border-color: #7a1c4b;
    }

    .btn-pagina {
        margin: 3px;
        padding: 7px 13px;
        border: 1px solid #d94b8c;
        background: #fff;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
        color: #7a1c4b;
        font-weight: 500;
    }

    .btn-pagina:hover {
        background-color: #d94b8c;
        color: #fff;
        border-color: #d94b8c;
    }

    .btn-pagina.activo {
        background-color: #7a1c4b;
        color: #fff;
        border-color: #7a1c4b;
    }

    /* === ICONOS === */
    .icono-btn,
    .icono-tabla {
        width: 22px;
        height: 22px;
        vertical-align: middle;
        object-fit: contain;
        display: inline-block;
    }

    /* === BOTÓN PRINCIPAL === */
    .btn-primario {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #d81b60, #ad1457);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 16px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 3px 6px rgba(173, 20, 87, 0.3);
        transition: all 0.25s ease-in-out;
        transform: translateY(0);
    }

    .btn-primario:hover {
        background: linear-gradient(135deg, #e91e63, #b0003a);
        box-shadow: 0 5px 12px rgba(173, 20, 87, 0.4);
        transform: translateY(-2px);
    }

    .btn-primario:active {
        transform: scale(0.97);
        box-shadow: 0 2px 6px rgba(173, 20, 87, 0.25);
    }

    .btn-primario img.icono-btn {
        width: 20px;
        height: 20px;
        filter: brightness(0) invert(1);
    }

    /* === BOTONES DE ACCIÓN EN TABLA === */
    .btn-accion {
        border: none;
        background: transparent;
        cursor: pointer;
        margin: 0 5px;
        padding: 4px;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .btn-accion:hover {
        transform: scale(1.15);
        opacity: 0.8;
    }

    /* === MODALES === */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        justify-content: center;
        align-items: center;
        z-index: 999;
        animation: fadeIn 0.2s ease-in;
    }

    .modal-content {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        animation: scaleIn 0.2s ease-in-out;
        border-top: 6px solid #7a1c4b;
    }

    .modal-content h3 {
        margin-top: 0;
        text-align: center;
        color: #7a1c4b;
    }

    .modal-content label {
        font-weight: 500;
        display: block;
        margin-top: 10px;
        color: #2b1a1f;
    }

    .modal-content input {
        width: 100%;
        padding: 8px;
        margin-top: 6px;
        border: 1px solid #d94b8c;
        border-radius: 5px;
        outline: none;
        transition: 0.2s;
    }

    .modal-content input:focus {
        border-color: #7a1c4b;
        box-shadow: 0 0 4px rgba(122, 28, 75, 0.3);
    }

    .acciones-modal {
        margin-top: 15px;
        text-align: right;
    }

    .btn-secundario {
        background: #f9e2ec;
        border: 1px solid #d94b8c;
        color: #7a1c4b;
        border-radius: 5px;
        padding: 7px 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .btn-secundario:hover {
        background: #d94b8c;
        color: #fff;
    }

    /* === ANIMACIONES === */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<div class="contenedor-localidades">
    <h2>Localidades</h2>

    <!-- 🔍 Buscador y filtros -->
    <div class="buscador">
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="text" id="buscar" placeholder="Buscar localidad...">
            <select id="ordenar">
                <option value="ASC">A - Z</option>
                <option value="DESC">Z - A</option>
            </select>
            <select id="registrosPorPagina" title="Registros por página">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>

        <button id="btnNuevo" class="btn-primario">
            <img src="assets/images/icons/add.png" alt="Nuevo" class="icono-btn">
            Nueva localidad
        </button>
    </div>

    <!-- 🧾 Tabla -->
    <table id="tablaLocalidades">
        <thead>
            <tr>
                <th>Localidad</th>
                <th>Provincia</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contenido dinámico AJAX -->
        </tbody>
    </table>

    <!-- 🪄 MODAL NUEVA LOCALIDAD -->
    <div id="modalNuevaLocalidad" class="modal">
        <div class="modal-content">
            <h3>Nueva localidad</h3>
            <form id="formNuevaLocalidad" novalidate>
                <label for="nombre_localidad">Nombre</label>
                <input type="text" id="nombre_localidad" name="nombre_localidad" maxlength="50" required>

                <label for="id_provincia">Provincia</label>
                <select id="id_provincia" name="id_provincia" required>
                    <option value="">Seleccione una provincia</option>
                </select>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarModal" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🪄 MODAL EDITAR LOCALIDAD -->
    <div id="modalEditarLocalidad" class="modal">
        <div class="modal-content">
            <h3>Editar localidad</h3>
            <form id="formEditarLocalidad" novalidate>
                <input type="hidden" id="editar_id_localidad" name="editar_id_localidad">

                <label for="editar_nombre_localidad">Nombre</label>
                <input type="text" id="editar_nombre_localidad" name="editar_nombre_localidad" maxlength="50" required>

                <label for="editar_id_provincia">Provincia</label>
                <select id="editar_id_provincia" name="editar_id_provincia" required>
                    <option value="">Seleccione una provincia</option>
                </select>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarEditar" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📄 Paginador -->
    <div id="paginador" class="paginador"></div>
</div>

<!-- Script principal -->
<script type="module" src="assets/js/localidades.js"></script>
