document.addEventListener('DOMContentLoaded', function () {

    const contenedor = document.getElementById('contenedorProductos');
    const paginacion = document.getElementById('contenedorPaginacion');
    const selectCategoria = document.getElementById('filtroCategoria');
    const selectSubCategoria = document.getElementById('filtroSubCategoria');

    // ===============================
    // 🔎 Obtener parámetros de la URL
    // ===============================
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }
    const slugCategoria = getQueryParam('categoria');

    // ===============================
    // 🧩 Normalizar texto (slugify)
    // ===============================
    function slugify(text) {
        return text.toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '');
    }

    // ===============================
    // 📂 Cargar categorías
    // ===============================
    function cargarCategorias() {
        fetch('index.php?controller=Catalogo&action=listarCategoriasAjax')
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error('Error al obtener categorías');
                selectCategoria.innerHTML = '<option value="">Todas las categorías</option>';

                res.data.forEach(c => {
                    selectCategoria.innerHTML += `<option value="${c.id_categoria}">${c.nombre_categoria}</option>`;
                });

                if (slugCategoria) {
                    const categoriaCoincidente = res.data.find(c => slugify(c.nombre_categoria) === slugCategoria);
                    if (categoriaCoincidente) {
                        selectCategoria.value = categoriaCoincidente.id_categoria;
                        cargarSubcategorias(categoriaCoincidente.id_categoria);
                        cargarProductos(1);

                        const titulo = document.querySelector('.row-2 h2');
                        if (titulo) titulo.textContent = categoriaCoincidente.nombre_categoria;
                        return;
                    }
                }

                cargarProductos();
            })
            .catch(err => console.error('Error:', err));
    }

    // ===============================
    // 🪶 Cargar subcategorías
    // ===============================
    function cargarSubcategorias(idCategoria) {
        selectSubCategoria.innerHTML = '<option value="">Todas las subcategorías</option>';
        selectSubCategoria.disabled = true;

        if (!idCategoria) return;

        const datos = new FormData();
        datos.append('id_categoria', idCategoria);

        fetch('index.php?controller=Catalogo&action=listarSubCategoriasAjax', {
            method: 'POST',
            body: datos
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error('Error al obtener subcategorías');
                if (res.data.length > 0) {
                    selectSubCategoria.disabled = false;
                    res.data.forEach(sc => {
                        selectSubCategoria.innerHTML += `<option value="${sc.id_sub_categoria}">${sc.nombre_sub_categoria}</option>`;
                    });
                }
            })
            .catch(err => console.error('Error:', err));
    }

    // ===============================
    // 🛍️ Cargar productos filtrados
    // ===============================
    function cargarProductos(pagina = 1) {
        const datos = new FormData();
        datos.append('buscar', document.getElementById('buscar').value);
        datos.append('orden', document.getElementById('ordenar').value);
        datos.append('pagina', pagina);
        datos.append('porPagina', document.getElementById('registrosPorPagina').value);
        datos.append('precio_min', document.getElementById('precioMin').value);
        datos.append('precio_max', document.getElementById('precioMax').value);
        datos.append('id_categoria', selectCategoria.value);
        datos.append('id_sub_categoria', selectSubCategoria.value);

        contenedor.innerHTML = `<p style="color:#e91e63;text-align:center;margin-top:40px;">Cargando productos...</p>`;
        paginacion.innerHTML = '';

        fetch('index.php?controller=Catalogo&action=listarProductosAjax', {
            method: 'POST',
            body: datos
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Error desconocido');
                renderProductos(res.data);
                renderPaginacion(res.total, res.porPagina, pagina);
            })
            .catch(err => {
                contenedor.innerHTML = `<p style="color:red;text-align:center;">${err.message}</p>`;
                paginacion.innerHTML = '';
            });
    }

    // ===============================
    // 🎨 Renderizar productos
    // ===============================
    function renderProductos(productos) {
        contenedor.innerHTML = '';

        if (!productos || productos.length === 0) {
            contenedor.innerHTML = '<p class="no-products">No se encontraron productos.</p>';
            return;
        }

        const html = productos.map(p => {
            const precio = parseFloat(p.precio_venta || 0).toLocaleString('es-AR', { minimumFractionDigits: 2 });
            const imagen = p.imagen_producto && p.imagen_producto.trim() !== ''
                ? p.imagen_producto
                : 'assets/images/no-image.png';

            // ✅ Aseguramos tomar correctamente el stock_actual del backend
            const stock = parseInt(p.stock_actual ?? p.stock ?? p.cantidad_disponible ?? 0);

            // ✅ Tomamos la marca si viene desde el JOIN, o mostramos “Sin marca”
            const marca = p.nombre_marca ? p.nombre_marca : 'Sin marca';

            const stockTexto = stock > 0
                ? `<p class="stock-info">Stock: <strong>${stock}</strong> unidades</p>`
                : `<p class="stock-info sin-stock" style="color:#d81b60;">Sin stock</p>`;

            const disabledAttr = stock < 1 ? 'disabled style="opacity:0.6;cursor:not-allowed;"' : '';

            return `
                <div class="col-4 producto-item" style="animation: fadeIn 0.4s ease;">
                    <div class="producto-card">
                        <a href="#">
                            <img src="${imagen}" alt="${p.nombre_producto}" loading="lazy"
                                 onerror="this.src='assets/images/no-image.png'">
                        </a>
                        <h4><a href="#">${p.nombre_producto}</a></h4>
                        <p class="marca-info">Marca: <strong>${marca}</strong></p>
                        <p>$${precio}</p>
                        ${stockTexto}
                        <button class="btn-agregar-carrito"
                                data-id="${p.id_producto}"
                                data-nombre="${p.nombre_producto}"
                                data-precio="${p.precio_venta}"
                                data-stock="${stock}"
                                ${disabledAttr}>
                            🛒 Agregar
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        contenedor.innerHTML = html;

        // ⚡ Emitir evento global para el carrito
        document.dispatchEvent(new CustomEvent('productosRenderizados'));
    }

    // ===============================
    // 📄 Renderizar paginación
    // ===============================
    function renderPaginacion(total, porPagina, paginaActual) {
        const totalPaginas = Math.ceil(total / porPagina);
        paginacion.innerHTML = '';
        if (totalPaginas <= 1) return;

        let html = '';

        if (paginaActual > 1)
            html += `<span class="pagina-btn" data-pag="${paginaActual - 1}">&laquo;</span>`;

        for (let i = 1; i <= totalPaginas; i++) {
            html += `<span class="pagina-btn ${i === paginaActual ? 'active' : ''}" data-pag="${i}">${i}</span>`;
        }

        if (paginaActual < totalPaginas)
            html += `<span class="pagina-btn" data-pag="${paginaActual + 1}">&raquo;</span>`;

        paginacion.innerHTML = html;

        paginacion.querySelectorAll('.pagina-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const pag = parseInt(btn.dataset.pag);
                if (!isNaN(pag)) cargarProductos(pag);
            });
        });
    }

    // ===============================
    // 🎚️ Filtros y eventos
    // ===============================
    selectCategoria.addEventListener('change', e => {
        const idCategoria = e.target.value;
        cargarSubcategorias(idCategoria);
        cargarProductos(1);
    });
    selectSubCategoria.addEventListener('change', () => cargarProductos(1));

    const filtros = ['#buscar', '#ordenar', '#registrosPorPagina', '#precioMin', '#precioMax'];
    filtros.forEach(sel => {
        const el = document.querySelector(sel);
        if (el) {
            el.addEventListener('change', () => cargarProductos(1));
            if (sel === '#buscar') {
                el.addEventListener('keyup', e => {
                    if (e.key === 'Enter') cargarProductos(1);
                });
            }
        }
    });

    // ===============================
    // 💫 Animación suave
    // ===============================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);

    // ===============================
    // 🚀 Carga inicial
    // ===============================
    cargarCategorias();
});
