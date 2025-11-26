import { mostrarExito, mostrarError } from './alertas.js';

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');

  form.addEventListener('submit', async e => {
    e.preventDefault();

    const usuario = document.getElementById('usuario').value.trim();
    const contrasena = document.getElementById('contrasena').value.trim();

    // 🔹 Validar campos vacíos
    if (!usuario || !contrasena) {
      mostrarError('Campos incompletos', 'Por favor completá todos los campos.');
      return;
    }

    try {
      const res = await fetch('/MizzaStore/index.php?controller=Login&action=loginApi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario, contrasena })
      });

      if (!res.ok) {
        throw new Error(`Error HTTP: ${res.status}`);
      }

      const data = await res.json();
      console.log('Respuesta del servidor:', data);

      if (data.success) {
        // 🔹 Mostrar mensaje de éxito y redirigir según corresponda
        mostrarExito('¡Bienvenido!', data.message).then(() => {
          const destino = data.redirect || '/MizzaStore/index.php?controller=Panel&action=dashboard';
          window.location.href = destino;
        });
      } else {
        // 🔹 Mostrar mensaje de error personalizado según backend
        let titulo = 'Error de autenticación';
        let mensaje = data.message || 'Usuario o contraseña incorrectos.';

        if (mensaje.includes('inactivo')) titulo = 'Cuenta inactiva';
        else if (mensaje.includes('activada')) titulo = 'Cuenta no activada';
        else if (mensaje.includes('sesión activa')) titulo = 'Sesión existente';

        // ⚠️ Si el backend indica que puede reenviar el correo de activación
        if (data.reenviar_activacion && data.id_usuario) {
          mostrarError(
            titulo,
            `${mensaje}<br><br>
            <button id="btnReenviarActivacion" 
                    class="swal2-confirm swal2-styled" 
                    style="background-color:#3085d6;">
              Reenviar correo de activación
            </button>`
          );

          // 🔹 Esperar a que SweetAlert inserte el botón y luego agregar listener
          setTimeout(() => {
            const btnReenviar = document.getElementById('btnReenviarActivacion');
            if (btnReenviar) {
              btnReenviar.addEventListener('click', async () => {
                try {
                  const resp = await fetch('/MizzaStore/index.php?controller=Activar&action=reenviarToken', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_usuario: data.id_usuario })
                  });

                  const resultado = await resp.json();
                  if (resultado.success) {
                    mostrarExito('Correo reenviado', resultado.message);
                  } else {
                    mostrarError('Error', resultado.message || 'No se pudo reenviar el correo.');
                  }
                } catch (err) {
                  console.error('Error al reenviar el correo:', err);
                  mostrarError('Error de conexión', 'No se pudo contactar con el servidor.');
                }
              });
            }
          }, 300); // Espera leve para asegurar render SweetAlert
        } else {
          mostrarError(titulo, mensaje);
        }
      }
    } catch (err) {
      console.error('Error al conectar o procesar la solicitud:', err);
      mostrarError('Error del servidor', 'No se pudo conectar con el servidor. Intentalo más tarde.');
    }
  });
});
