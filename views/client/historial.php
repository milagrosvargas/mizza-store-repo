<style>
    /* ============================================
   CONTENEDOR GENERAL DEL HISTORIAL
   ============================================ */
.contenedor-historial {
    width: 90%;
    max-width: 1250px;
    margin: 10px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2b1a1f;
    background: #fff;
    padding: 30px 25px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.contenedor-historial h2 {
    text-align: left;
    margin-bottom: 10px;
    font-weight: 600;
    color: #7a1c4b;
    letter-spacing: 0.4px;
    font-size: 1.6rem;
}

.contenedor-historial .subtitulo {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 25px;
    line-height: 1.4;
}

/* ============================================
   FILTROS Y BUSCADOR
   ============================================ */
.filtros-historial {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.filtros-historial input,
.filtros-historial select {
    padding: 8px 10px;
    border: 1px solid #d94b8c;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s ease-in-out;
    color: #2b1a1f;
    background-color: #fff;
}

.filtros-historial input:focus,
.filtros-historial select:focus {
    border-color: #7a1c4b;
    box-shadow: 0 0 4px rgba(122, 28, 75, 0.3);
}

/* ============================================
   TABLA HISTORIAL
   ============================================ */
.tabla-historial-contenedor {
    width: 100%;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 65vh;
    white-space: nowrap;
    border: 1px solid #f0c8d8;
    border-radius: 10px;
}

.tabla-historial-contenedor::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.tabla-historial-contenedor::-webkit-scrollbar-thumb {
    background-color: #d94b8c;
    border-radius: 4px;
}

#tablaHistorial {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

#tablaHistorial thead {
    background-color: #7a1c4b;
    color: #fff;
}

#tablaHistorial th {
    padding: 12px 10px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.3px;
}

#tablaHistorial td {
    padding: 10px 8px;
    vertical-align: middle;
    font-size: 14px;
    border-bottom: 1px solid #f0c8d8;
}

#tablaHistorial tbody tr:hover {
    background-color: #f9e2ec;
}

/* ============================================
   BOTONES DE ACCIÓN EN TABLA
   ============================================ */
.btn-historial {
    border: none;
    background: transparent;
    cursor: pointer;
    margin: 0 3px;
    padding: 3px;
    transition: transform 0.15s ease, opacity 0.15s ease;
}

.btn-historial:hover {
    transform: scale(1.1);
    opacity: 0.8;
}

.icono-historial {
    width: 20px;
    height: 20px;
}

/* ============================================
   PAGINADOR
   ============================================ */
.paginador-historial {
    margin-top: 20px;
    text-align: center;
}

.paginador-historial button {
    margin: 2px;
    padding: 6px 12px;
    border: 1px solid #d94b8c;
    background: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
    color: #7a1c4b;
    font-weight: 500;
}

.paginador-historial button:hover {
    background-color: #d94b8c;
    color: #fff;
    border-color: #d94b8c;
}

.paginador-historial button.activo {
    background-color: #7a1c4b;
    color: #fff;
    border-color: #7a1c4b;
}

/* ============================================
   RESPONSIVIDAD
   ============================================ */
@media (max-width: 768px) {
    .contenedor-historial {
        padding: 20px 15px;
    }

    #tablaHistorial th,
    #tablaHistorial td {
        font-size: 13px;
        padding: 8px;
    }

    .icono-historial {
        width: 16px;
        height: 16px;
    }

    .filtros-historial {
        flex-direction: column;
        align-items: flex-start;
    }
}

</style>
<div class="contenedor-historial">
    <h2>Historial de compras</h2>
    <p class="subtitulo">Consulta tus pedidos realizados, filtra por fecha, estado, método de pago o busca un pedido específico.</p>

    <!-- FILTROS -->
    <div class="filtros-historial">
        <input type="text" id="buscarPedido" placeholder="N° Pedido...">
        <input type="date" id="fechaInicio" title="Fecha desde">
        <input type="date" id="fechaFin" title="Fecha hasta">

        <select id="estadoPedido">
            <option value="">Estado del pedido</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Procesando">Procesando</option>
            <option value="Enviado">Enviado</option>
            <option value="Entregado">Entregado</option>
            <option value="Cancelado">Cancelado</option>
        </select>

        <select id="estadoPago">
            <option value="">Estado de pago</option>
            <option value="pendiente">Pendiente</option>
            <option value="completado">Completado</option>
            <option value="fallido">Fallido</option>
        </select>

        <select id="metodoPago">
            <option value="">Método de pago</option>
        </select>

        <input type="number" id="montoMin" placeholder="Monto mínimo" min="0" style="width:120px;">
        <input type="number" id="montoMax" placeholder="Monto máximo" min="0" style="width:120px;">

        <select id="registrosPorPagina">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>

    <!-- TABLA -->
    <div class="tabla-historial-contenedor">
        <table id="tablaHistorial">
            <thead>
                <tr>
                    <th>N° Pedido</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Estado Pedido</th>
                    <th>Estado Pago</th>
                    <th>Método</th>
                    <th>Productos</th>
                    <th>Envío</th>
                    <th>Domicilio</th>
                    <th>Registrado en</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- PAGINADOR -->
    <div id="paginador" class="paginador-historial"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tablaBody = document.querySelector('#tablaHistorial tbody');
    const paginador = document.getElementById('paginador');
    const registrosPorPaginaSelect = document.getElementById('registrosPorPagina');

    const filtros = {
        buscarPedido: document.getElementById('buscarPedido'),
        fechaInicio: document.getElementById('fechaInicio'),
        fechaFin: document.getElementById('fechaFin'),
        estadoPedido: document.getElementById('estadoPedido'),
        estadoPago: document.getElementById('estadoPago'),
        metodoPago: document.getElementById('metodoPago'),
        montoMin: document.getElementById('montoMin'),
        montoMax: document.getElementById('montoMax'),
    };

    let paginaActual = 1;

    function cargarHistorial() {
        const params = new URLSearchParams();
        params.append('pagina', paginaActual);
        params.append('registrosPorPagina', registrosPorPaginaSelect.value);

        Object.keys(filtros).forEach(key => {
            if (filtros[key].value.trim() !== '') {
                params.append(key, filtros[key].value.trim());
            }
        });

        fetch('index.php?controller=Cliente&action=obtenerHistorial&' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    tablaBody.innerHTML = `<tr><td colspan="11">${data.message || 'Error cargando datos'}</td></tr>`;
                    return;
                }
                renderTabla(data.data);
                renderPaginador(data.totalPaginas);
            })
            .catch(() => {
                tablaBody.innerHTML = `<tr><td colspan="11">Error de conexión</td></tr>`;
            });
    }

    function renderTabla(registros) {
        tablaBody.innerHTML = '';

        if (!registros.length) {
            tablaBody.innerHTML = `<tr><td colspan="11">No hay resultados</td></tr>`;
            return;
        }

        registros.forEach(reg => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${reg.id_pedido}</td>
                <td>${formatearFecha(reg.fecha_pedido)}</td>
                <td>$ ${parseFloat(reg.monto_total).toFixed(2)}</td>
                <td>${reg.estado_pedido}</td>
                <td>${reg.estado_pago || 'Sin pago'}</td>
                <td>${reg.nombre_metodo_pago || 'No definido'}</td>
                <td>${reg.productos || '-'}</td>
                <td>${reg.estado_envio || 'No informado'}</td>
                <td>${reg.domicilio || '-'}</td>
                <td>${formatearFecha(reg.fecha_registro)}</td>
                <td style="text-align:center;">
                    <button class="btn-historial" onclick="verDetalle(${reg.id_pedido})">
                        <img src="assets/images/icons/view.png" class="icono-historial">
                    </button>
                    <button class="btn-historial" onclick="descargarComprobante(${reg.id_pedido})">
                        <img src="assets/images/icons/pdf.png" class="icono-historial">
                    </button>
                </td>
            `;
            tablaBody.appendChild(fila);
        });
    }

    function renderPaginador(totalPaginas) {
        paginador.innerHTML = '';
        for (let i = 1; i <= totalPaginas; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.classList.toggle('activo', i === paginaActual);
            btn.addEventListener('click', () => {
                paginaActual = i;
                cargarHistorial();
            });
            paginador.appendChild(btn);
        }
    }

    function formatearFecha(fechaSql) {
        if (!fechaSql) return '';
        const fecha = new Date(fechaSql);
        return fecha.toLocaleDateString('es-AR') + ' ' + fecha.toLocaleTimeString('es-AR', {hour: '2-digit', minute:'2-digit'});
    }

    // Recargar al cambiar filtros
    Object.values(filtros).forEach(input => {
        input.addEventListener('change', () => {
            paginaActual = 1;
            cargarHistorial();
        });
    });

    registrosPorPaginaSelect.addEventListener('change', () => {
        paginaActual = 1;
        cargarHistorial();
    });

    cargarHistorial();
});

// Acciones reales
function verDetalle(idPedido) {
    window.location.href = `index.php?controller=Cliente&action=verDetallePedido&id=${idPedido}`;
}

function descargarComprobante(idPedido) {
    window.location.href = `index.php?controller=Cliente&action=generarComprobante&id=${idPedido}`;
}
</script>
