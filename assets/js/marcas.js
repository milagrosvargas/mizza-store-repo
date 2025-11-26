import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    // 🔹 Elementos principales
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaMarcas tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // 🔹 Modal NUEVA MARCA
    const modalNuevo = document.getElementById("modalNuevaMarca");
    const formNuevo = document.getElementById("formNuevaMarca");
    const inputNombre = document.getElementById("nombre_marca");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    // 🔹 Modal EDITAR MARCA
    const modalEditar = document.getElementById("modalEditarMarca");
    const formEditar = document.getElementById("formEditarMarca");
    const inputEditarId = document.getElementById("editar_id_marca");
    const inputEditarNombre = document.getElementById("editar_nombre_marca");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    /* ===================================================
       🧩 FUNCIONES DE MODALES
    =================================================== */
    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        inputNombre.focus();
    }

    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
        formNuevo.reset();
    }

    function abrirModalEditar(id, nombre) {
        modalEditar.style.display = "flex";
        inputEditarId.value = id;
        inputEditarNombre.value = nombre;
        inputEditarNombre.focus();
    }

    function cerrarModalEditar() {
        modalEditar.style.display = "none";
        formEditar.reset();
    }

    /* ===================================================
       📄 CARGAR TABLA (AJAX)
    =================================================== */
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarMarcas", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `buscar=${encodeURIComponent(buscar)}&orden=${encodeURIComponent(orden)}&pagina=${paginaActual}&porPagina=${porPagina}`
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            paginador.innerHTML = "";

            if (!data.data || data.data.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='2'>No se encontraron resultados</td></tr>";
                return;
            }

            data.data.forEach(marca => {
                const fila = `
                    <tr data-id="${marca.id_marca}">
                        <td>${marca.nombre_marca}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${marca.id_marca}" data-nombre="${marca.nombre_marca}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${marca.id_marca}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            // Eventos dinámicos
            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => {
                    abrirModalEditar(btn.dataset.id, btn.dataset.nombre);
                });
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarMarca(btn.dataset.id));
            });

            // Paginador
            const totalPaginas = Math.ceil(data.total / data.porPagina);
            for (let i = 1; i <= totalPaginas; i++) {
                const boton = document.createElement("button");
                boton.textContent = i;
                if (i === paginaActual) boton.classList.add("active");
                boton.addEventListener("click", () => {
                    paginaActual = i;
                    cargarDatos();
                });
                paginador.appendChild(boton);
            }
        })
        .catch(() => {
            tablaBody.innerHTML = "<tr><td colspan='2'>Error al cargar los datos</td></tr>";
        });
    }

    /* ===================================================
       ➕ CREAR MARCA
    =================================================== */
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de marca.");
            return;
        }

        if (nombre.length < 2) {
            mostrarError("Nombre demasiado corto", "Debe tener al menos 2 caracteres.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("nombre_marca", nombre);

        fetch("index.php?controller=Master&action=crearMarca", {
            method: "POST",
            body: datos
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Éxito", data.message);
                cerrarModalNuevo();
                cargarDatos();
            } else {
                mostrarError("Error", data.message);
            }
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    });

    /* ===================================================
       ✏️ EDITAR MARCA
    =================================================== */
    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputEditarNombre.value.trim();
        const id = inputEditarId.value;

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de marca.");
            return;
        }

        if (nombre.length < 2) {
            mostrarError("Nombre demasiado corto", "Debe tener al menos 2 caracteres.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("id_marca", id);
        datos.append("nombre_marca", nombre);

        fetch("index.php?controller=Master&action=editarMarca", {
            method: "POST",
            body: datos
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Actualizado", data.message);
                cerrarModalEditar();
                cargarDatos();
            } else {
                mostrarError("Error", data.message);
            }
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    });

    /* ===================================================
       🗑️ ELIMINAR MARCA
    =================================================== */
    async function eliminarMarca(id) {
        const confirmacion = await mostrarConfirmacion(
            "¿Eliminar marca?",
            "Esta acción no se puede deshacer."
        );

        if (!confirmacion.isConfirmed) return;

        const datos = new URLSearchParams();
        datos.append("id_marca", id);

        fetch("index.php?controller=Master&action=eliminarMarca", {
            method: "POST",
            body: datos
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Eliminado", data.message);
                cargarDatos();
            } else {
                mostrarError("Error", data.message);
            }
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    }

    /* ===================================================
       🎛️ EVENTOS DE INTERFAZ
    =================================================== */
    btnNuevo.addEventListener("click", abrirModalNuevo);
    btnCancelarModal.addEventListener("click", cerrarModalNuevo);
    btnCancelarEditar.addEventListener("click", cerrarModalEditar);

    window.addEventListener("click", e => {
        if (e.target === modalNuevo) cerrarModalNuevo();
        if (e.target === modalEditar) cerrarModalEditar();
    });

    inputBuscar.addEventListener("keyup", () => {
        paginaActual = 1;
        cargarDatos();
    });

    selectOrdenar.addEventListener("change", () => {
        paginaActual = 1;
        cargarDatos();
    });

    selectCantidad.addEventListener("change", () => {
        paginaActual = 1;
        cargarDatos();
    });

    // 🚀 Inicializar
    cargarDatos();
});
