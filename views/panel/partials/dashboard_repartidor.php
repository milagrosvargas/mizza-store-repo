<style>
/* 🎨 Contenedor general */
.repartidor-container {
    padding: 30px 10px;
    max-width: 900px;
    margin: auto;
}

/* 🗂 Tarjeta general */
.card-repartidor {
    border: none;
    border-radius: 18px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    overflow: hidden;
    background-color: #ffffff;
}

/* 🧭 Encabezado */
.card-repartidor-header {
    background: linear-gradient(90deg, #003366, #0059b3);
    color: #ffffff;
    font-weight: 600;
    font-size: 1.2rem;
    padding: 16px 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* 📦 Tarjeta de pedido */
.pedido-item {
    background-color: #ffffff;
    margin-bottom: 14px;
    border-radius: 14px;
    border: 1px solid #e6e9ee;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    padding: 16px 20px;
}

.pedido-item:hover {
    transform: scale(1.01);
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
}

/* 🏷 Estado de envío */
.badge-estado-envio {
    font-size: 0.82rem;
    padding: 6px 14px;
    border-radius: 999px;
    font-weight: 600;
    display: inline-block;
}

.badge-envio-pendiente {
    background-color: #f0ad4e;
    color: #fff;
}

.badge-envio-en-camino {
    background-color: #0275d8;
    color: #fff;
}

.badge-envio-entregado {
    background-color: #28a745;
    color: #fff;
}

/* 📍 Dirección */
.texto-direccion {
    font-size: 0.95rem;
    padding: 6px 0;
    color: #444;
}

/* 🗺 Botones */
.btn-mapa, .btn-accion-envio {
    font-size: 0.85rem;
    border-radius: 8px;
    padding: 6px 12px;
}

.btn-mapa {
    border: 1px solid #aaa;
    color: #444;
}

.btn-accion-envio {
    font-weight: 500;
}

/* 🍞 Toast */
.toast-custom {
    background: #ffdd57;
    color: #333;
    font-weight: 600;
    border-radius: 12px;
    padding: 14px 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    border-left: 6px solid #ffaa00;
}

/* ℹ Mensaje vacío */
.mensaje-vacio {
    font-size: 1rem;
    padding: 30px 0;
    color: #888;
}

</style>
<div class="container repartidor-container">
    <div class="card-repartidor">
        <div class="card-repartidor-header text-center">
            Resumen de mis pedidos asignados
        </div>
        <div class="card-body">
            <div id="listaPedidos"></div>

            <div id="mensajeSinPedidos" class="text-center text-muted py-4" style="display:none;">
                No tienes pedidos asignados por el momento.
            </div>
        </div>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function () {
    cargarPedidosAsignados();
});

function cargarPedidosAsignados() {
    fetch('index.php?controller=Repartidor&action=listarPedidosAsignados')
        .then(r => r.json())
        .then(res => {
            const lista = document.getElementById('listaPedidos');
            const mensajeSin = document.getElementById('mensajeSinPedidos');
            lista.innerHTML = '';

            if (!res.success || !res.data || res.data.length === 0) {
                mensajeSin.style.display = 'block';
                // Si no hay pedidos, limpiar contador de almacenamiento
                localStorage.setItem('totalPedidosAsignados', 0);
                return;
            }
            mensajeSin.style.display = 'none';

            // ================================
            // 🔔 DETECTAR NUEVOS PEDIDOS
            // ================================
            const pedidosActuales = res.data.length;
            const pedidosPrevios = parseInt(localStorage.getItem('totalPedidosAsignados')) || 0;

            if (pedidosPrevios !== 0 && pedidosActuales > pedidosPrevios) {
                mostrarToast('📦 Nuevo pedido asignado');
            }

            // Actualizar valor almacenado siempre
            localStorage.setItem('totalPedidosAsignados', pedidosActuales);

            // ================================
            // 📝 IMPRIMIR PEDIDOS
            // ================================
            res.data.forEach(pedido => {
                const estado = (pedido.estado_envio || '').toLowerCase();
                let claseEstado = 'badge-envio-pendiente';
                let textoEstado = 'Pendiente';

                if (estado === 'en camino') {
                    claseEstado = 'badge-envio-en-camino';
                    textoEstado = 'En camino';
                } else if (estado === 'entregado') {
                    claseEstado = 'badge-envio-entregado';
                    textoEstado = 'Entregado';
                }

                const direccion = pedido.direccion_completa || 'Sin dirección';
                const urlMapa =
                    'https://www.google.com/maps/search/?api=1&query=' +
                    encodeURIComponent(direccion);

                const html = `
                    <div class="pedido-item row align-items-center">
                        <div class="col-md-8">
                            <div class="fw-bold">Pedido #${pedido.id_pedido}</div>
                            <div class="small text-muted">Fecha de pedido ${pedido.fecha_pedido ? formatearFecha(pedido.fecha_pedido) : '-'}</div>
                            <div class="small text-muted">Monto total a pagar $${Number(pedido.monto_total || 0).toFixed(2)}</div>
                            <div class="texto-direccion mt-2">Ubicación <strong>${direccion}</strong></div>
                            <span class="badge-estado-envio ${claseEstado} mt-2">${textoEstado}</span>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <a href="${urlMapa}" target="_blank" class="btn btn-sm btn-outline-secondary btn-mapa">
                                🗺 Ver en Google Maps
                            </a>
                        </div>
                    </div>
                `;

                lista.insertAdjacentHTML('beforeend', html);
            });
        })
        .catch(() => {
            mostrarToast('Ocurrió un error al comunicarse con el servidor.');
        });
}

function formatearFecha(fechaIso) {
    const partes = fechaIso.split(' ');
    if (partes.length === 0) return fechaIso;
    const fecha = partes[0].split('-');
    if (fecha.length !== 3) return fechaIso;
    return `${fecha[2]}/${fecha[1]}/${fecha[0]}${partes[1] ? ' ' + partes[1].substring(0,5) : ''}`;
}

function mostrarToast(mensaje) {
    const toastHTML = `
        <div class="toast toast-custom show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${mensaje}</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

    const container = document.createElement('div');
    container.className = "toast-container position-fixed bottom-0 end-0 p-3";
    container.innerHTML = toastHTML;
    document.body.appendChild(container);

    setTimeout(() => {
        const toastEl = container.querySelector('.toast');
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }, 50);
}

</script>
