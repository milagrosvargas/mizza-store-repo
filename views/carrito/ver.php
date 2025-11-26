<style>
    /* CONTENEDOR GENERAL DEL CARRITO */
    .carrito-container {
        background-color: #fff;
        box-shadow: 0 6px 24px rgba(136, 14, 79, 0.1);
        padding: 40px 35px;
        max-width: 1000px;
        margin: 40px auto;
        border-radius: 20px;
        overflow-x: auto;
        transition: all 0.3s ease-in-out;
    }

    /* TÍTULO */
    .titulo-carrito {
        color: #880e4f;
        font-weight: 800;
        font-size: 2.1rem;
        border-bottom: 4px solid #880e4f;
        padding-bottom: 14px;
        margin-bottom: 35px;
        letter-spacing: 0.5px;
    }

    /* TABLA DEL CARRITO */
    .tabla-carrito {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.97rem;
        border-radius: 12px;
        overflow: hidden;
    }

    .encabezado-tabla {
        background-color: #880e4f;
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .tabla-carrito th,
    .tabla-carrito td {
        padding: 14px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #f3d5df;
    }

    .tabla-carrito tr:hover {
        background-color: #fff7fa;
        transition: background-color 0.3s ease;
    }

    /* IMAGEN PRODUCTO */
    .carrito-img {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(136, 14, 79, 0.1);
    }

    /* NOMBRE Y PRECIO */
    .tabla-carrito td strong {
        color: #333;
        font-weight: 600;
    }

    .tabla-carrito td small {
        color: #777;
        font-size: 0.85rem;
    }

    /* INPUT CANTIDAD */
    .tabla-carrito input[type="number"] {
        width: 65px;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        padding: 5px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .tabla-carrito input[type="number"]:focus {
        border-color: #ec407a;
        box-shadow: 0 0 0 0.1rem rgba(136, 14, 79, 0.2);
    }

    /* BOTÓN ELIMINAR */
    .btn-eliminar {
        background-color: #fce4ec;
        color: #b71c1c;
        border: none;
        border-radius: 10px;
        padding: 7px 14px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-eliminar:hover {
        background-color: #e53935;
        color: #fff;
        transform: translateY(-1px);
    }

    /* BLOQUE DE TOTALES */
    .total-carrito {
        max-width: 320px;
        margin-left: auto;
        margin-top: 25px;
        text-align: right;
    }

    .total-carrito h4 {
        font-weight: 600;
        color: #333;
    }

    .total-carrito span {
        color: #880e4f;
        font-weight: 700;
        font-size: 1.15rem;
    }

    /* BOTONES PRINCIPALES */
    .btn-volver-tienda,
    .btn-finalizar,
    .btn-vaciar {
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    /* Volver a tienda */
    .btn-volver-tienda {
        background-color: #fce4ec;
        color: #880e4f;
        border: 1px solid #f8bbd0;
    }

    .btn-volver-tienda:hover {
        background-color: #f06292;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Vaciar carrito */
    .btn-vaciar {
        background-color: #fde4e4;
        color: #b71c1c;
        border: 1px solid #f8b0b0;
    }

    .btn-vaciar:hover {
        background-color: #f44336;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Finalizar compra */
    .btn-finalizar {
        background-color: #880e4f;
        color: #fff;
        border: none;
        box-shadow: 0 4px 12px rgba(136, 14, 79, 0.3);
        width: 100%;
        margin-top: 20px;
        font-size: 1rem;
    }

    .btn-finalizar:hover {
        background-color: #ad1457;
        transform: translateY(-2px);
    }

    /* CARRITO VACÍO */
    .carrito-vacio {
        padding: 60px 25px;
        background-color: #fff7fa;
        border-radius: 20px;
        box-shadow: inset 0 0 15px rgba(136, 14, 79, 0.05);
        text-align: center;
    }

    .carrito-vacio img {
        width: 110px;
        margin-bottom: 15px;
        opacity: 0.9;
    }

    .carrito-vacio h5 {
        color: #880e4f;
        font-weight: 600;
    }

    .carrito-vacio p {
        color: #666;
    }

    /* ANIMACIONES */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ==========================================================
   🎨 MODAL DE ENVÍO — ESTILO ACTUALIZADO
   ========================================================== */
    #modalEnvio .modal-content {
        background-color: #fff;
        border-radius: 18px;
        border: 2px solid #f8bbd0;
        box-shadow: 0 6px 18px rgba(136, 14, 79, 0.15);
        animation: fadeIn 0.35s ease-in-out;
        padding: 25px 30px;
    }

    #modalEnvio .modal-title {
        color: #880e4f;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* ==========================================================
   🛍️ BOTONES DE OPCIÓN (RETIRO / DOMICILIO)
   ========================================================== */
    .opcion-envio {
        transition: all 0.25s ease-in-out;
        width: 190px;
        padding: 16px 12px;
        border-radius: 14px;
        font-weight: 600;
        border: 2px solid #f8bbd0;
        color: #880e4f;
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(136, 14, 79, 0.05);
    }

    .opcion-envio:hover {
        background-color: #880e4f;
        color: #fff !important;
        border-color: #880e4f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(136, 14, 79, 0.25);
    }

    .opcion-envio.active,
    .opcion-envio.btn-success {
        background-color: #880e4f !important;
        border-color: #880e4f !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(136, 14, 79, 0.3);
    }

    /* ==========================================================
   ✅ BOTONES DE CONFIRMACIÓN
   ========================================================== */
    #btnConfirmarEnvio,
    #btnConfirmarRetiro {
        background-color: #43a047;
        color: #fff;
        border: none;
        border-radius: 30px;
        font-weight: 600;
        padding: 12px 0;
        margin-top: 10px;
        width: 100%;
        transition: all 0.25s ease-in-out;
        box-shadow: 0 3px 10px rgba(67, 160, 71, 0.3);
    }

    #btnConfirmarEnvio:hover,
    #btnConfirmarRetiro:hover {
        background-color: #2e7d32;
        transform: scale(1.03);
        color: #fff;
    }

    /* ==========================================================
   🏠 BLOQUE DE DOMICILIO MOSTRADO
   ========================================================== */
    #bloqueDomicilio .card {
        background-color: #fff7fa;
        border-left: 4px solid #880e4f;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    #bloqueDomicilio .card:hover {
        background-color: #fff0f5;
        transform: scale(1.01);
    }

    /* ==========================================================
   ✨ ANIMACIÓN
   ========================================================== */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* RESPONSIVE */
    @media (max-width: 768px) {
        .tabla-carrito thead {
            display: none;
        }

        .tabla-carrito tr {
            display: flex;
            flex-direction: column;
            background: #fff;
            margin-bottom: 15px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(136, 14, 79, 0.1);
            padding: 12px;
        }

        .tabla-carrito td {
            border: none !important;
            text-align: left !important;
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
        }

        .tabla-carrito td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #880e4f;
        }

        .btn-volver-tienda,
        .btn-finalizar,
        .btn-vaciar {
            width: 100%;
            margin-top: 10px;
        }

        .total-carrito {
            text-align: center;
        }
    }

    .text-success {
        color: #f884afff !important;
    }

    /* Caja del mensaje de envío (estilo premium Mizza) */
    #mensajeDomicilio {
        background: linear-gradient(135deg, #fff0f6, #ffe3ed);
        border: 1px solid #f5b7cc;
        padding: 18px 20px;
        border-radius: 14px;
        box-shadow: 0 3px 8px rgba(216, 27, 96, 0.15);
        color: #8a1042;
        font-size: 15px;
        font-weight: 500;
        animation: fadeIn 0.3s ease;
    }

    /* Párrafo del texto */
    #mensajeDomicilio p {
        margin: 0 0 10px;
    }

    /* Checkbox Mizza */
    #aceptoEnvio {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #d81b60;
    }

    /* Texto del checkbox */
    #mensajeDomicilio label {
        font-size: 15px;
        font-weight: 500;
        margin-left: 10px;
        color: #8a1042;
        cursor: pointer;
        user-select: none;
    }

    /* Animación suave */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container mt-5 mb-5 carrito-container">
    <h2 class="text-center mb-4 titulo-carrito">Mi carrito</h2>

    <?php if (empty($carrito)): ?>
        <div class="carrito-vacio text-center">
            <img src="assets/images/empty-cart.png" alt="Carrito vacío" class="img-fluid mb-3" style="max-width: 180px;">
            <h5 class="text-muted">Tu carrito está vacío</h5>
            <p class="text-secondary">Explora nuestros productos y ¡agrega lo que más te guste!</p>
            <a href="index.php?controller=Home&action=index" class="btn-volver-tienda">Volver</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center tabla-carrito">
                <thead class="encabezado-tabla">
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($carrito as $item):
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['imagen']) ?>"
                                    alt="<?= htmlspecialchars($item['nombre']) ?>"
                                    class="img-thumbnail rounded carrito-img"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </td>
                            <td class="text-start">
                                <strong><?= htmlspecialchars($item['nombre']) ?></strong>
                            </td>
                            <td>$<?= number_format($item['precio'], 2, ',', '.') ?></td>
                            <td><?= (int)$item['cantidad'] ?></td>
                            <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                            <td>
                                <form method="POST"
                                    action="index.php?controller=Carrito&action=eliminar"
                                    class="d-inline formulario-eliminar">
                                    <input type="hidden" name="id_producto" value="<?= (int)$item['id_producto'] ?>">
                                    <button type="submit" class="btn-eliminar">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total-carrito mt-4">
            <h4 class="text-end">Total: <span>$<?= number_format($total, 2, ',', '.') ?></span></h4>
        </div>

        <div class="acciones-carrito d-flex flex-wrap justify-content-between mt-4">
            <form method="POST" action="index.php?controller=Carrito&action=vaciar" class="form-vaciar">
                <button type="submit" class="btn-vaciar">Vaciar carrito
                </button>
            </form>
            <div>
                <a href="index.php?controller=Home&action=cosmeticos" class="btn-volver-tienda me-2">Seguir comprando
                </a>

                <!-- Botón en la vista del carrito -->
                <a href="#" id="btnFinalizarCompra" class="btn-finalizar">Finalizar compra
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de método de entrega -->
<div class="modal fade" id="modalEnvio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 shadow-lg border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-center text-uppercase fw-bold" style="color:#880e4f;">
                    Seleccioná el método de entrega
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <!-- Bloque dinámico RETIRO / DOMICILIO -->
                <div id="textoEntrega" class="mb-4 d-none text-center">

                    <!-- RETIRO -->
                    <p id="mensajeRetiro" class="d-none">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        Retirás en <strong>Moreno 806, Formosa</strong>
                    </p>

                    <!-- ENVÍO A DOMICILIO -->
                    <div id="mensajeDomicilio" class="alert alert-warning d-none" style="font-size: 14px;">
                        <p class="mb-2">
                            El envío cuesta <strong>$2000</strong> y se sumará al total del pedido.
                        </p>

                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="aceptoEnvio">
                            <label class="form-check-label ms-2" for="aceptoEnvio">
                                Estoy de acuerdo
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Opciones del modal -->
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
                    <button class="btn btn-outline-success opcion-envio" data-tipo="retiro">
                        <i class="bi bi-shop fs-3"></i><br> Retirar en el local
                    </button>
                    <button class="btn btn-outline-success opcion-envio" data-tipo="domicilio">
                        <i class="bi bi-truck fs-3"></i><br> Envío a mi domicilio
                    </button>
                </div>

                <!-- Bloque envío a domicilio -->
                <div id="bloqueDomicilio" class="d-none">
                    <p class="text-muted mb-3">Tu envío se realizará a:</p>

                    <div class="card shadow-sm border-0 p-3 mb-3" style="background:#fff7fa;">
                        <p class="mb-1 fw-semibold text-dark" id="direccionTexto"></p>
                        <p class="mb-0 small text-secondary" id="barrioTexto"></p>
                    </div>

                    <button id="btnConfirmarEnvio" class="btn btn-success w-100">
                        Confirmar envío
                    </button>
                </div>

                <!-- Bloque retiro -->
                <div id="bloqueRetiro" class="d-none">
                    <button id="btnConfirmarRetiro" class="btn btn-primary w-100">
                        Confirmar retiro
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnFinalizarCompra').addEventListener('click', async e => {
        e.preventDefault();

        try {
            const resp = await fetch('index.php?controller=Checkout&action=validarSesion');
            const res = await resp.json();

            if (!res.success) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Iniciá sesión',
                    text: 'Debes iniciar sesión para continuar con la compra.',
                    showCancelButton: true,
                    confirmButtonText: 'Iniciar sesión',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#880e4f'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php?controller=Login&action=login';
                    }
                });
                return;
            }

            new bootstrap.Modal(document.getElementById('modalEnvio')).show();
        } catch (err) {
            Swal.fire('Error', 'No se pudo verificar la sesión', 'error');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {

        const opciones = document.querySelectorAll('.opcion-envio');

        const textoEntrega = document.getElementById('textoEntrega');
        const mensajeRetiro = document.getElementById('mensajeRetiro');
        const mensajeDomicilio = document.getElementById('mensajeDomicilio');
        const checkAcepto = document.getElementById('aceptoEnvio');

        const bloqueDomicilio = document.getElementById('bloqueDomicilio');
        const bloqueRetiro = document.getElementById('bloqueRetiro');

        const direccionTexto = document.getElementById('direccionTexto');
        const barrioTexto = document.getElementById('barrioTexto');

        const btnConfirmarEnvio = document.getElementById('btnConfirmarEnvio');
        const btnConfirmarRetiro = document.getElementById('btnConfirmarRetiro');

        // Selección de tipo de envío
        opciones.forEach(btn => {
            btn.addEventListener('click', async () => {
                const tipo = btn.dataset.tipo;
                sessionStorage.setItem('tipo_envio', tipo);

                opciones.forEach(b => b.classList.remove('btn-success', 'active'));
                btn.classList.add('btn-success', 'active');

                textoEntrega.classList.remove('d-none');

                if (tipo === 'retiro') {
                    mensajeRetiro.classList.remove('d-none');
                    mensajeDomicilio.classList.add('d-none');
                    bloqueDomicilio.classList.add('d-none');
                    bloqueRetiro.classList.remove('d-none');
                } else {
                    mensajeRetiro.classList.add('d-none');
                    mensajeDomicilio.classList.remove('d-none');
                    checkAcepto.checked = false;
                    bloqueRetiro.classList.add('d-none');
                    bloqueDomicilio.classList.remove('d-none');
                    await cargarDomicilio();
                }
            });
        });

        // Confirmar retiro
        btnConfirmarRetiro.addEventListener('click', async () => {
            await guardarEnvioYCrearPedido('retiro', null);
        });

        // Confirmar envío a domicilio
        btnConfirmarEnvio.addEventListener('click', async () => {
            const tipoEnvio = sessionStorage.getItem('tipo_envio');
            const idDomicilio = sessionStorage.getItem('id_domicilio') || null;

            if (!tipoEnvio) {
                Swal.fire('Atención', 'Seleccioná un método de entrega.', 'warning');
                return;
            }

            if (tipoEnvio === 'domicilio' && !checkAcepto.checked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Confirmación pendiente',
                    text: 'Debés aceptar el costo de envío para continuar.',
                    confirmButtonColor: '#9a1b1bff',
                    confirmButtonText: 'Entendido',
                });
                return;
            }

            await guardarEnvioYCrearPedido(tipoEnvio, idDomicilio);
        });

        // Guardó envío y creó el pedido
        async function guardarEnvioYCrearPedido(tipoEnvio, idDom) {
            try {
                const envioData = new FormData();
                envioData.append('tipo_envio', tipoEnvio);
                if (idDom) envioData.append('id_domicilio', idDom);

                const respEnvio = await fetch('index.php?controller=Checkout&action=seleccionarEnvio', {
                    method: 'POST',
                    body: envioData
                });

                const resEnvio = await respEnvio.json();
                if (!resEnvio.success) {
                    Swal.fire('Error', resEnvio.message || 'No se pudo guardar el envío.', 'error');
                    return;
                }

                // Crear pedido en backend
                const respPedido = await fetch('index.php?controller=Checkout&action=generarPedido', {
                    method: 'POST'
                });
                const resPedido = await respPedido.json();

                if (!resPedido.success) {
                    Swal.fire('Error', resPedido.message || 'No se pudo crear el pedido.', 'error');
                    return;
                }

                // Sincronizar visualmente el carrito desde SESSION
                await sincronizarLocalStorageConSession();

                Swal.fire({
                    icon: 'success',
                    title: 'Pedido creado',
                    text: 'Redirigiendo al pago...',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href =
                        `index.php?controller=Checkout&action=pago&id_pedido=${resPedido.id_pedido}`;
                }, 1500);

            } catch (err) {
                Swal.fire('Error', 'No se pudo procesar la compra.', 'error');
            }
        }

        // Sincronizar LocalStorage con SESSION real
        async function sincronizarLocalStorageConSession() {
            try {
                const resp = await fetch('index.php?controller=Carrito&action=obtener');
                const res = await resp.json();

                if (res.success && Array.isArray(res.carrito)) {
                    localStorage.setItem('carritoMizza', JSON.stringify(res.carrito));
                } else {
                    localStorage.removeItem('carritoMizza');
                }

                // Avisar a navbar
                document.dispatchEvent(new Event('carrito:actualizado'));
            } catch (err) {
                console.error('Error al sincronizar carrito:', err);
            }
        }

        // Cargar domicilio del usuario
        async function cargarDomicilio() {
            try {
                const resp = await fetch('index.php?controller=Checkout&action=obtenerDomicilio');
                const res = await resp.json();

                if (res.success && res.data) {
                    const d = res.data;
                    direccionTexto.textContent = d.texto_formateado;
                    barrioTexto.textContent =
                        `${d.nombre_barrio}, ${d.nombre_localidad}, ${d.nombre_provincia}, ${d.nombre_pais}`;
                    sessionStorage.setItem('id_domicilio', d.id_domicilio);
                } else {
                    bloqueDomicilio.innerHTML =
                        `<div class="alert alert-warning mt-3">No se encontró un domicilio registrado.</div>`;
                }
            } catch (err) {
                bloqueDomicilio.innerHTML =
                    `<div class="alert alert-danger mt-3">Error al cargar el domicilio.</div>`;
            }
        }
    });
</script>




<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ELIMINAR PRODUCTO DEL CARRITO (desde SESSION)
        document.querySelectorAll('.formulario-eliminar').forEach(form => {
            form.addEventListener('submit', async e => {
                e.preventDefault();
                const datos = new FormData(form);

                try {
                    const resp = await fetch('index.php?controller=Carrito&action=eliminar', {
                        method: 'POST',
                        body: datos
                    });
                    const res = await resp.json();

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Producto eliminado',
                            text: 'El producto fue eliminado del carrito correctamente.',
                            showConfirmButton: false,
                            timer: 1500,
                            background: '#fff6f8',
                            color: '#d81b60',
                            iconColor: '#d81b60'
                        }).then(async () => {
                            // Remover
                            const fila = form.closest('tr');
                            if (fila) {
                                fila.style.transition = 'opacity 0.4s ease';
                                fila.style.opacity = '0';
                                setTimeout(() => fila.remove(), 400);
                            }

                            verificarCarritoVacio();

                            // Sincronizar visual con SESSION (navbar y LocalStorage)
                            await sincronizarLocalStorageConSession();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'No se pudo eliminar el producto.',
                            confirmButtonColor: '#e06388',
                            background: '#fff6f8',
                            color: '#d81b60'
                        });
                    }
                } catch (err) {
                    console.error('Error eliminando producto:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: 'Ocurrió un problema al eliminar el producto.',
                        confirmButtonColor: '#e06388',
                        background: '#fff6f8',
                        color: '#d81b60'
                    });
                }
            });
        });

        // VACIAR CARRITO COMPLETO
        const formVaciar = document.querySelector('.form-vaciar');
        if (formVaciar) {
            formVaciar.addEventListener('submit', async e => {
                e.preventDefault();

                const confirmar = await Swal.fire({
                    title: '¿Vaciar carrito?',
                    text: 'Se eliminarán todos los productos del carrito.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, vaciar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#e06388',
                    cancelButtonColor: '#aaa',
                    background: '#fff6f8',
                    color: '#d81b60'
                });

                if (!confirmar.isConfirmed) return;

                try {
                    const resp = await fetch('index.php?controller=Carrito&action=vaciar', {
                        method: 'POST'
                    });
                    const res = await resp.json().catch(() => ({
                        success: true
                    }));

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Carrito vaciado',
                            text: 'Tu carrito ahora está vacío.',
                            showConfirmButton: false,
                            timer: 1500,
                            background: '#fff6f8',
                            color: '#d81b60',
                            iconColor: '#d81b60'
                        }).then(async () => {
                            verificarCarritoVacio(true);
                            // Sincronizar LocalStorage y contador desde SESSION
                            await sincronizarLocalStorageConSession();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'No se pudo vaciar el carrito.',
                            confirmButtonColor: '#e06388',
                            background: '#fff6f8',
                            color: '#d81b60'
                        });
                    }

                } catch (err) {
                    console.error('Error al vaciar carrito:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: 'No se pudo completar la acción.',
                        confirmButtonColor: '#e06388',
                        background: '#fff6f8',
                        color: '#d81b60'
                    });
                }
            });
        }

        // Menú vacío visual cuando no hay productos
        function verificarCarritoVacio(forzar = false) {
            const filas = document.querySelectorAll('.tabla-carrito tbody tr');
            if (filas.length === 0 || forzar) {
                const contenedor = document.querySelector('.carrito-container');
                contenedor.innerHTML = `
            <div class="carrito-vacio text-center">
                <img src="assets/images/empty-cart.png" alt="Carrito vacío" class="img-fluid mb-3" style="max-width: 180px;">
                <h5 class="text-muted">Tu carrito está vacío</h5>
                <p class="text-secondary">Explora nuestros productos y ¡agrega lo que más te guste!</p>
                <a href="index.php?controller=Home&action=index" class="btn-volver-tienda">Volver</a>
            </div>`;
            }
        }

        // Sincronización real desde SESSION (backend)
        async function sincronizarLocalStorageConSession() {
            try {
                const resp = await fetch('index.php?controller=Carrito&action=obtener');
                const res = await resp.json();

                if (res.success && res.carrito) {
                    localStorage.setItem('carritoMizza', JSON.stringify(res.carrito));
                } else {
                    localStorage.removeItem('carritoMizza');
                }

                document.dispatchEvent(new Event('carrito:actualizado'));
            } catch (err) {
                console.error('Error sincronizando LocalStorage:', err);
            }
        }

    });
</script>