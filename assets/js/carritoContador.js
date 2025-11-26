document.addEventListener('DOMContentLoaded', () => {

    const carritoLink = document.querySelector('.carrito-link');
    if (!carritoLink) return;

    // Crear contador visual si no existe
    let contador = carritoLink.querySelector('.carrito-contador');
    if (!contador) {
        contador = document.createElement('span');
        contador.classList.add('carrito-contador');
        carritoLink.appendChild(contador);
    }

    // 🌐 Obtener cantidad real desde SESSION (backend)
    async function obtenerCantidadDesdeSession() {
        try {
            const resp = await fetch('index.php?controller=Carrito&action=contar');
            const res = await resp.json();

            if (res.success) {
                const total = Number(res.cantidad) || 0;

                contador.textContent = total;
                contador.style.display = total > 0 ? 'flex' : 'none';
            }
        } catch (err) {
            console.error('Error obteniendo cantidad del carrito:', err);
        }
    }

    // 👉 Hacer global si algún componente quiere forzar actualización
    window.actualizarContadorNavbar = obtenerCantidadDesdeSession;

    // 📡 Escuchar evento global emitido cuando cambia el carrito
    document.addEventListener('carrito:actualizado', obtenerCantidadDesdeSession);

    // 🚀 Inicializar contador al cargar
    obtenerCantidadDesdeSession();
});
