import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    // 🔹 Elementos principales
    const inputBuscar = document.getElementById("buscarPais");
    const selectOrdenar = document.getElementById("ordenarPais");
    const selectCantidad = document.getElementById("registrosPorPaginaPais");
    const tablaBody = document.querySelector("#tablaPaises tbody");
    const paginador = document.getElementById("paginadorPais");
    const btnNuevo = document.getElementById("btnNuevoPais");

    // 🔹 Modal NUEVO
    const modalNuevo = document.getElementById("modalNuevoPais");
    const formNuevo = document.getElementById("formNuevoPais");
    const inputNombre = document.getElementById("nombre_pais");
    const btnCancelarModal = document.getElementById("btnCancelarModalPais");

    // 🔹 Modal EDITAR
    const modalEditar = document.getElementById("modalEditarPais");
    const formEditar = document.getElementById("formEditarPais");
    const inputEditarId = document.getElementById("editar_id_pais");
    const inputEditarNombre = document.getElementById("editar_nombre_pais");
    const btnCancelarEditar = document.getElementById("btnCancelarEditarPais");

    let paginaActual = 1;

    /** -------------------------------
     * 🧩 FUNCIONES DE MODALES
     * ------------------------------- */
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

    /** -------------------------------
     * 📄 CARGAR DATOS
     * ------------------------------- */
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarPaises", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `buscar=${encodeURIComponent(buscar)}&orden=${encodeURIComponent(orden)}&pagina=${paginaActual}&porPagina=${porPagina}`
        })
            .then(res => res.json())
            .then(data => {
                tablaBody.innerHTML = "";
                paginador.innerHTML = "";

                if (!data.datos || data.datos.length === 0) {
                    tablaBody.innerHTML = "<tr><td colspan='2' style='text-align:center;'>No se encontraron resultados</td></tr>";
                    return;
                }

                data.datos.forEach(e => {
                    const fila = `
                        <tr>
                            <td>${e.nombre_pais}</td>
                            <td class="acciones">
                                <button class="btn-accion btn-editar" data-id="${e.id_pais}" data-nombre="${e.nombre_pais}">
                                    <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                                </button>
                                <button class="btn-accion btn-eliminar" data-id="${e.id_pais}">
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
                    btn.addEventListener("click", () => eliminarPais(btn.dataset.id));
                });

                // Paginador
                for (let i = 1; i <= data.total_paginas; i++) {
                    const boton = document.createElement("button");
                    boton.textContent = i;
                    boton.className = "btn-pagina";
                    if (i === paginaActual) boton.classList.add("activo");
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

    /** -------------------------------
     * ➕ CREAR PAÍS
     * ------------------------------- */
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de país.");
            return;
        }

        fetch("index.php?controller=Master&action=crearPais", {
            method: "POST",
            body: new URLSearchParams({ nombre_pais: nombre })
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

    /** -------------------------------
     * ✏️ EDITAR PAÍS
     * ------------------------------- */
    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const nombre = inputEditarNombre.value.trim();

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de país.");
            return;
        }

        fetch("index.php?controller=Master&action=editarPais", {
            method: "POST",
            body: new URLSearchParams({ id_pais: id, nombre_pais: nombre })
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

    /** -------------------------------
     * 🗑️ ELIMINAR PAÍS
     * ------------------------------- */
    async function eliminarPais(id) {
        const confirmacion = await mostrarConfirmacion("¿Eliminar país?", "Esta acción no se puede deshacer.");

        if (!confirmacion.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarPais", {
            method: "POST",
            body: new URLSearchParams({ id_pais: id })
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

    /** -------------------------------
     * 🎛️ EVENTOS DE INTERFAZ
     * ------------------------------- */
    btnNuevo.addEventListener("click", abrirModalNuevo);
    btnCancelarModal.addEventListener("click", cerrarModalNuevo);
    btnCancelarEditar.addEventListener("click", cerrarModalEditar);

    window.addEventListener("click", e => {
        if (e.target === modalNuevo) cerrarModalNuevo();
        if (e.target === modalEditar) cerrarModalEditar();
    });

    inputBuscar.addEventListener("keyup", () => { paginaActual = 1; cargarDatos(); });
    selectOrdenar.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });
    selectCantidad.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });

    // Inicialización
    cargarDatos();
});
