import { mostrarExito, mostrarError, mostrarConfirmacion } from "./alertas.js";
import { inicializarUbicaciones } from "./ubicaciones.js";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formCliente");

    const campos = {
        nombre: document.getElementById("nombre"),
        apellido: document.getElementById("apellido"),
        genero: document.getElementById("genero"),
        tipo_documento: document.getElementById("tipo_documento"),
        numero_documento: document.getElementById("numero_documento"),
        email: document.getElementById("email"),
        telefono: document.getElementById("telefono"),
        pais: document.getElementById("pais"),
        provincia: document.getElementById("provincia"),
        ciudad: document.getElementById("ciudad"),
        barrio: document.getElementById("barrio"),
        direccion: document.getElementById("direccion"),
        numero: document.getElementById("numero"),
        password: document.getElementById("password"),
        password2: document.getElementById("password2"),
        usuario: document.getElementById("usuario"),
        fecha_nacimiento: document.getElementById("fecha_nacimiento")
    };

    inicializarUbicaciones(
        campos.pais,
        campos.provincia,
        campos.ciudad,
        campos.barrio
    );

    // ===============================
    // 🌐 Expresiones regulares
    // ===============================
    const regexNombre = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]{2,50}$/;
    const regexTelefono = /^\+?\d{8,15}$/;
    const regexEmail = /^[\w.%+-]+@(?:gmail\.com|outlook\.com|yahoo\.com|hotmail\.com|live\.com|icloud\.com|protonmail\.com|mail\.com|gmx\.com|aol\.com)$/i;

    // ===============================
    // ⚙️ Funciones auxiliares
    // ===============================
    function limpiarErrores() {
        document.querySelectorAll(".campo.error").forEach(c => c.classList.remove("error"));
        document.querySelectorAll(".mensaje-error").forEach(m => m.remove());
    }

    function mostrarErrorCampo(input, mensaje) {
        const campo = input.closest(".campo");
        campo.classList.add("error");

        const previo = campo.querySelector(".mensaje-error");
        if (previo) previo.remove();

        const span = document.createElement("span");
        span.classList.add("mensaje-error");
        span.textContent = mensaje;
        campo.appendChild(span);
    }

    function validarDocumento(tipoNombre, numero) {
        const t = (tipoNombre || "").toUpperCase();
        const num = (numero || "").replace(/\D/g, "");

        switch (t) {
            case "DNI": return /^\d{8}$/.test(num);
            case "CUIL":
            case "CUIT":
            case "CDI": return /^\d{11}$/.test(num);
            case "LC": return /^\d{7,8}$/.test(num);
            case "DNIE": return /^[A-Za-z0-9]{8,9}$/.test(num);
            default: return /^[A-Za-z0-9]{6,20}$/.test(num);
        }
    }

    function validarPassword(pass) {
        // Acepta cualquier combinación de letras, números o símbolos
        // Solo exige un mínimo de 6 caracteres (sin máximo estricto)
        const regla = /^[A-Za-z\d!@#$%^&*()_\-+=?.]{6,}$/;
        return regla.test(pass.trim());
    }

    function esMayorEdad(fechaStr) {
        const hoy = new Date();
        const fecha = new Date(fechaStr);
        const edad = hoy.getFullYear() - fecha.getFullYear();
        const m = hoy.getMonth() - fecha.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < fecha.getDate())) {
            return edad - 1 >= 18;
        }
        return edad >= 18;
    }

    async function existeEmail(email) {
        const res = await fetch(`index.php?controller=Cliente&action=validarEmail&email=${encodeURIComponent(email)}`);
        const data = await res.json();
        return !!data.exists;
    }

    async function existeTelefono(telefono) {
        const res = await fetch(`index.php?controller=Cliente&action=validarTelefono&telefono=${encodeURIComponent(telefono)}`);
        const data = await res.json();
        return !!data.exists;
    }

    async function existeUsuario(usuario) {
        const res = await fetch(`index.php?controller=Cliente&action=validarUsuario&usuario=${encodeURIComponent(usuario)}`);
        const data = await res.json();
        return !!data.exists;
    }

    function baseUsuario(nombre, apellido) {
        const n = nombre.trim().toLowerCase();
        const a = apellido.trim().toLowerCase();
        const base = (n[0] + a)
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]/g, "");
        return base;
    }

    async function generarUsuarioUnico(nombre, apellido) {
        const base = baseUsuario(nombre, apellido);
        if (!base) return "";

        if (!(await existeUsuario(base))) return base;

        for (let i = 0; i < 20; i++) {
            const suf = Math.floor(100 + Math.random() * 900);
            const cand = `${base}${suf}`;
            if (!(await existeUsuario(cand))) return cand;
        }
        return `${base}${Date.now().toString().slice(-5)}`;
    }

    // ===============================
    // 🧾 Validación visual por campo
    // ===============================
    function validarFormulario() {
        limpiarErrores();
        let valido = true;

        if (!regexNombre.test(campos.nombre.value.trim())) {
            mostrarErrorCampo(campos.nombre, "Ingrese un nombre válido.");
            valido = false;
        }

        if (!regexNombre.test(campos.apellido.value.trim())) {
            mostrarErrorCampo(campos.apellido, "Ingrese un apellido válido.");
            valido = false;
        }

        if (!campos.genero.value) {
            mostrarErrorCampo(campos.genero, "Seleccione un género.");
            valido = false;
        }

        const optTipo = campos.tipo_documento.options[campos.tipo_documento.selectedIndex];
        const nombreTipo = optTipo ? (optTipo.dataset.codigo || optTipo.textContent).trim() : "";
        if (!campos.tipo_documento.value || !validarDocumento(nombreTipo, campos.numero_documento.value)) {
            mostrarErrorCampo(campos.numero_documento, `Documento inválido (${nombreTipo || "desconocido"}).`);
            valido = false;
        }

        if (!regexEmail.test(campos.email.value.trim())) {
            mostrarErrorCampo(campos.email, "Correo electrónico inválido o dominio no permitido.");
            valido = false;
        }

        if (!regexTelefono.test(campos.telefono.value.trim())) {
            mostrarErrorCampo(campos.telefono, "Teléfono inválido (mín. 8 dígitos).");
            valido = false;
        }

        if (!campos.fecha_nacimiento.value || !esMayorEdad(campos.fecha_nacimiento.value)) {
            mostrarErrorCampo(campos.fecha_nacimiento, "Debe ser mayor de 18 años.");
            valido = false;
        }

        if ((campos.direccion.value || "").trim().length < 3) {
            mostrarErrorCampo(campos.direccion, "Ingrese una dirección válida.");
            valido = false;
        }

        if (!validarPassword(campos.password.value)) {
            mostrarErrorCampo(campos.password, "Contraseña no cumple requisitos.");
            valido = false;
        }

        if (campos.password.value !== campos.password2.value) {
            mostrarErrorCampo(campos.password2, "Las contraseñas no coinciden.");
            valido = false;
        }

        ["pais", "provincia", "ciudad", "barrio"].forEach(id => {
            if (!campos[id].value) {
                mostrarErrorCampo(campos[id], "Campo obligatorio.");
                valido = false;
            }
        });

        return valido;
    }

    // ====================================
    // 📤 Envío del formulario
    // ====================================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!validarFormulario()) return;

        try {
            const [emailDup, telDup] = await Promise.all([
                existeEmail(campos.email.value.trim()),
                existeTelefono(campos.telefono.value.trim())
            ]);

            if (emailDup) {
                mostrarErrorCampo(campos.email, "El correo ya está registrado.");
                return;
            }

            if (telDup) {
                mostrarErrorCampo(campos.telefono, "El teléfono ya está registrado.");
                return;
            }
        } catch {
            mostrarError("Error de conexión", "No se pudo validar email/teléfono.");
            return;
        }

        const usuario = await generarUsuarioUnico(campos.nombre.value, campos.apellido.value);
        campos.usuario.value = usuario;

        const confirm = await mostrarConfirmacion(
            "¿Registrar cliente?",
            "Se enviará un correo para activar la cuenta."
        );
        if (!confirm.isConfirmed) return;

        const datos = new URLSearchParams();
        Object.entries(campos).forEach(([key, el]) => datos.append(key, el.value.trim()));

        fetch("index.php?controller=Cliente&action=guardar", {
            method: "POST",
            body: datos
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarExito("Registro exitoso", "Revisa tu correo para activar la cuenta.");
                    form.reset();
                    limpiarErrores();
                } else {
                    mostrarError("Error", data.message || "No se pudo registrar el cliente.");
                }
            })
            .catch(() => mostrarError("Error de conexión", "No se pudo contactar con el servidor."));
    });

    document.querySelectorAll(".campo input, .campo select").forEach(el => {
        el.addEventListener("focus", () => el.parentElement.classList.add("activo"));
        el.addEventListener("blur", () => {
            if (!el.value.trim()) el.parentElement.classList.remove("activo");
        });
    });
});
