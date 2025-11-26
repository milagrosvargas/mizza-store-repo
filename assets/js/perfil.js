import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {

    // 🔹 Elementos principales
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaPerfiles tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // 🔹 Modal NUEVO
    const modalNuevo = document.getElementById("modalNuevoPerfil");
    const formNuevo = document.getElementById("formNuevoPerfil");
    const inputDescripcion = document.getElementById("descripcion_perfil");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    // 🔹 Modal EDITAR
    const modalEditar = document.getElementById("modalEditarPerfil");
    const formEditar = document.getElementById("formEditarPerfil");
    const inputEditarId = document.getElementById("editar_id_perfil");
    const inputEditarDescripcion = document.getElementById("editar_descripcion_perfil");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    /** -------------------------------
     * 🧩 FUNCIONES DE MODALES
     * ------------------------------- */
    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        inputDescripcion.focus();
    }

    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
        formNuevo.reset();
    }

    function abrirModalEditar(id, descripcion) {
        modalEditar.style.display = "flex";
        inputEditarId.value = id;
        inputEditarDescripcion.value = descripcion;
        inputEditarDescripcion.focus();
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
        const porPagina = parseInt(selectCantidad.value);

        const datos = new URLSearchParams();
        datos.append("buscar", buscar);
        datos.append("orden", orden);
        datos.append("pagina", paginaActual);
        datos.append("porPagina", porPagina);

        fetch("index.php?controller=Master&action=listarPerfiles", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: datos.toString()
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            paginador.innerHTML = "";

            if (!data.datos || data.datos.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='2'>No se encontraron resultados</td></tr>";
                return;
            }

            data.datos.forEach(p => {
                const fila = `
                    <tr data-id="${p.id_perfil}">
                        <td>${p.descripcion_perfil}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${p.id_perfil}" data-descripcion="${p.descripcion_perfil}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${p.id_perfil}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            // 🎯 Reasignar eventos dinámicos
            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => {
                    abrirModalEditar(btn.dataset.id, btn.dataset.descripcion);
                });
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarPerfil(btn.dataset.id));
            });

            // 📄 Paginador
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
     * ➕ CREAR PERFIL
     * ------------------------------- */
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const descripcion = inputDescripcion.value.trim();

        if (descripcion === "") {
            mostrarError("Campo vacío", "Debe ingresar una descripción de perfil.");
            return;
        }
        if (descripcion.length < 3) {
            mostrarError("Texto demasiado corto", "Debe tener al menos 3 caracteres.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("descripcion_perfil", descripcion);

        fetch("index.php?controller=Master&action=crearPerfil", {
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
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
    });

    /** -------------------------------
     * ✏️ EDITAR PERFIL
     * ------------------------------- */
    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const descripcion = inputEditarDescripcion.value.trim();

        if (descripcion === "") {
            mostrarError("Campo vacío", "Debe ingresar una descripción de perfil.");
            return;
        }
        if (descripcion.length < 3) {
            mostrarError("Texto demasiado corto", "Debe tener al menos 3 caracteres.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("id_perfil", id);
        datos.append("descripcion_perfil", descripcion);

        fetch("index.php?controller=Master&action=editarPerfil", {
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
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
    });

    /** -------------------------------
     * 🗑️ ELIMINAR PERFIL
     * ------------------------------- */
    async function eliminarPerfil(id) {
        const confirmacion = await mostrarConfirmacion(
            "¿Eliminar perfil?",
            "Esta acción no se puede deshacer."
        );

        if (!confirmacion.isConfirmed) return;

        const datos = new URLSearchParams();
        datos.append("id_perfil", id);

        fetch("index.php?controller=Master&action=eliminarPerfil", {
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
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor."));
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
