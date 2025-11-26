import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    // 🔹 Elementos comunes
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaEstados tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // 🔹 Modal NUEVO
    const modalNuevo = document.getElementById("modalNuevoEstado");
    const formNuevo = document.getElementById("formNuevoEstado");
    const inputNombre = document.getElementById("nombre_estado");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    // 🔹 Modal EDITAR
    const modalEditar = document.getElementById("modalEditarEstado");
    const formEditar = document.getElementById("formEditarEstado");
    const inputEditarId = document.getElementById("editar_id_estado");
    const inputEditarNombre = document.getElementById("editar_nombre_estado");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

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
     * 📄 CARGAR TABLA
     * ------------------------------- */
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarEstados", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `buscar=${encodeURIComponent(buscar)}&orden=${encodeURIComponent(orden)}&pagina=${paginaActual}&porPagina=${porPagina}`
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            paginador.innerHTML = "";

            if (!data.datos || data.datos.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='2'>No se encontraron resultados</td></tr>";
                return;
            }

            data.datos.forEach(e => {
                const fila = `
                    <tr data-id="${e.id_estado_logico}">
                        <td>${e.nombre_estado}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${e.id_estado_logico}" data-nombre="${e.nombre_estado}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${e.id_estado_logico}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            // 🎯 Eventos dinámicos
            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;
                    const nombre = btn.dataset.nombre;
                    abrirModalEditar(id, nombre);
                });
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarEstado(btn.dataset.id));
            });

            // 📄 Paginador dinámico
            for (let i = 1; i <= data.total_paginas; i++) {
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

    /** -------------------------------
     * ➕ CREAR ESTADO
     * ------------------------------- */
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de estado.");
            return;
        }

        if (nombre.length < 3) {
            mostrarError("Nombre demasiado corto", "Debe tener al menos 3 letras.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("nombre_estado", nombre);

        fetch("index.php?controller=Master&action=crearEstado", {
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

    /** -------------------------------
     * ✏️ EDITAR ESTADO
     * ------------------------------- */
    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const nombre = inputEditarNombre.value.trim();

        if (nombre === "") {
            mostrarError("Campo vacío", "Debe ingresar un nombre de estado.");
            return;
        }

        if (nombre.length < 3) {
            mostrarError("Nombre demasiado corto", "Debe tener al menos 3 letras.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("id_estado_logico", id);
        datos.append("nombre_estado", nombre);

        fetch("index.php?controller=Master&action=editarEstado", {
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

    /** -------------------------------
     * 🗑️ ELIMINAR ESTADO
     * ------------------------------- */
    async function eliminarEstado(id) {
        const confirmacion = await mostrarConfirmacion(
            "¿Eliminar estado?",
            "Esta acción no se puede deshacer."
        );

        if (!confirmacion.isConfirmed) return;

        const datos = new URLSearchParams();
        datos.append("id_estado_logico", id);

        fetch("index.php?controller=Master&action=eliminarEstado", {
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
