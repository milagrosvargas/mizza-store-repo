import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaBarrios tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // === Modales ===
    const modalNuevo = document.getElementById("modalNuevoBarrio");
    const formNuevo = document.getElementById("formNuevoBarrio");
    const inputNombre = document.getElementById("nombre_barrio");
    const selectLocalidad = document.getElementById("id_localidad");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarBarrio");
    const formEditar = document.getElementById("formEditarBarrio");
    const inputEditarId = document.getElementById("editar_id_barrio");
    const inputEditarNombre = document.getElementById("editar_nombre_barrio");
    const selectEditarLocalidad = document.getElementById("editar_id_localidad");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    // === Cargar localidades para los select ===
    function cargarLocalidadesSelect() {
        fetch("index.php?controller=Master&action=listarLocalidadesSelect")
            .then(res => res.json())
            .then(data => {
                selectLocalidad.innerHTML = '<option value="">Seleccione una localidad</option>';
                selectEditarLocalidad.innerHTML = '<option value="">Seleccione una localidad</option>';
                data.forEach(l => {
                    const opt1 = new Option(l.nombre_localidad, l.id_localidad);
                    const opt2 = new Option(l.nombre_localidad, l.id_localidad);
                    selectLocalidad.add(opt1);
                    selectEditarLocalidad.add(opt2);
                });
            })
            .catch(() => console.error("Error al cargar localidades"));
    }

    // === Modales ===
    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        inputNombre.focus();
        cargarLocalidadesSelect();
    }

    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
        formNuevo.reset();
    }

    function abrirModalEditar(id, nombre, id_localidad) {
        modalEditar.style.display = "flex";
        inputEditarId.value = id;
        inputEditarNombre.value = nombre;
        cargarLocalidadesSelect();
        setTimeout(() => {
            selectEditarLocalidad.value = id_localidad;
        }, 200);
    }

    function cerrarModalEditar() {
        modalEditar.style.display = "none";
        formEditar.reset();
    }

    // === Cargar barrios ===
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarBarrios", {
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
                            <td>${e.nombre_barrio}</td>
                            <td>${e.nombre_localidad}</td>
                            <td class="acciones">
                                <button class="btn-accion btn-editar" data-id="${e.id_barrio}" data-nombre="${e.nombre_barrio}" data-localidad="${e.id_localidad}">
                                    <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                                </button>
                                <button class="btn-accion btn-eliminar" data-id="${e.id_barrio}">
                                    <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                                </button>
                            </td>
                        </tr>
                    `;
                    tablaBody.insertAdjacentHTML("beforeend", fila);
                });

                // === Botones editar/eliminar ===
                document.querySelectorAll(".btn-editar").forEach(btn => {
                    btn.addEventListener("click", () => {
                        abrirModalEditar(btn.dataset.id, btn.dataset.nombre, btn.dataset.localidad);
                    });
                });

                document.querySelectorAll(".btn-eliminar").forEach(btn => {
                    btn.addEventListener("click", () => eliminarBarrio(btn.dataset.id));
                });

                // === Paginador ===
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
        const localidad = selectLocalidad.value;

        if (nombre === "" || localidad === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y localidad.");
            return;
        }

        fetch("index.php?controller=Master&action=crearBarrio", {
            method: "POST",
            body: new URLSearchParams({ nombre_barrio: nombre, id_localidad: localidad })
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
        const localidad = selectEditarLocalidad.value;

        if (nombre === "" || localidad === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y localidad.");
            return;
        }

        fetch("index.php?controller=Master&action=editarBarrio", {
            method: "POST",
            body: new URLSearchParams({
                id_barrio: id,
                nombre_barrio: nombre,
                id_localidad: localidad
            })
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
    async function eliminarBarrio(id) {
        const confirmacion = await mostrarConfirmacion("¿Eliminar barrio?", "Esta acción no se puede deshacer.");
        if (!confirmacion.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarBarrio", {
            method: "POST",
            body: new URLSearchParams({ id_barrio: id })
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

    // 🚀 Inicial
    cargarDatos();
});
