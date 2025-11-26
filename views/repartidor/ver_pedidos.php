<style>
    :root {
        --rosa-glam: #B6465F;
        --rosa-claro: #fce4ec;
        --rosa-bg: #fff8f9;
        --gris-texto: #555;
        --gris-borde: #e0e0e0;
        --verde-ok: #2e7d32;
        --amarillo: #ffb300;
        --azul: #0d6efd;
    }

    h3 {
        color: var(--rosa-glam);
        font-weight: 600;
        margin-bottom: 20px;
    }

    .tabla-pedidos {
        background: #ffffff;
        border-radius: 14px;
        padding: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.04);
    }

    td, th {
        vertical-align: middle;
    }

    .badge-envio {
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 30px;
        text-transform: capitalize;
        font-weight: 500;
    }
    .bg-pendiente { background-color: var(--amarillo); color: #fff; }
    .bg-en-camino { background-color: var(--azul); color: #fff; }
    .bg-entregado { background-color: var(--verde-ok); color: #fff; }

.btn-mapa {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 110px;          /* Más ancho */
    height: 36px;          /* Altura uniforme */
    background: var(--rosa-glam);
    border: none;
    color: white;
    font-size: 0.8rem;
    border-radius: 18px;
    transition: all 0.3s ease;
    text-align: center;
    white-space: nowrap;
}

.btn-mapa:hover {
    background: #8c3448;
    color: white;
}


    /* === SLIDER MINIMALISTA CENTRADO === */
/* Ajuste de la columna Accion para no apilar sliders */
td:last-child {
    min-width: 220px;      /* Obliga a tener espacio suficiente */
    text-align: center;
}

/* Slider minimalista más equilibrado */
.slider-confirm-container {
    width: 230px;
    height: 36px;          /* Más estilizado y menos alto */
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.slider-track {
    width: 100%;
    height: 100%;
    background: var(--rosa-claro);
    border-radius: 30px;    /* Más elegante */
    position: relative;
    border: 1px solid var(--rosa-glam);
    overflow: hidden;
}

/* Texto centrado y equilibrado */
.slider-text {
    position: absolute;
    width: 100%;
    text-align: center;
    line-height: 34px;      /* Ajustado al nuevo alto */
    font-size: 0.8rem;
    color: var(--rosa-glam);
    font-weight: 500;
}

/* Botón circular más pequeño y proporcionado */
.slider-thumb {
    width: 36px;
    height: 36px;
    background: var(--rosa-glam);
    border-radius: 50%;
    position: absolute;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    cursor: grab;
    box-shadow: 0 2px 6px rgba(0,0,0,0.18);
    transition: left 0.2s ease;
}


    .toast-custom {
        background-color: var(--rosa-glam);
        color: white;
        font-weight: 500;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        border-left: 5px solid #880E4F;
    }
</style>

<div class="container mt-4">
    <h3 class="mb-4">Transportista</h3>

    <div class="tabla-pedidos">
        <table class="table table-hover align-middle text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Estado de envío</th>
                    <th>Dirección de entrega</th>
                    <th>Mapa</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['pedidos'])): ?>
                    <tr>
                        <td colspan="7" class="text-muted py-4">No hay pedidos asignados por el momento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['pedidos'] as $pedido): ?>
                        <?php
                            $estado = strtolower($pedido['estado_envio']);
                            $clase = $estado === 'en camino' ? 'bg-en-camino' :
                                     ($estado === 'entregado' ? 'bg-entregado' : 'bg-pendiente');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($pedido['id_pedido']) ?></td>
                            <td><?= htmlspecialchars($pedido['fecha_pedido']) ?></td>
                            <td>$<?= number_format($pedido['monto_total'], 2) ?></td>
                            <td><span class="badge-envio <?= $clase ?>"><?= ucfirst($estado) ?></span></td>
                            <td><?= htmlspecialchars($pedido['direccion_completa']) ?></td>
                            <td>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($pedido['direccion_completa']) ?>"
                                   class="btn-mapa" target="_blank">Ver mapa</a>
                            </td>
                            <td>
                                <?php if ($estado !== 'entregado'): ?>
                                    <div class="slider-confirm-container">
                                        <div class="slider-track" id="slider-track-<?= $pedido['id_pedido'] ?>">
                                            <span class="slider-text">Desliza para entregar</span>
                                            <div class="slider-thumb" id="slider-thumb-<?= $pedido['id_pedido'] ?>">➤</div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-success fw-bold">Completado ✔</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inicializar sliders automáticamente
    document.querySelectorAll('.slider-thumb').forEach(thumb => {
        const idPedido = thumb.id.replace('slider-thumb-', '');
        initializeSlider(idPedido);
    });
});

function initializeSlider(idPedido) {
    const track = document.getElementById('slider-track-' + idPedido);
    const thumb = document.getElementById('slider-thumb-' + idPedido);
    if (!track || !thumb) return;

    const trackWidth = track.offsetWidth - thumb.offsetWidth;
    let isDragging = false, startX = 0;

    thumb.addEventListener('mousedown', e => {
        isDragging = true;
        startX = e.clientX;
        document.body.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', e => {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        thumb.style.left = Math.min(Math.max(0, dx), trackWidth) + 'px';
    });

    document.addEventListener('mouseup', () => {
        if (!isDragging) return;
        isDragging = false;
        document.body.style.cursor = 'default';
        if (parseInt(thumb.style.left, 10) >= trackWidth * 0.9) {
            thumb.style.left = trackWidth + 'px';
            marcarEntregado(idPedido);
        } else {
            thumb.style.left = '0';
        }
    });
}

function marcarEntregado(idPedido) {
    fetch('index.php?controller=Repartidor&action=marcarEntrega', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_pedido=' + idPedido
    })
    .then(r => r.json())
    .then(res => {
        mostrarToast(res.message);
        if (res.success) setTimeout(() => location.reload(), 1000);
    })
    .catch(() => mostrarToast('Error al comunicarse con el servidor'));
}

function mostrarToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'toast-custom position-fixed bottom-0 end-0 m-3 p-3';
    toast.style.zIndex = "9999";
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}
</script>
