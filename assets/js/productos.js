import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

// Previsualizar imagenes al seleccionar archivos

document.addEventListener("change", e => {
    const fileInput = e.target;

    // Nueva imagen
    if (fileInput.id === "imagen_producto") {
        const file = fileInput.files[0];
        const preview = document.getElementById("imagenPreviewNueva");
        if (file) {
            const reader = new FileReader();
            reader.onload = ev => {
                preview.src = ev.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    }

    // Editar imagen

    if (fileInput.id === "editar_imagen_producto") {
        const file = fileInput.files[0];
        const preview = document.getElementById("imagen_actual_preview");
        if (file) {
            const reader = new FileReader();
            reader.onload = ev => {
                preview.src = ev.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }
});

// Funcionalidad principal de gestión de productos

document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaProductos tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    const filtroCategoria = document.getElementById("filtroCategoria");
    const filtroSubCategoria = document.getElementById("filtroSubCategoria");
    const precioMin = document.getElementById("precioMin");
    const precioMax = document.getElementById("precioMax");

    const modalNuevo = document.getElementById("modalNuevoProducto");
    const formNuevo = document.getElementById("formNuevoProducto");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarProducto");
    const formEditar = document.getElementById("formEditarProducto");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");
    const imgPreviewEditar = document.getElementById("imagen_actual_preview");

    let paginaActual = 1;

    // Cargar datos iniciales para los combos

    async function cargarCombosIniciales() {
        try {
            // Categorías
            const resCat = await fetch("index.php?controller=Productos&action=listarCategorias");
            const catData = await resCat.json();
            filtroCategoria.innerHTML = `<option value="">Todas las categorías</option>`;
            document.getElementById("id_categoria").innerHTML = `<option value="">Seleccione</option>`;
            document.getElementById("editar_id_categoria").innerHTML = `<option value="">Seleccione</option>`;
            catData.data?.forEach(c => {
                const opt = `<option value="${c.id_categoria}">${c.nombre_categoria}</option>`;
                filtroCategoria.insertAdjacentHTML("beforeend", opt);
                document.getElementById("id_categoria").insertAdjacentHTML("beforeend", opt);
                document.getElementById("editar_id_categoria").insertAdjacentHTML("beforeend", opt);
            });

            // Marcas
            const resMarca = await fetch("index.php?controller=Productos&action=listarMarcas");
            const marcaData = await resMarca.json();
            document.getElementById("id_marca").innerHTML = `<option value="">Seleccione</option>`;
            document.getElementById("editar_id_marca").innerHTML = `<option value="">Seleccione</option>`;
            marcaData.data?.forEach(m => {
                const opt = `<option value="${m.id_marca}">${m.nombre_marca}</option>`;
                document.getElementById("id_marca").insertAdjacentHTML("beforeend", opt);
                document.getElementById("editar_id_marca").insertAdjacentHTML("beforeend", opt);
            });

            // Unidades de medida
            const resUni = await fetch("index.php?controller=Productos&action=listarUnidadesMedida");
            const uniData = await resUni.json();
            document.getElementById("id_unidad_medida").innerHTML = `<option value="">Seleccione</option>`;
            document.getElementById("editar_id_unidad_medida").innerHTML = `<option value="">Seleccione</option>`;
            uniData.data?.forEach(u => {
                const opt = `<option value="${u.id_unidad_medida}">${u.nombre_unidad_medida}</option>`;
                document.getElementById("id_unidad_medida").insertAdjacentHTML("beforeend", opt);
                document.getElementById("editar_id_unidad_medida").insertAdjacentHTML("beforeend", opt);
            });

            // Estado lógico fijo (no visible)

            const estadoInput = document.getElementById("id_estado_logico");
            if (estadoInput) estadoInput.value = 8; // 8 <- Estado lógico "Disponible"
        } catch (err) {
            console.error("Error al cargar combos:", err);
            mostrarError("Error", "No se pudieron cargar los combos iniciales.");
        }
    }

    // Cargar subcategorías según la categoría seleccionada

    async function cargarSubCategorias(selectCat, selectSub) {
        const idCategoria = selectCat.value;
        if (!idCategoria) {
            selectSub.innerHTML = `<option value="">Seleccione</option>`;
            return;
        }

        const res = await fetch("index.php?controller=Productos&action=listarSubCategoriasPorCategoria", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_categoria=${idCategoria}`
        });
        const data = await res.json();
        selectSub.innerHTML = `<option value="">Seleccione</option>`;
        if (data && data.data && Array.isArray(data.data)) {
            data.data.forEach(sc => {
                selectSub.insertAdjacentHTML("beforeend", `<option value="${sc.id_sub_categoria}">${sc.nombre_sub_categoria}</option>`);
            });
        }
    }

    // Evento al cambiar categoría en el filtro

    filtroCategoria.addEventListener("change", () => {
        cargarSubCategorias(filtroCategoria, filtroSubCategoria);
        paginaActual = 1;
        cargarProductos();
    });

    document.getElementById("id_categoria").addEventListener("change", () => {
        cargarSubCategorias(document.getElementById("id_categoria"), document.getElementById("id_sub_categoria"));
    });

    document.getElementById("editar_id_categoria").addEventListener("change", () => {
        cargarSubCategorias(document.getElementById("editar_id_categoria"), document.getElementById("editar_id_sub_categoria"));
    });

    // Cargar productos según filtros y paginación

    function cargarProductos() {
        const datos = new URLSearchParams();
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;
        const idCat = filtroCategoria.value;
        const idSub = filtroSubCategoria.value;
        const min = precioMin.value;
        const max = precioMax.value;

        datos.append("buscar", buscar);
        datos.append("orden", orden);
        datos.append("pagina", paginaActual);
        datos.append("porPagina", porPagina);
        if (idCat) datos.append("id_categoria", idCat);
        if (idSub) datos.append("id_sub_categoria", idSub);
        if (min !== "") datos.append("precio_min", min);
        if (max !== "") datos.append("precio_max", max);

        fetch("index.php?controller=Productos&action=listarProductos", {
            method: "POST",
            body: datos
        })
            .then(res => res.json())
            .then(data => {
                tablaBody.innerHTML = "";
                paginador.innerHTML = "";

                if (!data.success || !data.data?.length) {
                    tablaBody.innerHTML = "<tr><td colspan='10'>No se encontraron productos.</td></tr>";
                    return;
                }

                data.data.forEach(prod => {
                    const codigo = prod.codigo_barras ?? "—";
                    const nombre = prod.nombre_producto ?? "—";
                    const categoria = prod.nombre_categoria ?? "—";
                    const subcategoria = prod.nombre_sub_categoria ?? "—";
                    const marca = prod.nombre_marca ?? "—";
                    const precioCompra = prod.precio_compra !== null ? `$${parseFloat(prod.precio_compra).toFixed(2)}` : "$0.00";
                    const precioVenta = prod.precio_venta !== null ? `$${parseFloat(prod.precio_venta).toFixed(2)}` : "$0.00";
                    const stock = prod.stock_actual ?? 0;
                    const estado = prod.nombre_estado_logico ?? "—";
                    const imagen = prod.imagen_producto
                        ? `<img src="${prod.imagen_producto}" alt="Img" style="max-width:60px;border-radius:4px;">`
                        : "—";

                    const fila = `
                    <tr data-id="${prod.id_producto}">
                        <td>${codigo}</td>
                        <td>${nombre}</td>
                        <td>${categoria}</td>
                        <td>${subcategoria}</td>
                        <td>${marca}</td>
                        <td>${precioCompra}</td>
                        <td>${precioVenta}</td>
                        <td>${stock}</td>
                        <td>${estado}</td>
                        <td>${imagen}</td>
                        <td style="text-align:center;">
                            <button class="btn-accion btn-editar" data-prod='${JSON.stringify(prod)}'>
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${prod.id_producto}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>`;
                    tablaBody.insertAdjacentHTML("beforeend", fila);
                });

                // Paginador

                const totalPaginas = Math.ceil(data.total / data.porPagina);
                for (let i = 1; i <= totalPaginas; i++) {
                    const boton = document.createElement("button");
                    boton.textContent = i;
                    if (i === paginaActual) boton.classList.add("activo");
                    boton.addEventListener("click", () => {
                        paginaActual = i;
                        cargarProductos();
                    });
                    paginador.appendChild(boton);
                }

                // Acciones
                document.querySelectorAll(".btn-editar").forEach(btn => {
                    btn.addEventListener("click", () => abrirModalEditar(JSON.parse(btn.dataset.prod)));
                });
                document.querySelectorAll(".btn-eliminar").forEach(btn => {
                    btn.addEventListener("click", () => eliminarProducto(btn.dataset.id));
                });
            })
            .catch(() => {
                tablaBody.innerHTML = "<tr><td colspan='10'>Error al cargar los productos.</td></tr>";
            });
    }

    // Modales

    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        formNuevo.reset();
        document.getElementById("imagenPreviewNueva").style.display = "none";
        const estadoInput = document.getElementById("id_estado_logico");
        if (estadoInput) estadoInput.value = 25;
    }

    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
    }

    function abrirModalEditar(prod) {
        modalEditar.style.display = "flex";
        formEditar.reset();

        formEditar.editar_id_producto.value = prod.id_producto;
        formEditar.editar_codigo_barras.value = prod.codigo_barras ?? "";
        formEditar.editar_nombre_producto.value = prod.nombre_producto ?? "";
        formEditar.editar_descripcion_producto.value = prod.descripcion_producto ?? "";
        formEditar.editar_precio_compra.value = prod.precio_compra ?? "";
        formEditar.editar_precio_venta.value = prod.precio_venta ?? "";
        formEditar.editar_stock_minimo.value = prod.stock_minimo ?? "";
        formEditar.editar_stock_actual.value = prod.stock_actual ?? "";
        formEditar.editar_id_categoria.value = prod.id_categoria ?? "";
        formEditar.editar_id_marca.value = prod.id_marca ?? "";
        formEditar.editar_id_unidad_medida.value = prod.id_unidad_medida ?? "";
        if (formEditar.editar_id_estado_logico) {
            formEditar.editar_id_estado_logico.value = prod.id_estado_logico ?? 25;
        }

        cargarSubCategorias(formEditar.editar_id_categoria, formEditar.editar_id_sub_categoria)
            .then(() => formEditar.editar_id_sub_categoria.value = prod.id_sub_categoria ?? "");

        if (prod.imagen_producto) {
            imgPreviewEditar.src = prod.imagen_producto;
            imgPreviewEditar.style.display = "block";
        } else {
            imgPreviewEditar.style.display = "none";
        }
    }

    function cerrarModalEditar() {
        modalEditar.style.display = "none";
    }

    // Validación de formularios

    function validarFormulario(form) {
        const nombre = form.querySelector("[name$='nombre_producto']").value.trim();
        const precioCompra = parseFloat(form.querySelector("[name$='precio_compra']").value || 0);
        const precioVenta = parseFloat(form.querySelector("[name$='precio_venta']").value || 0);
        const stockActual = parseInt(form.querySelector("[name$='stock_actual']").value || 0);
        const stockMinimo = parseInt(form.querySelector("[name$='stock_minimo']").value || 0);

        if (nombre.length < 3) {
            mostrarError("Error", "El nombre debe tener al menos 3 caracteres.");
            return false;
        }
        if (precioCompra < 0 || precioVenta < 0) {
            mostrarError("Error", "Los precios no pueden ser negativos.");
            return false;
        }
        if (precioVenta < precioCompra) {
            mostrarError("Error", "El precio de venta no puede ser menor que el de compra.");
            return false;
        }
        if (stockActual < 0 || stockMinimo < 0) {
            mostrarError("Error", "El stock no puede ser negativo.");
            return false;
        }
        return true;
    }

    // Crear producto

    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        if (!validarFormulario(formNuevo)) return;

        const formData = new FormData(formNuevo);
        fetch("index.php?controller=Productos&action=crearProducto", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarExito("Éxito", data.message);
                    cerrarModalNuevo();
                    cargarProductos();
                } else mostrarError("Error", data.message);
            })
            .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
    });

    // Editar producto

    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        if (!validarFormulario(formEditar)) return;

        const formData = new FormData(formEditar);
        fetch("index.php?controller=Productos&action=editarProducto", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarExito("Actualizado", data.message);
                    cerrarModalEditar();
                    cargarProductos();
                } else mostrarError("Error", data.message);
            })
            .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
    });

    // Eliminar producto

    async function eliminarProducto(id) {
        const conf = await mostrarConfirmacion("¿Eliminar producto?", "Esta acción no se puede deshacer.");
        if (!conf.isConfirmed) return;

        const datos = new URLSearchParams();
        datos.append("id_producto", id);

        fetch("index.php?controller=Productos&action=eliminarProducto", {
            method: "POST",
            body: datos
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarExito("Eliminado", data.message);
                    cargarProductos();
                } else mostrarError("Error", data.message);
            })
            .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
    }

    btnNuevo.addEventListener("click", abrirModalNuevo);
    btnCancelarModal.addEventListener("click", cerrarModalNuevo);
    btnCancelarEditar.addEventListener("click", cerrarModalEditar);

    window.addEventListener("click", e => {
        if (e.target === modalNuevo) cerrarModalNuevo();
        if (e.target === modalEditar) cerrarModalEditar();
    });

    [inputBuscar, selectOrdenar, selectCantidad, filtroSubCategoria].forEach(el => {
        el.addEventListener("input", () => {
            paginaActual = 1;
            cargarProductos();
        });
    });

    [precioMin, precioMax].forEach(el => {
        el.addEventListener("input", () => {
            paginaActual = 1;
            cargarProductos();
        });
    });

    cargarCombosIniciales().then(cargarProductos);
});
