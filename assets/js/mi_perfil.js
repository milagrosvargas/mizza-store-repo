document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formEditarPerfil");

    const pais = document.getElementById("pais");
    const provincia = document.getElementById("provincia");
    const localidad = document.getElementById("localidad");
    const barrio = document.getElementById("barrio");

    // ===========================================================
    // UTILIDADES DE VALIDACIÓN
    // ===========================================================
    const mostrarError = (input, mensaje) => {
        input.classList.add("is-invalid");
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains("invalid-feedback")) {
            error = document.createElement("div");
            error.className = "invalid-feedback";
            input.parentNode.appendChild(error);
        }
        error.textContent = mensaje;
    };

    const limpiarError = (input) => {
        input.classList.remove("is-invalid");
        const error = input.nextElementSibling;
        if (error && error.classList.contains("invalid-feedback")) {
            error.textContent = "";
        }
    };

    const validarCampoTexto = (input, mensaje) => {
        const valor = input.value.trim();
        if (valor === "") {
            mostrarError(input, mensaje);
            return false;
        }
        limpiarError(input);
        return true;
    };

    const validarNumero = (input, mensaje) => {
        const valor = input.value.trim();
        if (!/^\d+$/.test(valor)) {
            mostrarError(input, mensaje);
            return false;
        }
        limpiarError(input);
        return true;
    };

    const validarFecha = (input, mensaje) => {
        const valor = input.value;
        if (!valor) {
            mostrarError(input, mensaje);
            return false;
        }

        const fechaNacimiento = new Date(valor);
        const hoy = new Date();
        const edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        if (edad < 18) {
            mostrarError(input, "Debe ser mayor de 18 años.");
            return false;
        }

        limpiarError(input);
        return true;
    };

    const validarSelect = (select, mensaje) => {
        if (!select.value) {
            mostrarError(select, mensaje);
            return false;
        }
        limpiarError(select);
        return true;
    };

    // ===========================================================
    // SELECTORES ANIDADOS (UbicacionController)
    // ===========================================================
    const cargarPaises = async () => {
        const res = await fetch("index.php?controller=Ubicacion&action=obtenerPaises");
        const data = await res.json();
        pais.innerHTML = '<option value="">Seleccione...</option>';
        data.data.forEach(p => pais.innerHTML += `<option value="${p.id_pais}">${p.nombre_pais}</option>`);
    };

    pais.addEventListener("change", async () => {
        provincia.innerHTML = "<option value=''>Seleccione...</option>";
        localidad.innerHTML = "<option value=''>Seleccione...</option>";
        barrio.innerHTML = "<option value=''>Seleccione...</option>";
        provincia.disabled = true; localidad.disabled = true; barrio.disabled = true;

        if (!pais.value) return;
        const res = await fetch(`index.php?controller=Ubicacion&action=obtenerProvincias&id_pais=${pais.value}`);
        const data = await res.json();
        provincia.disabled = false;
        data.data.forEach(pr => provincia.innerHTML += `<option value="${pr.id_provincia}">${pr.nombre_provincia}</option>`);
    });

    provincia.addEventListener("change", async () => {
        localidad.innerHTML = "<option value=''>Seleccione...</option>";
        barrio.innerHTML = "<option value=''>Seleccione...</option>";
        localidad.disabled = true; barrio.disabled = true;

        if (!provincia.value) return;
        const res = await fetch(`index.php?controller=Ubicacion&action=obtenerLocalidades&id_provincia=${provincia.value}`);
        const data = await res.json();
        localidad.disabled = false;
        data.data.forEach(l => localidad.innerHTML += `<option value="${l.id_localidad}">${l.nombre_localidad}</option>`);
    });

    localidad.addEventListener("change", async () => {
        barrio.innerHTML = "<option value=''>Seleccione...</option>";
        barrio.disabled = true;

        if (!localidad.value) return;
        const res = await fetch(`index.php?controller=Ubicacion&action=obtenerBarrios&id_localidad=${localidad.value}`);
        const data = await res.json();
        barrio.disabled = false;
        data.data.forEach(b => barrio.innerHTML += `<option value="${b.id_barrio}">${b.nombre_barrio}</option>`);
    });

    // ===========================================================
    // VALIDACIÓN Y ENVÍO DEL FORMULARIO
    // ===========================================================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valido = true;

        // Campos personales
        valido &= validarCampoTexto(form.nombre, "Ingrese su nombre.");
        valido &= validarCampoTexto(form.apellido, "Ingrese su apellido.");
        valido &= validarFecha(form.fecha_nac, "Seleccione su fecha de nacimiento.");
        valido &= validarSelect(form.genero, "Seleccione un género.");

        // Domicilio
        valido &= validarSelect(form.pais, "Seleccione un país.");
        valido &= validarSelect(form.provincia, "Seleccione una provincia.");
        valido &= validarSelect(form.localidad, "Seleccione una localidad.");
        valido &= validarSelect(form.barrio, "Seleccione un barrio.");
        valido &= validarCampoTexto(form.calle_direccion, "Ingrese la calle.");
        valido &= validarNumero(form.numero_direccion, "Ingrese un número válido.");

        // =========================================
        // ⚠️ Reemplazo del alert por mensaje visual
        // =========================================
        if (!valido) {
            const primerInvalido = form.querySelector(".is-invalid");
            if (primerInvalido) {
                primerInvalido.focus();
                primerInvalido.scrollIntoView({ behavior: "smooth", block: "center" });
            }

            let aviso = document.getElementById("formErrorMsg");
            if (!aviso) {
                aviso = document.createElement("div");
                aviso.id = "formErrorMsg";
                aviso.className = "alert alert-warning mt-2";
                aviso.style.fontSize = "0.9rem";
                aviso.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Por favor, complete los campos obligatorios correctamente.`;
                form.prepend(aviso);
            } else {
                aviso.style.display = "block";
            }

            setTimeout(() => {
                if (aviso) aviso.style.display = "none";
            }, 4000);

            return;
        }

        const formData = new FormData(form);

        try {
            const res = await fetch("index.php?controller=MiPerfil&action=actualizarPerfil", {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización exitosa',
                    text: data.mensaje,
                    confirmButtonColor: '#e06388'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.mensaje,
                    confirmButtonColor: '#e06388'
                });
            }
        } catch (error) {
            console.error("Error en la actualización:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error interno',
                text: 'Ocurrió un problema al enviar los datos.',
                confirmButtonColor: '#e06388'
            });
        }
    });

    // ===========================================================
    // CARGAR PAÍSES CUANDO SE ABRE EL MODAL
    // ===========================================================
    const modal = document.getElementById("modalEditarPerfil");
    modal.addEventListener("shown.bs.modal", cargarPaises);
});
