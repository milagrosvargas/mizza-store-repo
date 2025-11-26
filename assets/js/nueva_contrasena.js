import { mostrarExito, mostrarError } from './alertas.js';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formNuevaContrasena');
  const password1 = document.getElementById('password');
  const password2 = document.getElementById('password2');
  const tokenInput = document.getElementById('token');
  const btnSubmit = form.querySelector('button[type="submit"]');

  // 🧹 Limpia mensajes y estilos previos
  const limpiarErrores = () => {
    form.querySelectorAll('.error-msg').forEach(msg => msg.remove());
    [password1, password2].forEach(input => input.classList.remove('error'));
  };

  // ❌ Muestra error visual y mensaje
  const mostrarErrorCampo = (input, mensaje) => {
    const msg = document.createElement('small');
    msg.classList.add('error-msg');
    msg.style.color = 'red';
    msg.style.display = 'block';
    msg.style.marginTop = '4px';
    msg.textContent = mensaje;
    input.classList.add('error');
    input.parentElement.appendChild(msg);
  };

  // 🔍 Valida contraseñas
  const validarCampos = () => {
    limpiarErrores();

    const pass1 = password1.value.trim();
    const pass2 = password2.value.trim();
    let valido = true;

    // Campo vacío
    if (pass1 === '') {
      mostrarErrorCampo(password1, 'Debes ingresar una contraseña.');
      valido = false;
    }

    if (pass2 === '') {
      mostrarErrorCampo(password2, 'Debes confirmar la contraseña.');
      valido = false;
    }

    // Solo validar longitud si no está vacío
    if (pass1 !== '' && pass1.length < 6) {
      mostrarErrorCampo(password1, 'La contraseña debe tener al menos 6 caracteres.');
      valido = false;
    }

    // Validar coincidencia si ambos están cargados
    if (pass1 !== '' && pass2 !== '' && pass1 !== pass2) {
      mostrarErrorCampo(password2, 'Las contraseñas no coinciden.');
      valido = false;
    }

    return valido;
  };

  // 📤 Envío
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validarCampos()) return;

    const token = tokenInput.value.trim();
    const pass1 = password1.value.trim();
    const pass2 = password2.value.trim();

    // Bloquear botón mientras envía
    const textoOriginal = btnSubmit.textContent;
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Actualizando...';

    try {
      const res = await fetch('/MizzaStore/index.php?controller=Login&action=actualizarContrasena', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token, password: pass1, password2: pass2 })
      });

      const data = await res.json();

      if (data.success) {
        mostrarExito('Contraseña actualizada', data.message);
        form.reset();

        setTimeout(() => {
          window.location.href = '/MizzaStore/index.php?controller=Login&action=login';
        }, 2000);
      } else {
        mostrarError('Error', data.message || 'No se pudo actualizar la contraseña.');
      }
    } catch (err) {
      console.error('Error de conexión:', err);
      mostrarError('Error de conexión', 'No se pudo contactar con el servidor.');
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.textContent = textoOriginal;
    }
  });
});
