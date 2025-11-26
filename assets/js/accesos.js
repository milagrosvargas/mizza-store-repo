// =====================================================================
// JS: accesos.js
// ---------------------------------------------------------------------
// Sección: Gestión de Accesos
// Descripción: Renderiza los módulos vs perfiles en una tabla con toggles.
// Restricción: El perfil "Invitado" solo puede visualizar la web externa,
//              por lo tanto, NO puede autorizar ni desautorizar accesos.
// Dependencia: alertas.js (SweetAlert2 personalizado)
// =====================================================================

import { mostrarExito, mostrarError, mostrarConfirmacion } from './alertas.js';

document.addEventListener("DOMContentLoaded", () => {
    const theadModulos = document.getElementById("theadModulos");
    const tbodyAccesos = document.getElementById("tbodyAccesos");

    // =====================================================
    // 📄 CARGAR ACCESOS (modulos ↔ perfiles)
    // =====================================================
    async function cargarAccesos() {
        try {
            const res = await fetch("index.php?controller=Master&action=listarAccesos");
            const data = await res.json();

            if (!data || !data.perfiles || !data.modulos) {
                mostrarError("Error", "Respuesta inválida del servidor.");
                return;
            }

            const { perfiles, modulos, relaciones } = data;

            // --- Generar cabecera dinámica ---
            theadModulos.innerHTML = "<th>Perfil</th>";
            modulos.forEach(m => {
                theadModulos.innerHTML += `<th>${m.descripcion_modulo}</th>`;
            });

            // --- Generar cuerpo de la tabla ---
            tbodyAccesos.innerHTML = "";
            perfiles.forEach(p => {
                const isInvitado = p.descripcion_perfil.trim().toLowerCase() === "invitado";
                let fila = `<tr><td>${p.descripcion_perfil}</td>`;

                modulos.forEach(m => {
                    const activo = relaciones.some(
                        r => r.relacion_modulo == m.id_modulo && r.relacion_perfil == p.id_perfil
                    );

                    fila += `
                        <td>
                            <label class="switch">
                                <input type="checkbox"
                                       data-perfil="${p.id_perfil}"
                                       data-modulo="${m.id_modulo}"
                                       ${activo ? "checked" : ""}
                                       ${isInvitado ? "disabled" : ""}>
                                <span class="slider"></span>
                            </label>
                        </td>`;
                });

                fila += "</tr>";
                tbodyAccesos.insertAdjacentHTML("beforeend", fila);
            });

            asignarEventosToggle();
        } catch (err) {
            mostrarError("Error", "No se pudieron cargar los accesos.");
            console.error(err);
        }
    }

    // =====================================================
    // 🎚️ ASIGNAR EVENTOS A LOS TOGGLES
    // =====================================================
    function asignarEventosToggle() {
        document.querySelectorAll(".switch input:not(:disabled)").forEach(toggle => {
            toggle.addEventListener("change", async e => {
                const idPerfil = e.target.dataset.perfil;
                const idModulo = e.target.dataset.modulo;
                const estado = e.target.checked;

                // Confirmar acción
                const confirmacion = await mostrarConfirmacion(
                    estado ? "¿Autorizar acceso?" : "¿Revocar acceso?",
                    estado
                        ? "El perfil obtendrá acceso a este módulo."
                        : "El perfil perderá el acceso a este módulo."
                );

                if (!confirmacion.isConfirmed) {
                    e.target.checked = !estado;
                    return;
                }

                const datos = new URLSearchParams();
                datos.append("id_perfil", idPerfil);
                datos.append("id_modulo", idModulo);
                datos.append("estado", estado);

                try {
                    const res = await fetch("index.php?controller=Master&action=toggleAcceso", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: datos.toString()
                    });

                    const resp = await res.json();

                    if (!resp.success) {
                        e.target.checked = !estado;
                        mostrarError("Error", resp.message || "No se pudo actualizar el acceso.");
                    } else {
                        mostrarExito("Éxito", resp.message || "Acceso actualizado correctamente.");
                    }
                } catch (error) {
                    e.target.checked = !estado;
                    mostrarError("Error", "Error de conexión con el servidor.");
                }
            });
        });
    }

    // =====================================================
    // 💡 POPUP INFORMATIVO INICIAL
    // =====================================================
    const popupInfo = document.getElementById("popupInfo");
    const cerrarPopup = document.getElementById("cerrarPopup");

    if (popupInfo) {
        popupInfo.style.display = "flex";
        if (cerrarPopup) {
            cerrarPopup.addEventListener("click", () => {
                popupInfo.style.display = "none";
            });
        }
    }

    // 🚀 Iniciar
    cargarAccesos();
});
