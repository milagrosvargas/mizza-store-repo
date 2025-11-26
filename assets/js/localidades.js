import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaLocalidades tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // === Modales ===
    const modalNuevo = document.getElementById("modalNuevaLocalidad");
    const formNuevo = document.getElementById("formNuevaLocalidad");
    const inputNombre = document.getElementById("nombre_localidad");
    const selectProvincia = document.getElementById("id_provincia");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarLocalidad");
    const formEditar = document.getElementById("formEditarLocalidad");
    const inputEditarId = document.getElementById("editar_id_localidad");
    const inputEditarNombre = document.getElementById("editar_nombre_localidad");
    const selectEditarProvincia = document.getElementById("editar_id_provincia");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    // === Cargar provincias para los selects ===
    function cargarProvinciasSelect() {
        fetch("index.php?controller=Master&action=listarProvinciasSelect")
            .then(res => res.json())
            .then(data => {
                selectProvincia.innerHTML = '<option value="">Seleccione una provincia</option>';
                selectEditarProvincia.innerHTML = '<option value="">Seleccione una provincia</option>';
                data.forEach(p => {
                    const opt1 = new Option(p.nombre_provincia, p.id_provincia);
                    const opt2 = new Option(p.nombre_provincia, p.id_provincia);
                    selectProvincia.add(opt1);
                    selectEditarProvincia.add(opt2);
                });
            })
            .catch(() => console.error("Error al cargar provincias"));
    }

    // === Modales ===
    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        inputNombre.focus();
        cargarProvinciasSelect();
    }
    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
        formNuevo.reset();
    }
    function abrirModalEditar(id, nombre, id_provincia) {
        modalEditar.style.display = "flex";
        inputEditarId.value = id;
        inputEditarNombre.value = nombre;
        cargarProvinciasSelect();
        setTimeout(() => {
            selectEditarProvincia.value = id_provincia;
        }, 200);
    }
    function cerrarModalEditar() {
        modalEditar.style.display = "none";
        formEditar.reset();
    }

    // === Cargar localidades ===
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarLocalidades", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `buscar=${encodeURIComponent(buscar)}&orden=${encodeURIComponent(orden)}&pagina=${paginaActual}&porPagina=${porPagina}`
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            paginador.innerHTML = "";

            if (!data.datos || data.datos.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
                return;
            }

            data.datos.forEach(e => {
                const fila = `
                    <tr>
                        <td>${e.nombre_localidad}</td>
                        <td>${e.nombre_provincia}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${e.id_localidad}" data-nombre="${e.nombre_localidad}" data-provincia="${e.id_provincia}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${e.id_localidad}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => {
                    abrirModalEditar(btn.dataset.id, btn.dataset.nombre, btn.dataset.provincia);
                });
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarLocalidad(btn.dataset.id));
            });

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
            tablaBody.innerHTML = "<tr><td colspan='3'>Error al cargar los datos</td></tr>";
        });
    }

    // === Crear ===
    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();
        const provincia = selectProvincia.value;

        if (nombre === "" || provincia === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y provincia.");
            return;
        }

        fetch("index.php?controller=Master&action=crearLocalidad", {
            method: "POST",
            body: new URLSearchParams({ nombre_localidad: nombre, id_provincia: provincia })
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

    // === Editar ===
    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const nombre = inputEditarNombre.value.trim();
        const provincia = selectEditarProvincia.value;

        if (nombre === "" || provincia === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y provincia.");
            return;
        }

        fetch("index.php?controller=Master&action=editarLocalidad", {
            method: "POST",
            body: new URLSearchParams({ id_localidad: id, nombre_localidad: nombre, id_provincia: provincia })
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

    // === Eliminar ===
    async function eliminarLocalidad(id) {
        const confirmacion = await mostrarConfirmacion("¿Eliminar localidad?", "Esta acción no se puede deshacer.");
        if (!confirmacion.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarLocalidad", {
            method: "POST",
            body: new URLSearchParams({ id_localidad: id })
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

    // === Eventos globales ===
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
