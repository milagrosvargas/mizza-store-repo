import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";

document.addEventListener("DOMContentLoaded", () => {
    const buscar = document.getElementById("buscar");
    const ordenar = document.getElementById("ordenar");
    const porPagina = document.getElementById("registrosPorPagina");
    const tablaBody = document.querySelector("#tablaUnidades tbody");
    const paginador = document.getElementById("paginador");
    const btnNuevo = document.getElementById("btnNuevo");

    const modalNuevo = document.getElementById("modalNuevaUnidad");
    const formNuevo = document.getElementById("formNuevaUnidad");
    const inputNombre = document.getElementById("nombre_unidad_medida");
    const btnCancelarModal = document.getElementById("btnCancelarModal");

    const modalEditar = document.getElementById("modalEditarUnidad");
    const formEditar = document.getElementById("formEditarUnidad");
    const inputEditarId = document.getElementById("editar_id_unidad");
    const inputEditarNombre = document.getElementById("editar_nombre_unidad");
    const btnCancelarEditar = document.getElementById("btnCancelarEditar");

    let paginaActual = 1;

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
    }
    function cerrarModalEditar() {
        modalEditar.style.display = "none";
        formEditar.reset();
    }

    function cargarDatos() {
        const data = `buscar=${encodeURIComponent(buscar.value)}&orden=${encodeURIComponent(ordenar.value)}&pagina=${paginaActual}&porPagina=${porPagina.value}`;

        fetch("index.php?controller=Master&action=listarUnidades", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: data
        })
        .then(res => res.json())
        .then(data => {
            tablaBody.innerHTML = "";
            paginador.innerHTML = "";

            if (!data.data || data.data.length === 0) {
                tablaBody.innerHTML = "<tr><td colspan='2'>No se encontraron resultados</td></tr>";
                return;
            }

            data.data.forEach(u => {
                tablaBody.insertAdjacentHTML("beforeend", `
                    <tr>
                        <td>${u.nombre_unidad_medida}</td>
                        <td class="acciones">
                            <button class="btn-accion btn-editar" data-id="${u.id_unidad_medida}" data-nombre="${u.nombre_unidad_medida}">
                                <img src="assets/images/icons/edit.png" class="icono-tabla" alt="Editar">
                            </button>
                            <button class="btn-accion btn-eliminar" data-id="${u.id_unidad_medida}">
                                <img src="assets/images/icons/delete.png" class="icono-tabla" alt="Eliminar">
                            </button>
                        </td>
                    </tr>
                `);
            });

            document.querySelectorAll(".btn-editar").forEach(btn =>
                btn.addEventListener("click", () => abrirModalEditar(btn.dataset.id, btn.dataset.nombre))
            );
            document.querySelectorAll(".btn-eliminar").forEach(btn =>
                btn.addEventListener("click", () => eliminarUnidad(btn.dataset.id))
            );

            const totalPaginas = Math.ceil(data.total / data.porPagina);
            for (let i = 1; i <= totalPaginas; i++) {
                const boton = document.createElement("button");
                boton.textContent = i;
                if (i === paginaActual) boton.classList.add("active");
                boton.addEventListener("click", () => { paginaActual = i; cargarDatos(); });
                paginador.appendChild(boton);
            }
        })
        .catch(() => tablaBody.innerHTML = "<tr><td colspan='2'>Error al cargar los datos</td></tr>");
    }

    formNuevo.addEventListener("submit", e => {
        e.preventDefault();
        const nombre = inputNombre.value.trim();
        if (nombre.length < 2) {
            mostrarError("Nombre inválido", "Debe tener al menos 2 caracteres.");
            return;
        }

        fetch("index.php?controller=Master&action=crearUnidad", {
            method: "POST",
            body: new URLSearchParams({ nombre_unidad_medida: nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Éxito", data.message);
                cerrarModalNuevo();
                cargarDatos();
            } else mostrarError("Error", data.message);
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    });

    formEditar.addEventListener("submit", e => {
        e.preventDefault();
        const id = inputEditarId.value;
        const nombre = inputEditarNombre.value.trim();
        if (nombre.length < 2) {
            mostrarError("Nombre inválido", "Debe tener al menos 2 caracteres.");
            return;
        }

        fetch("index.php?controller=Master&action=editarUnidad", {
            method: "POST",
            body: new URLSearchParams({ id_unidad_medida: id, nombre_unidad_medida: nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Actualizado", data.message);
                cerrarModalEditar();
                cargarDatos();
            } else mostrarError("Error", data.message);
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    });

    async function eliminarUnidad(id) {
        const confirmacion = await mostrarConfirmacion("¿Eliminar unidad?", "Esta acción no se puede deshacer.");
        if (!confirmacion.isConfirmed) return;

        fetch("index.php?controller=Master&action=eliminarUnidad", {
            method: "POST",
            body: new URLSearchParams({ id_unidad_medida: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarExito("Eliminado", data.message);
                cargarDatos();
            } else mostrarError("Error", data.message);
        })
        .catch(() => mostrarError("Error", "No se pudo conectar con el servidor"));
    }

    // Eventos de interfaz
    btnNuevo.addEventListener("click", abrirModalNuevo);
    btnCancelarModal.addEventListener("click", cerrarModalNuevo);
    btnCancelarEditar.addEventListener("click", cerrarModalEditar);
    window.addEventListener("click", e => {
        if (e.target === modalNuevo) cerrarModalNuevo();
        if (e.target === modalEditar) cerrarModalEditar();
    });

    buscar.addEventListener("keyup", () => { paginaActual = 1; cargarDatos(); });
    ordenar.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });
    porPagina.addEventListener("change", () => { paginaActual = 1; cargarDatos(); });

    cargarDatos();
});
