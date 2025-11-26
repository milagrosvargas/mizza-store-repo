document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formSeguridad");

    const actual = form.actual;
    const nueva = form.nueva;
    const confirmar = form.confirmar;

    // ==========================================================
    // FUNCIONES DE VALIDACIÓN
    // ==========================================================
    const mostrarError = (input, mensaje) => {
        input.classList.add("is-invalid");
        input.nextElementSibling.textContent = mensaje;
    };

    const limpiarError = (input) => {
        input.classList.remove("is-invalid");
        input.nextElementSibling.textContent = "";
    };

    const validarCampos = async () => {
        let valido = true;

        // Contraseña actual
        if (actual.value.trim() === "") {
            mostrarError(actual, "Ingrese su contraseña actual.");
            valido = false;
        } else limpiarError(actual);

        // Nueva contraseña
        if (nueva.value.trim().length < 6) {
            mostrarError(nueva, "La nueva contraseña debe tener al menos 6 caracteres.");
            valido = false;
        } else if (nueva.value.trim() === actual.value.trim()) {
            mostrarError(nueva, "La nueva contraseña no puede ser igual a la actual.");
            valido = false;
        } else limpiarError(nueva);

        // Confirmar contraseña
        if (confirmar.value.trim() === "") {
            mostrarError(confirmar, "Confirme su nueva contraseña.");
            valido = false;
        } else if (confirmar.value !== nueva.value) {
            mostrarError(confirmar, "Las contraseñas no coinciden.");
            valido = false;
        } else limpiarError(confirmar);

        return valido;
    };

    // ==========================================================
    // MOSTRAR / OCULTAR CONTRASEÑA
    // ==========================================================
    const agregarTogglePassword = (input) => {
        const wrapper = document.createElement("div");
        wrapper.classList.add("position-relative");

        const parent = input.parentNode;
        parent.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const toggleBtn = document.createElement("button");
        toggleBtn.type = "button";
        toggleBtn.className = "btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2 border-0";
        toggleBtn.innerHTML = `<i class="bi bi-eye-slash"></i>`;
        wrapper.appendChild(toggleBtn);

        toggleBtn.addEventListener("click", () => {
            const tipo = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", tipo);
            toggleBtn.innerHTML = tipo === "password"
                ? `<i class="bi bi-eye-slash"></i>`
                : `<i class="bi bi-eye"></i>`;
        });
    };

    [actual, nueva, confirmar].forEach(input => agregarTogglePassword(input));

    // ==========================================================
    // ENVÍO DEL FORMULARIO
    // ==========================================================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const valido = await validarCampos();
        if (!valido) return;

        // Confirmación del cambio de contraseña
        const confirmacion = await Swal.fire({
            title: "¿Desea cambiar su contraseña?",
            text: "Asegúrese de recordar su nueva contraseña.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#e06388",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, cambiar",
            cancelButtonText: "Cancelar"
        });

        if (!confirmacion.isConfirmed) return;

        const formData = new FormData(form);

        try {
            const res = await fetch("index.php?controller=MiPerfil&action=actualizarContrasena", {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                // 🔥 Disparar evento de auditoría
                const evento = new CustomEvent("passwordUpdated", {
                    detail: {
                        mensaje: data.mensaje,
                        fecha: new Date().toLocaleString()
                    }
                });
                document.dispatchEvent(evento);

                Swal.fire({
                    icon: "success",
                    title: "Contraseña actualizada",
                    text: data.mensaje,
                    confirmButtonColor: "#e06388"
                }).then(() => form.reset());
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.mensaje,
                    confirmButtonColor: "#e06388"
                });
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Error interno",
                text: "Ocurrió un problema al actualizar la contraseña.",
                confirmButtonColor: "#e06388"
            });
        }
    });
});
