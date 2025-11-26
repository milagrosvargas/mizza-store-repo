<style>
    /* 🎨 PALETA DE COLORES
    Bordó: #7a1c4b
    Rosa medio: #d94b8c
    Rosa claro: #f9e2ec
    Texto oscuro: #2b1a1f
    */

    /* === CONTENEDOR GENERAL === */
    .contenedor-subcategorias {
        width: 80%;
        margin: 40px auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #2b1a1f;
    }

    .contenedor-subcategorias h2 {
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

    /* Estilo especial para el combo anidado */
    #filtroCategoria {
        min-width: 180px;
        font-weight: 500;
        color: #7a1c4b;
        background-color: #fff7fa;
    }

    #filtroCategoria option {
        color: #2b1a1f;
    }

    /* === TABLA === */
    #tablaSubCategorias {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    #tablaSubCategorias thead {
        background-color: #7a1c4b;
        color: #fff;
    }

    #tablaSubCategorias th {
        padding: 14px 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    #tablaSubCategorias th:last-child {
        text-align: center;
    }

    #tablaSubCategorias td {
        padding: 12px 15px;
        vertical-align: middle;
        font-size: 15px;
    }

    #tablaSubCategorias td.acciones {
        text-align: center;
    }

    #tablaSubCategorias tbody tr {
        border-bottom: 1px solid #f0c8d8;
        transition: background-color 0.2s ease;
    }

    #tablaSubCategorias tbody tr:hover {
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

    /* === ICONOS Y BOTONES === */
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

    /* Botones dentro de la tabla */
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
        width: 420px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        animation: scaleIn 0.2s ease-in-out;
        border-top: 6px solid #7a1c4b;
    }

    .modal-content h3 {
        margin-top: 0;
        text-align: center;
        color: #7a1c4b;
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 8px;
        margin-top: 8px;
        border-radius: 5px;
        border: 1px solid #d94b8c;
        outline: none;
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
        font-weight: 500;
    }

    .btn-secundario:hover {
        background: #d94b8c;
        color: #fff;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<div class="contenedor-subcategorias">
    <h2>Subcategorías</h2>

    <!-- 🔍 Buscador y filtros -->
    <div class="buscador">
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="text" id="buscarSubCat" placeholder="Buscar subcategoría...">

            <!-- 🔸 Combo de categorías (anidado) -->
            <select id="filtroCategoria" title="Filtrar por categoría">
                <option value="0" selected>-- Todas las categorías --</option>
            </select>

            <select id="ordenarSubCat">
                <option value="ASC">A - Z</option>
                <option value="DESC">Z - A</option>
            </select>

            <select id="registrosPorPaginaSubCat" title="Registros por página">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>

        <button id="btnNuevaSubCat" class="btn-primario">
            <img src="assets/images/icons/add.png" alt="Nuevo" class="icono-btn">
            Nueva subcategoría
        </button>
    </div>

    <!-- 🧾 Tabla -->
    <table id="tablaSubCategorias">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Contenido dinámico AJAX -->
        </tbody>
    </table>

    <!-- 🔹 MODAL NUEVA SUBCATEGORÍA -->
    <div id="modalNuevaSubCat" class="modal">
        <div class="modal-content">
            <h3>Nueva subcategoría</h3>

            <form id="formNuevaSubCat" novalidate>
                <label for="id_categoria">Categoría</label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Seleccione una categoría...</option>
                </select>

                <label for="nombre_sub_categoria">Nombre</label>
                <input type="text" id="nombre_sub_categoria" name="nombre_sub_categoria" maxlength="50" required>

                <label for="cant_sub_categoria">Cantidad</label>
                <input type="number" id="cant_sub_categoria" name="cant_sub_categoria" min="1" required>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarModalSubCat" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 MODAL EDITAR SUBCATEGORÍA -->
    <div id="modalEditarSubCat" class="modal">
        <div class="modal-content">
            <h3>Editar subcategoría</h3>

            <form id="formEditarSubCat" novalidate>
                <input type="hidden" id="editar_id_sub_categoria" name="id_sub_categoria">

                <label for="editar_id_categoria">Categoría</label>
                <select id="editar_id_categoria" name="id_categoria" required>
                    <option value="">Seleccione una categoría...</option>
                </select>

                <label for="editar_nombre_sub_categoria">Nombre</label>
                <input type="text" id="editar_nombre_sub_categoria" name="nombre_sub_categoria" maxlength="50" required>

                <label for="editar_cant_sub_categoria">Cantidad</label>
                <input type="number" id="editar_cant_sub_categoria" name="cant_sub_categoria" min="1" required>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarEditarSubCat" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📄 Paginador -->
    <div id="paginadorSubCat" class="paginador"></div>
</div>

<!-- 🔹 Script principal -->
<script type="module" src="assets/js/sub_categorias.js"></script>
