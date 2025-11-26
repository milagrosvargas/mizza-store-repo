<style>
    :root {
        --bordo: #7a1c4b;
        --rosa-medio: #d94b8c;
        --rosa-claro: #f9e2ec;
        --texto-oscuro: #2b1a1f;
        --blanco: #ffffff;
    }

    /* Contenedor principal */
    .contenedor-envios {
        width: 90%;
        max-width: 1250px;
        margin: 10px auto;
        background: var(--blanco);
        padding: 30px 25px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        font-family: 'Segoe UI', sans-serif;
        color: var(--texto-oscuro);
    }

    .contenedor-envios h2 {
        font-weight: 600;
        color: var(--bordo);
        margin-bottom: 6px;
    }

    .subtitulo-envios {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 20px;
    }

    /* Tabla */
    .tabla-contenedor-envios {
        width: 100%;
        overflow-x: auto;
        max-height: 70vh;
        border: 1px solid var(--rosa-medio);
        border-radius: 10px;
    }

    #tablaEnvios {
        width: 100%;
        border-collapse: collapse;
        background: var(--blanco);
    }

    #tablaEnvios th,
    #tablaEnvios td {
        padding: 10px 8px;
        border-bottom: 1px solid var(--rosa-claro);
        font-size: 14px;
    }

    #tablaEnvios thead {
        background-color: var(--bordo);
        color: var(--blanco);
    }

    #tablaEnvios tbody tr:hover {
        background-color: var(--rosa-claro);
    }

    /* Badges */
    .badge-envio {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-pendiente {
        background: #ffc107;
    }

    .badge-procesando {
        background: #0dcaf0;
        color: #fff;
    }

    .badge-enviado {
        background: #0d6efd;
        color: #fff;
    }

    .badge-entregado {
        background: #28a745;
        color: #fff;
    }

    .badge-cancelado {
        background: #dc3545;
        color: #fff;
    }

    /* Botones */
    .btn-accion-envio {
        border: none;
        background: linear-gradient(135deg, var(--rosa-medio), var(--bordo));
        color: var(--blanco);
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.25s ease-in-out;
    }

    .btn-accion-envio:hover {
        transform: scale(1.05);
    }

    /* Modal unificado (versión corregida) */
    .modal {
        display: none;
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
        justify-content: center;
        align-items: center;
        z-index: 1050;
    }

    .modal-content {
        background: var(--blanco);
        padding: 25px;
        border-radius: 10px;
        width: 400px;
        border-top: 6px solid var(--bordo);
        animation: scaleIn 0.3s ease-in-out;
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .acciones-modal {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-secundario {
        padding: 7px 12px;
        background: var(--rosa-claro);
        border: 1px solid var(--rosa-medio);
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-secundario:hover {
        background: var(--rosa-medio);
        color: var(--blanco);
    }
</style>


<div class="contenedor-envios">
    <h2>Gestión de Pedidos con Envío</h2>
    <p class="subtitulo-envios">Asignación de repartidores y estado de los pedidos.</p>

    <div id="alertContainer"></div>

    <div class="tabla-contenedor-envios">
        <table id="tablaEnvios">
            <thead>
                <tr>
                    <th>Pedido #</th>
                    <th>Cliente</th>
                    <th>Estado del pedido</th>
                    <th>Repartidor</th>
                    <th>Estado del envío</th>
                    <th>Fecha de envío</th>
                    <th>Fecha de entrega</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- Generado por AJAX -->
            </tbody>
        </table>
    </div>
</div>


<!-- Modal Asignar Repartidor -->
<div id="modalAsignar" class="modal">
    <div class="modal-content">
        <h3>Asignar repartidor</h3>
        <label>Seleccionar repartidor:</label>
        <select id="selectRepartidor" class="form-select"></select>
        <div class="acciones-modal mt-3">
            <button class="btn-secundario" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-accion-envio" onclick="confirmarAsignacion()">Asignar y Enviar</button>
        </div>
    </div>
</div>


<!-- Toast de notificaciones -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastNotificacion" class="toast align-items-center text-bg-primary border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMensaje"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>


<script>
    let pedidoSeleccionado = null;

    function cargarPedidos() {
        fetch('index.php?controller=Envio&action=listarPedidosEnvio')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#tablaEnvios tbody');
                tbody.innerHTML = '';

                data.data.forEach(pedido => {
                    tbody.innerHTML += `
                    <tr>
                        <td><strong>${pedido.id_pedido}</strong></td>
                        <td>${pedido.nombre_cliente} ${pedido.apellido_cliente}</td>
                        <td><span class="badge-envio badge-${pedido.estado_pedido.toLowerCase()}">${pedido.estado_pedido}</span></td>
                        <td>${pedido.nombre_repartidor ? pedido.nombre_repartidor + ' ' + pedido.apellido_repartidor : '-'}</td>
                        <td>${pedido.estado_envio ? `<span class="badge-envio badge-${pedido.estado_envio.toLowerCase()}">${pedido.estado_envio}</span>` : '<span class="badge-envio badge-pendiente">Sin envío</span>'}</td>
                        <td>${pedido.fecha_envio ?? '-'}</td>
                        <td>${pedido.fecha_entrega ?? '-'}</td>
                        <td>${generarBotonesAccion(pedido)}</td>
                    </tr>`;
                });
            });
    }

    function generarBotonesAccion(pedido) {
        // Si el pedido está en procesamiento y no tiene repartidor asignado
        if (pedido.estado_pedido === 'Procesando' && !pedido.nombre_repartidor) {
            return `<button class="btn-accion-envio" onclick="abrirModalAsignar(${pedido.id_pedido})">
                    Asignar pedido
                </button>`;
        }
        return `<span class="text-muted">Sin acciones</span>`;
    }


    function abrirModalAsignar(idPedido) {
        pedidoSeleccionado = idPedido;

        fetch('index.php?controller=Envio&action=listarRepartidores')
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('selectRepartidor');
                select.innerHTML = data.data.map(rep =>
                    `<option value="${rep.id_repartidor}">${rep.nombre_persona} ${rep.apellido_persona}</option>`
                ).join('');
                document.getElementById('modalAsignar').style.display = 'flex';
            });
    }

    function cerrarModal() {
        document.getElementById('modalAsignar').style.display = 'none';
    }

    function confirmarAsignacion() {
        const fd = new FormData();
        fd.append('id_pedido', pedidoSeleccionado);
        fd.append('id_repartidor', document.getElementById('selectRepartidor').value);

        fetch('index.php?controller=Envio&action=asignarEnvio', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                mostrarToast(data.message, data.success ? 'success' : 'danger');
                cerrarModal();
                cargarPedidos();
            });
    }

    function mostrarToast(mensaje, tipo = 'primary') {
        const toastEl = document.getElementById('toastNotificacion');
        const msgEl = document.getElementById('toastMensaje');
        toastEl.className = `toast align-items-center text-bg-${tipo} border-0`;
        msgEl.innerText = mensaje;

        new bootstrap.Toast(toastEl).show();
    }

    window.onclick = function(e) {
        const modal = document.getElementById('modalAsignar');
        if (e.target === modal) {
            cerrarModal();
        }
    };

    document.addEventListener('DOMContentLoaded', cargarPedidos);
</script>