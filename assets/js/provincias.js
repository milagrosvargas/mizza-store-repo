import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaProvincias tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    // === Modales ===
    const modalNuevo = document.getElementById("modalNuevaProvincia");
    const formNuevo = document.getElementById("formNuevaProvincia");
    const inputNombre = document.getElementById("nombre_provincia");
    const selectPais = document.getElementById("id_pais");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarProvincia");
    const formEditar = document.getElementById("formEditarProvincia");
    const inputEditarId = document.getElementById("editar_id_provincia");
    const inputEditarNombre = document.getElementById("editar_nombre_provincia");
    const selectEditarPais = document.getElementById("editar_id_pais");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

    // === Cargar países para los select ===
    function cargarPaisesSelect() {
        fetch("index.php?controller=Master&action=listarPaisesSelect")
            .then(res => res.json())
            .then(data => {
                selectPais.innerHTML = '<option value="">Seleccione un país</option>';
                selectEditarPais.innerHTML = '<option value="">Seleccione un país</option>';
                data.forEach(p => {
                    const opt1 = new Option(p.nombre_pais, p.id_pais);
                    const opt2 = new Option(p.nombre_pais, p.id_pais);
                    selectPais.add(opt1);
                    selectEditarPais.add(opt2);
                });
            })
            .catch(() => console.error("Error al cargar países"));
    }

    // === Modales ===
    function abrirModalNuevo() {
        modalNuevo.style.display = "flex";
        inputNombre.focus();
        cargarPaisesSelect();
    }
    function cerrarModalNuevo() {
        modalNuevo.style.display = "none";
        formNuevo.reset();
    }
    function abrirModalEditar(id, nombre, id_pais) {
        modalEditar.style.display = "flex";
        inputEditarId.value = id;
        inputEditarNombre.value = nombre;
        cargarPaisesSelect();
        setTimeout(() => {
            selectEditarPais.value = id_pais;
        }, 200);
    }
    function cerrarModalEditar() {
        modalEditar.style.display = "none";
        formEditar.reset();
    }

    // === Cargar provincias ===
    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarProvincias", {
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
                        <td>${e.nombre_provincia}</td>
                        <td>${e.nombre_pais}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${e.id_provincia}" data-nombre="${e.nombre_provincia}" data-pais="${e.id_pais}">
                                <img src="assets/images/icons/edit.png" alt="Editar" class="icono-tabla">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${e.id_provincia}">
                                <img src="assets/images/icons/delete.png" alt="Eliminar" class="icono-tabla">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () => {
                    abrirModalEditar(btn.dataset.id, btn.dataset.nombre, btn.dataset.pais);
                });
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminarProvincia(btn.dataset.id));
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
        const pais = selectPais.value;

        if (nombre === "" || pais === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y país.");
            return;
        }

        fetch("index.php?controller=Master&action=crearProvincia", {
            method: "POST",
            body: new URLSearchParams({ nombre_provincia: nombre, id_pais: pais })
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
        const pais = selectEditarPais.value;

        if (nombre === "" || pais === "") {
            mostrarError("Campos incompletos", "Debe ingresar nombre y país.");
            return;
        }

        fetch("index.php?controller=Master&action=editarProvincia", {
            method: "POST",
            body: new URLSearchParams({ id_provincia: id, nombre_provincia: nombre, id_pais: pais })
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
    async function eliminarProvincia(id) {
        const confirmacion = await mostrarConfirmacion("¿Eliminar provincia?", "Esta acción no se puede deshacer.");
        if (!confirmacion.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarProvincia", {
            method: "POST",
            body: new URLSearchParams({ id_provincia: id })
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
