<style>
    /* Gama de colores que utilizo
Bordó: #7a1c4b
Rosa medio: #d94b8c
Rosa claro: #f9e2ec
Texto oscuro: #2b1a1f
*/

    /* Contenedor general */
    .contenedor-productos {
        width: 90%;
        max-width: 1250px;
        margin: 10px auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #2b1a1f;
        background: #fff;
        padding: 30px 25px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Encabezado */
    .contenedor-productos h2 {
        text-align: left;
        margin-bottom: 10px;
        font-weight: 600;
        color: #7a1c4b;
        letter-spacing: 0.4px;
        font-size: 1.6rem;
    }

    .contenedor-productos .subtitulo {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 25px;
        line-height: 1.4;
    }

    /* Buscador y filtro */
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
        border: 1px solid #d94b8c;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s ease-in-out;
        color: #2b1a1f;
        background-color: #fff;
    }

    .buscador input:focus,
    .buscador select:focus {
        border-color: #7a1c4b;
        box-shadow: 0 0 4px rgba(122, 28, 75, 0.3);
    }

    /* Botón principal de nuevo producto */
    .btn-primario {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #d94b8c, #7a1c4b);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 9px 16px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.25s ease-in-out;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(122, 28, 75, 0.25);
    }

    .btn-primario:hover {
        background: linear-gradient(135deg, #7a1c4b, #d94b8c);
        box-shadow: 0 4px 10px rgba(122, 28, 75, 0.35);
    }

    .icono-btn {
        width: 18px;
        height: 18px;
        filter: brightness(0) invert(1);
    }

    /* Contenedor con posibilidad de scroll horizontal */
    .tabla-contenedor {
        width: 100%;
    overflow-x: scroll; 
    overflow-y: scroll; 
    white-space: nowrap; 
        max-height: 65vh;
        border: 1px solid #f0c8d8;
        border-radius: 10px;
    }

    .tabla-contenedor::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .tabla-contenedor::-webkit-scrollbar-thumb {
        background-color: #d94b8c;
        border-radius: 4px;
    }

    /* Tabla que imprime los productos */
    #tablaProductos {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    #tablaProductos thead {
        background-color: #7a1c4b;
        color: #fff;
    }

    #tablaProductos th {
        padding: 12px 10px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    #tablaProductos th:last-child {
        text-align: center;
    }

    #tablaProductos td {
        padding: 10px 8px;
        vertical-align: middle;
        font-size: 14px;
        border-bottom: 1px solid #f0c8d8;
    }

    #tablaProductos tbody tr:hover {
        background-color: #f9e2ec;
    }

    #tablaProductos td img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    /* Botones de acción de la tabla */
    .btn-accion {
        border: none;
        background: transparent;
        cursor: pointer;
        margin: 0 3px;
        padding: 3px;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .btn-accion:hover {
        transform: scale(1.1);
        opacity: 0.8;
    }

    .icono-tabla {
        width: 20px;
        height: 20px;
        object-fit: contain;
        vertical-align: middle;
    }

    /* Paginador */
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

    .paginador button.activo {
        background-color: #7a1c4b;
        color: #fff;
        border-color: #7a1c4b;
    }

    /* Modales */
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

    /* Modal con scroll */
    .modal-content {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        width: 420px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        border-top: 6px solid #7a1c4b;
        max-height: 85vh;
        overflow-y: scroll;
        scrollbar-width: thin;
        scrollbar-color: #d94b8c #f9e2ec;
    }

    /* Scroll personalizado (para Chrome/Edge/Safari) */
    .modal-content::-webkit-scrollbar {
        width: 8px;
    }

    .modal-content::-webkit-scrollbar-track {
        background: #f9e2ec;
    }

    .modal-content::-webkit-scrollbar-thumb {
        background: #d94b8c;
        border-radius: 4px;
    }

    /* Campos del formulario === */
    .modal-content h3 {
        margin-top: 0;
        text-align: center;
        color: #7a1c4b;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    .modal-content label {
        font-weight: 500;
        font-size: 0.9rem;
        margin-top: 8px;
        display: block;
        color: #2b1a1f;
    }

    .modal-content input,
    .modal-content select,
    .modal-content textarea {
        width: 100%;
        padding: 7px 9px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #d94b8c;
        outline: none;
        transition: 0.2s;
        font-size: 14px;
        color: #2b1a1f;
    }

    .modal-content textarea {
        resize: none;
        height: 70px;
    }

    .modal-content input:focus,
    .modal-content select:focus,
    .modal-content textarea:focus {
        border-color: #7a1c4b;
        box-shadow: 0 0 4px rgba(122, 28, 75, 0.3);
    }

    /* Botones del modal */
    .acciones-modal {
        margin-top: 18px;
        text-align: right;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
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

    /* Previsualización de las imágenes */
    #previewNueva img,
    #previewEditar img {
        margin-top: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        max-width: 100px;
    }

    /* Nota aclaratoria dependiente */
    .nota-ayuda {
        font-size: 0.85rem;
        color: #ac2424ff;
        margin-top: 8px;
    }

    .nota-ayuda::before {
        content: "***";
    }

    .nota-ayuda::after {
        content: "***";
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Para responsividad */
    @media (max-width: 768px) {
        .contenedor-productos {
            padding: 20px 15px;
        }

        .modal-content {
            width: 92%;
            max-height: 75vh;
        }

        #tablaProductos th,
        #tablaProductos td {
            font-size: 13px;
            padding: 8px;
        }

        .icono-tabla {
            width: 16px;
            height: 16px;
        }

        #tablaProductos td img {
            width: 45px;
            height: 45px;
        }

        .buscador {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


<div class="contenedor-productos">
    <h2>Administrar inventario</h2>

    <!-- Buscador -->
    <div class="buscador">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="text" id="buscar" placeholder="Buscar producto...">

            <select id="filtroCategoria">
                <option value="">Todas las categorías</option>
            </select>

            <select id="filtroSubCategoria">
                <option value="">Todas las subcategorías</option>
            </select>

            <input type="number" id="precioMin" placeholder="Precio min" min="0" style="width:110px;">
            <input type="number" id="precioMax" placeholder="Precio max" min="0" style="width:110px;">

            <select id="ordenar">
                <option value="ASC">A - Z</option>
                <option value="DESC">Z - A</option>
            </select>

            <select id="registrosPorPagina" title="Registros por página">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>

        <button id="btnNuevo" class="btn-primario">
            <img src="assets/images/icons/add.png" alt="Nuevo" class="icono-btn">
            Nuevo producto
        </button>
    </div>

    <!-- Tabla de productos -->
    <div class="tabla-contenedor">
        <table id="tablaProductos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Marca</th>
                    <th>Precio compra</th>
                    <th>Precio venta</th>
                    <th>Stock actual</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contenido dinámico -->
            </tbody>
        </table>
    </div>

    <!-- Paginador -->
    <div id="paginador" class="paginador"></div>

    <!-- Modal nuevo producto -->
    <div id="modalNuevoProducto" class="modal">
        <div class="modal-content">
            <h3>Nuevo producto</h3>

            <form id="formNuevoProducto" enctype="multipart/form-data" novalidate>
                <label for="codigo_barras">Código de barras (opcional)</label>
                <input type="text" id="codigo_barras" name="codigo_barras" maxlength="50" placeholder="Ej. 7891234567890">

                <label for="nombre_producto">Nombre</label>
                <input type="text" id="nombre_producto" name="nombre_producto" required maxlength="255">

                <label for="descripcion_producto">Descripción (opcional)</label>
                <textarea id="descripcion_producto" name="descripcion_producto" rows="3"></textarea>

                <div class="grid-2">
                    <div>
                        <label for="precio_compra">Precio de compra</label>
                        <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label for="precio_venta">Precio de venta</label>
                        <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="stock_minimo">Stock mínimo</label>
                        <input type="number" id="stock_minimo" name="stock_minimo" min="0" required>
                    </div>
                    <div>
                        <label for="stock_actual">Stock actual</label>
                        <input type="number" id="stock_actual" name="stock_actual" min="0" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="id_marca">Marca</label>
                        <select id="id_marca" name="id_marca" required>
                            <option value="">Seleccione una marca</option>
                        </select>
                    </div>
                    <div>
                        <label for="id_unidad_medida">Unidad de medida</label>
                        <select id="id_unidad_medida" name="id_unidad_medida" required>
                            <option value="">Seleccione una unidad</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="id_categoria">Categoría</label>
                        <select id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una categoría</option>
                        </select>
                    </div>
                    <p class="nota-ayuda">
                        La subcategoría depende de la categoría. Por favor, seleccione primero una categoría
                    </p>
                    <div>
                        <label for="id_sub_categoria">Subcategoría</label>
                        <select id="id_sub_categoria" name="id_sub_categoria">
                            <option value="">Seleccione una subcategoría</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" id="id_estado_logico" name="id_estado_logico" value="25">

                <label for="imagen_producto">Imagen (opcional)</label>
                <input type="file" id="imagen_producto" name="imagen_producto" accept="image/*">

                <div id="previewNueva" style="margin-top:8px;text-align:center;">
                    <img id="imagenPreviewNueva" src="" alt="Vista previa" style="max-width:100px;border-radius:5px;display:none;">
                </div>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarModal" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal editar producto -->
    <div id="modalEditarProducto" class="modal">
        <div class="modal-content">
            <h3>Editar producto</h3>

            <form id="formEditarProducto" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="editar_id_producto" name="editar_id_producto">

                <label for="editar_codigo_barras">Código de barras (opcional)</label>
                <input type="text" id="editar_codigo_barras" name="editar_codigo_barras" maxlength="50">

                <label for="editar_nombre_producto">Nombre</label>
                <input type="text" id="editar_nombre_producto" name="editar_nombre_producto" required maxlength="255">

                <label for="editar_descripcion_producto">Descripción (opcional)</label>
                <textarea id="editar_descripcion_producto" name="editar_descripcion_producto" rows="3"></textarea>

                <div class="grid-2">
                    <div>
                        <label for="editar_precio_compra">Precio de compra</label>
                        <input type="number" id="editar_precio_compra" name="editar_precio_compra" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label for="editar_precio_venta">Precio de venta</label>
                        <input type="number" id="editar_precio_venta" name="editar_precio_venta" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="editar_stock_minimo">Stock mínimo</label>
                        <input type="number" id="editar_stock_minimo" name="editar_stock_minimo" min="0" required>
                    </div>
                    <div>
                        <label for="editar_stock_actual">Stock actual</label>
                        <input type="number" id="editar_stock_actual" name="editar_stock_actual" min="0" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="editar_id_marca">Marca</label>
                        <select id="editar_id_marca" name="editar_id_marca" required></select>
                    </div>
                    <div>
                        <label for="editar_id_unidad_medida">Unidad de medida</label>
                        <select id="editar_id_unidad_medida" name="editar_id_unidad_medida" required></select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="editar_id_categoria">Categoría</label>
                        <select id="editar_id_categoria" name="editar_id_categoria" required></select>
                    </div>
                    <p class="nota-ayuda">
                        La subcategoría depende de la categoría. Por favor, seleccione primero una categoría
                    </p>
                    <div>
                        <label for="editar_id_sub_categoria">Subcategoría</label>
                        <select id="editar_id_sub_categoria" name="editar_id_sub_categoria"></select>
                    </div>
                </div>

                <input type="hidden" id="editar_id_estado_logico" name="editar_id_estado_logico">

                <label for="editar_imagen_producto">Reemplazar imagen (opcional)</label>
                <input type="file" id="editar_imagen_producto" name="editar_imagen_producto" accept="image/*">

                <div id="previewEditar" style="margin-top:10px;text-align:center;">
                    <img id="imagen_actual_preview" src="" alt="Vista previa" style="max-width:100px;border-radius:5px;display:none;">
                </div>

                <div class="acciones-modal">
                    <button type="button" id="btnCancelarEditar" class="btn-secundario">Cancelar</button>
                    <button type="submit" class="btn-primario">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script principal -->
    <script type="module" src="assets/js/productos.js"></script>