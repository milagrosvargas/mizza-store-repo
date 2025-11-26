import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const tablaBody = document.querySelector("#tablaTipoNota tbody");
    const inputBuscar = document.getElementById("buscar");
    const selectOrdenar = document.getElementById("ordenar");
    const selectCantidad = document.getElementById("registrosPorPagina");
    const btnNuevo = document.getElementById("btnNuevo");
    const modal = document.getElementById("modalTipoNota");
    const form = document.getElementById("formTipoNota");
    const inputId = document.getElementById("id_tipo_nota");
    const inputNombre = document.getElementById("nombre_tipo_nota");
    const btnCancelar = document.getElementById("btnCancelarModal");
    const tituloModal = document.getElementById("tituloModal");
    const paginador = document.getElementById("paginador");

    let paginaActual = 1;

    function abrirModal(titulo = "Nuevo tipo de nota", data = null) {
        tituloModal.textContent = titulo;
        modal.style.display = "flex";
        if (data) {
            inputId.value = data.id_tipo_nota;
            inputNombre.value = data.nombre_tipo_nota;
        } else {
            form.reset();
        }
        inputNombre.focus();
    }

    function cerrarModal() {
        modal.style.display = "none";
        form.reset();
    }

    function cargarDatos() {
        const buscar = inputBuscar.value.trim();
        const orden = selectOrdenar.value;
        const porPagina = selectCantidad.value;

        fetch("index.php?controller=Master&action=listarTiposNota", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `buscar=${buscar}&orden=${orden}&pagina=${paginaActual}&porPagina=${porPagina}`
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            if (!data.data || data.data.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='2'>No se encontraron registros</td></tr>";
                return;
            }

            data.data.forEach(n => {
                const fila = `
                    <tr>
                        <td>${n.nombre_tipo_nota}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${n.id_tipo_nota}" data-nombre="${n.nombre_tipo_nota}">
                                <img src="assets/images/icons/edit.png" class="icono-tabla" alt="Editar">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${n.id_tipo_nota}">
                                <img src="assets/images/icons/delete.png" class="icono-tabla" alt="Eliminar">
                            </button>
                        </td>
                    </tr>
                `;
                tablaBody.insertAdjacentHTML("beforeend", fila);
            });

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", () =>
                    abrirModal("Editar tipo de nota", {
                        id_tipo_nota: btn.dataset.id,
                        nombre_tipo_nota: btn.dataset.nombre
                    })
                );
            });

            document.querySelectorAll(".btn-eliminar").forEach(btn => {
                btn.addEventListener("click", () => eliminar(btn.dataset.id));
            });

            // Paginador
            paginador.innerHTML = "";
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
        .catch(() => tablaBody.innerHTML = "<tr><td colspan='2'>Error al cargar datos</td></tr>");
    }

    form.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();

        if (nombre.length < 3) {
            mostrarError("Error", "El nombre debe tener al menos 3 caracteres.");
            return;
        }

        const formData = new FormData(form);
        const accion = inputId.value ? "editarTipoNota" : "crearTipoNota";

        fetch(`index.php?controller=Master&action=${accion}`, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Éxito", data.message);
                cerrarModal();
                cargarDatos();
            } else {
                mostrarError("Error", data.message);
            }
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    });

    async function eliminar(id) {
        const confirmacion = await mostrarConfirmacion(
            "¿Eliminar tipo de nota?",
            "Esta acción no se puede deshacer."
        );

        if (!confirmacion.isConfirmed) return;

        const datos = new URLSearchParams();
        datos.append("id_tipo_nota", id);

        fetch("index.php?controller=Master&action=eliminarTipoNota", {
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

    btnNuevo.addEventListener("click", () => abrirModal());
    btnCancelar.addEventListener("click", cerrarModal);
    window.addEventListener("click", e => { if (e.target === modal) cerrarModal(); });
    inputBuscar.addEventListener("keyup", () => { paginaActual = 1; cargarDatos(); });
    selectOrdenar.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });
    selectCantidad.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });

    cargarDatos();
});
