/**
 * ===========================================
 * 🌎 Módulo general para combos anidados de ubicación
 * -------------------------------------------
 * Uso:
 * import { inicializarUbicaciones } from "./assets/js/ubicaciones.js";
 * 
 * document.addEventListener("DOMContentLoaded", () => {
 *     const pais = document.getElementById("pais");
 *     const provincia = document.getElementById("provincia");
 *     const ciudad = document.getElementById("ciudad");
 *     const barrio = document.getElementById("barrio");
 * 
 *     inicializarUbicaciones(pais, provincia, ciudad, barrio);
 * });
 * ===========================================
 */

export function inicializarUbicaciones(paisSelect, provinciaSelect, localidadSelect, barrioSelect, callback = null) {

    /** -------------------------------
     * 🔧 Funciones utilitarias
     * ------------------------------- */
    function limpiar(select) {
        select.innerHTML = `<option value="" disabled selected hidden>Seleccione...</option>`;
    }

    function cargarOpciones(select, data, valueField, textField) {
        limpiar(select);
        data.forEach(item => {
            const opt = document.createElement("option");
            opt.value = item[valueField];
            opt.textContent = item[textField];
            select.appendChild(opt);
        });
    }

    function manejarError(mensaje = "No se pudo cargar la información.") {
        mostrarError("Error de conexión", mensaje);
    }

    /** -------------------------------
     * 🌍 País → Provincia
     * ------------------------------- */
    paisSelect.addEventListener("change", () => {
        const idPais = paisSelect.value;
        if (!idPais) {
            limpiar(provinciaSelect);
            limpiar(localidadSelect);
            limpiar(barrioSelect);
            return;
        }

        fetch(`index.php?controller=Ubicacion&action=obtenerProvincias&id_pais=${idPais}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cargarOpciones(provinciaSelect, data.data, "id_provincia", "nombre_provincia");
                    limpiar(localidadSelect);
                    limpiar(barrioSelect);
                } else {
                    manejarError(data.message);
                }
            })
            .catch(() => manejarError());
    });

    /** -------------------------------
     * 🗺️ Provincia → Localidad
     * ------------------------------- */
    provinciaSelect.addEventListener("change", () => {
        const idProvincia = provinciaSelect.value;
        if (!idProvincia) {
            limpiar(localidadSelect);
            limpiar(barrioSelect);
            return;
        }

        fetch(`index.php?controller=Ubicacion&action=obtenerLocalidades&id_provincia=${idProvincia}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cargarOpciones(localidadSelect, data.data, "id_localidad", "nombre_localidad");
                    limpiar(barrioSelect);
                } else {
                    manejarError(data.message);
                }
            })
            .catch(() => manejarError());
    });

    /** -------------------------------
     * 🏘️ Localidad → Barrio
     * ------------------------------- */
    localidadSelect.addEventListener("change", () => {
        const idLocalidad = localidadSelect.value;
        if (!idLocalidad) {
            limpiar(barrioSelect);
            return;
        }

        fetch(`index.php?controller=Ubicacion&action=obtenerBarrios&id_localidad=${idLocalidad}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cargarOpciones(barrioSelect, data.data, "id_barrio", "nombre_barrio");
                } else {
                    manejarError(data.message);
                }
            })
            .catch(() => manejarError());
    });

    /** -------------------------------
     * 🧱 Callback opcional (cuando cambia el barrio)
     * ------------------------------- */
    if (barrioSelect && typeof callback === "function") {
        barrioSelect.addEventListener("change", () => {
            callback(barrioSelect.value);
        });
    }
}
