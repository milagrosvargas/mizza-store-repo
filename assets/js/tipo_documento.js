import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaTiposDocumento tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    const modalNuevo = document.getElementById("modalNuevoTipo");
    const formNuevo = document.getElementById("formNuevoTipo");
    const inputNombre = document.getElementById("nombre_tipo_documento");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarTipo");
    const formEditar = document.getElementById("formEditarTipo");
    const inputEditarId = document.getElementById("editar_id_tipo_documento");
    const inputEditarNombre = document.getElementById("editar_nombre_tipo_documento");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarTiposDocumento", {
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
                    <tr>
                        <td>${e.nombre_tipo_documento}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${e.id_tipo_documento}" data-nombre="${e.nombre_tipo_documento}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${e.id_tipo_documento}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => abrirModalEditar(btn.dataset.id, btn.dataset.nombre));
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarTipo(btn.dataset.id));
            });

            for (let i = 1; i <= data.total_paginas; i++) {
                const boton = document.createElement("button");
                boton.textContent = i;
                if (i === paginaActual) boton.classList.add("active");
                boton.addEventListener("click", () => { paginaActual = i; cargarDatos(); });
                paginador.appendChild(boton);
            }
        })
        .catch(() => tablaBody.innerHTML = "<tr><td colspan='2'>Error al cargar datos</td></tr>");
    }

    // Modal funciones
    const abrirModalNuevo = () => { modalNuevo.style.display = "flex"; inputNombre.focus(); };
    const cerrarModalNuevo = () => { modalNuevo.style.display = "none"; formNuevo.reset(); };
    const abrirModalEditar = (id, nombre) => { 
        modalEditar.style.display = "flex"; 
        inputEditarId.value = id; 
        inputEditarNombre.value = nombre; 
    };
    const cerrarModalEditar = () => { modalEditar.style.display = "none"; formEditar.reset(); };

    // CRUD
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();
        if (nombre === "") return mostrarError("Campos incompletos", "Debe ingresar un nombre.");

        fetch("index.php?controller=Master&action=crearTipoDocumento", {
            method: "POST",
            body: new URLSearchParams({ nombre_tipo_documento: nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Éxito", data.message);
                cerrarModalNuevo();
                cargarDatos();
            } else mostrarError("Error", data.message);
        });
    });

    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const nombre = inputEditarNombre.value.trim();
        if (nombre === "") return mostrarError("Campos incompletos", "Debe ingresar un nombre.");

        fetch("index.php?controller=Master&action=editarTipoDocumento", {
            method: "POST",
            body: new URLSearchParams({ id_tipo_documento: id, nombre_tipo_documento: nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Actualizado", data.message);
                cerrarModalEditar();
                cargarDatos();
            } else mostrarError("Error", data.message);
        });
    });

    async function eliminarTipo(id) {
        const conf = await mostrarConfirmacion("¿Eliminar tipo?", "Esta acción no se puede deshacer.");
        if (!conf.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarTipoDocumento", {
            method: "POST",
            body: new URLSearchParams({ id_tipo_documento: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Eliminado", data.message);
                cargarDatos();
            } else mostrarError("Error", data.message);
        });
    }

    // Eventos
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

    cargarDatos();
});
