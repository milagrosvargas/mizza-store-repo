<style>
    /* 🎨 PALETA DE COLORES */
    /* Bordó: #7a1c4b | Rosa medio: #d94b8c | Rosa claro: #f9e2ec | Texto oscuro: #2b1a1f */

    .contenedor-barrios {
        width: 80%;
        margin: 20px auto; /* 🔽 antes 40px, se reduce el espacio superior */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #2b1a1f;
    }

    .contenedor-barrios h2 {
        text-align: center;
        margin-bottom: 20px; /* 🔽 antes 25px */
        font-weight: 600;
        color: #7a1c4b;
        letter-spacing: 0.5px;
    }

    /* === BUSCADOR Y FILTROS === */
    .buscador {
        display: flex;
        flex-wrap: wrap; /* ✅ permite que se acomoden si el espacio es chico */
        align-items: center;
        gap: 10px;
        margin-bottom: 15px; /* 🔽 más compacto */
        justify-content: flex-start; /* ✅ evita separaciones grandes */
    }

    /* ✅ esto empuja el botón a la derecha sin dejar espacio vacío */
    .buscador .btn-primario {
        margin-left: auto;
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
    #tablaBarrios {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    #tablaBarrios thead {
        background-color: #7a1c4b;
        color: #fff;
    }

    #tablaBarrios th,
    #tablaBarrios td {
        padding: 12px 15px;
        font-size: 15px;
        vertical-align: middle;
    }

    #tablaBarrios th:last-child,
    #tablaBarrios td.acciones {
        text-align: center;
    }

    #tablaBarrios tbody tr {
        border-bottom: 1px solid #f0c8d8;
        transition: background-color 0.2s ease;
    }

    #tablaBarrios tbody tr:hover {
        background-color: #f9e2ec;
    }

    /* === PAGINADOR === */
    .paginador {
        margin-top: 15px;
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
        color: #7a1c4b;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .paginador button:hover {
        background-color: #d94b8c;
        color: #fff;
    }

    .paginador button.active {
        background-color: #7a1c4b;
        color: #fff;
    }

    /* === BOTONES === */
    .icono-btn,
    .icono-tabla {
        width: 22px;
        height: 22px;
        vertical-align: middle;
        object-fit: contain;
    }

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
    }

    .btn-primario:hover {
        background: linear-gradient(135deg, #e91e63, #b0003a);
        transform: translateY(-2px);
    }

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
    }

    .modal-content {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        width: 400px;
        border-top: 6px solid #7a1c4b;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }

    .modal-content h3 {
        text-align: center;
        color: #7a1c4b;
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 8px;
        margin-top: 8px;
        border-radius: 5px;
        outline: none;
        border: 1px solid #d94b8c;
        transition: 0.2s;
    }

    .modal-content input:focus,
    .modal-content select:focus {
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
    }

    .btn-secundario:hover {
        background: #d94b8c;
        color: #fff;
    }
</style>


<div class="contenedor-barrios">
    <h2>Barrios</h2>

    <!-- 🔍 BUSCADOR Y FILTROS -->
    <div class="buscador">
        <input type="text" id="buscar" placeholder="Buscar barrio...">
        <select id="ordenar">
            <option value="ASC">A-Z</option>
            <option value="DESC">Z-A</option>
        </select>
        <select id="registrosPorPagina">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
        <button id="btnNuevo" class="btn-primario">
            <img src="assets/images/icons/add.png" alt="Nuevo" class="icono-btn"> Nuevo barrio
        </button>
    </div>

    <!-- 📋 TABLA DE BARRIOS -->
    <table id="tablaBarrios">
        <thead>
            <tr>
                <th>Nombre del barrio</th>
                <th>Localidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Datos cargados dinámicamente -->
        </tbody>
    </table>

    <!-- 📄 PAGINADOR -->
    <div id="paginador" class="paginador"></div>
</div>

<!-- 🧩 MODAL NUEVO BARRIO -->
<div id="modalNuevoBarrio" class="modal">
    <div class="modal-content">
        <h3>Nuevo Barrio</h3>
        <form id="formNuevoBarrio" novalidate>
            <label for="nombre_barrio">Nombre:</label>
            <input type="text" id="nombre_barrio" name="nombre_barrio" placeholder="Ingrese nombre del barrio" required>

            <label for="id_localidad">Localidad:</label>
            <select id="id_localidad" name="id_localidad" required>
                <option value="">Seleccione una localidad</option>
            </select>

            <div class="acciones-modal">
                <button type="button" id="btnCancelarModal" class="btn-secundario">Cancelar</button>
                <button type="submit" class="btn-primario">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- ✏️ MODAL EDITAR BARRIO -->
<div id="modalEditarBarrio" class="modal">
    <div class="modal-content">
        <h3>Editar barrio</h3>
        <form id="formEditarBarrio" novalidate>
            <input type="hidden" id="editar_id_barrio" name="editar_id_barrio">

            <label for="editar_nombre_barrio">Nombre</label>
            <input type="text" id="editar_nombre_barrio" name="editar_nombre_barrio" required>

            <label for="editar_id_localidad">Localidad</label>
            <select id="editar_id_localidad" name="editar_id_localidad" required>
                <option value="">Seleccione una localidad</option>
            </select>

            <div class="acciones-modal">
                <button type="button" id="btnCancelarEditar" class="btn-secundario">Cancelar</button>
                <button type="submit" class="btn-primario">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- 🚀 JS -->
<script type="module" src="assets/js/barrios.js"></script>
