<style>
/* =============================
   💄 ESTILOS BASE — MizzaStore
   ============================= */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #fff8f9;
    color: #333;
    line-height: 1.6;
    overflow: visible;
}

h2 {
    font-weight: 600;
    color: #e91e63;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 26px;
}

a {
    text-decoration: none;
    color: inherit;
    transition: color 0.3s ease;
}

img {
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: block;
    width: 100%;
}

img:hover {
    transform: scale(1.04);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* =============================
   🧱 CONTENEDOR PRINCIPAL
   ============================= */
.small-container {
    max-width: 1200px;
    margin: 30px auto 60px;
    padding: 0 20px;
}

/* =============================
   🏷️ ENCABEZADO
   ============================= */
.row-2 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 40px auto 25px;
    flex-wrap: wrap;
}

.row-2 h2 {
    flex: 1;
    text-align: left;
    color: #e91e63;
    font-size: 26px;
}

/* =============================
   🔍 BUSCADOR Y FILTROS
   ============================= */
.buscador {
    background-color: #fff;
    padding: 12px 18px;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.buscador input[type="text"],
.buscador input[type="number"],
.buscador select {
    border: 1px solid #f48fb1;
    padding: 10px 14px;
    border-radius: 25px;
    background-color: #fff;
    color: #e91e63;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.3s ease;
}

.buscador input::placeholder {
    color: #f8bbd0;
}

.buscador input:focus,
.buscador select:focus {
    outline: none;
    box-shadow: 0 0 8px rgba(233, 30, 99, 0.3);
}

.buscador input:hover,
.buscador select:hover {
    background-color: #fce4ec;
}

/* =============================
   🛍️ PRODUCTOS
   ============================= */
.row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
}

.col-4 {
    flex: 1 1 220px;
    background: #fff;
    border-radius: 15px;
    padding: 15px;
    text-align: center;
    transition: all 0.4s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    min-height: 340px;
    max-width: 250px;
    position: relative;
}

.col-4:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
}

.col-4 img {
    height: 190px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 10px;
}

.col-4 h4 {
    font-weight: 500;
    color: #333;
    margin: 8px 0 4px;
    transition: color 0.3s ease;
    font-size: 15px;
    height: 40px;
    overflow: hidden;
}

.col-4 h4:hover {
    color: #e91e63;
}

.col-4 p {
    color: #e91e63;
    font-weight: 600;
    font-size: 15px;
    margin-top: 6px;
}

/* =============================
   🧮 CONTROLES DE CANTIDAD
   ============================= */
.carrito-controles {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
}

.carrito-controles button {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 50%;
    background-color: #f8bbd0;
    color: #e91e63;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
}

.carrito-controles button:hover {
    background-color: #e91e63;
    color: #fff;
}

.cantidad-input {
    width: 42px;
    text-align: center;
    border: 1px solid #f48fb1;
    border-radius: 6px;
    padding: 4px;
    font-weight: 500;
    color: #e91e63;
}

/* =============================
   🛒 BOTÓN AGREGAR AL CARRITO
   ============================= */
.btn-agregar-carrito {
    margin-top: 10px;
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 25px;
    padding: 8px 18px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-agregar-carrito:hover {
    background: #d81b60;
    transform: scale(1.05);
}

.btn-agregar-carrito:active {
    transform: scale(0.97);
}

/* =============================
   📄 PAGINACIÓN
   ============================= */
.page-btn {
    text-align: center;
    margin: 40px auto 60px;
}

.page-btn span {
    display: inline-block;
    border: 1px solid #e91e63;
    color: #e91e63;
    margin: 4px;
    width: 38px;
    height: 38px;
    line-height: 38px;
    text-align: center;
    border-radius: 50%;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
}

.page-btn span:hover,
.page-btn span.active {
    background: #e91e63;
    color: #fff;
    transform: scale(1.12);
}

/* =============================
   🚫 SIN PRODUCTOS
   ============================= */
.no-products {
    text-align: center;
    color: #999;
    font-size: 18px;
    padding: 60px 0;
}

</style>


<!-- <link rel="stylesheet" href="/MizzaStore/assets/css/cosmeticos.css"> -->

<body>
    <div class="small-container">
        <!-- 🔖 Título dinámico de la categoría -->
        <div class="row row-2">
            <h2><?= htmlspecialchars($nombreCategoria ?? 'Cosméticos') ?></h2>
        </div>

        <!-- 🔍 Buscador y filtros -->
        <div class="buscador">
            <input type="text" id="buscar" placeholder="Buscar producto...">

            <select id="filtroCategoria">
                <option value="">Todas las categorías</option>
            </select>

            <select id="filtroSubCategoria" disabled>
                <option value="">Todas las subcategorías</option>
            </select>

            <input type="number" id="precioMin" placeholder="Precio min" min="0" style="width:110px;">
            <input type="number" id="precioMax" placeholder="Precio max" min="0" style="width:110px;">

            <select id="ordenar">
                <option value="ASC">A - Z</option>
                <option value="DESC">Z - A</option>
            </select>

            <select id="registrosPorPagina" title="Registros por página">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
        </div>

        <!-- 🛍️ Contenedor dinámico de productos -->
        <div class="row" id="contenedorProductos"></div>

        <!-- 📄 Paginación dinámica -->
        <div class="page-btn" id="contenedorPaginacion"></div>
    </div>

    <script src="/MizzaStore/assets/js/catalogo.js"></script>

    <!-- <script src="/MizzaStore/assets/js/carrito.js"></script> -->
    
</body>


<script>
document.addEventListener('DOMContentLoaded', () => {

  // 🧠 Obtener carrito desde localStorage
  function obtenerCarrito() {
    try {
      return JSON.parse(localStorage.getItem('carritoMizza')) || [];
    } catch {
      return [];
    }
  }

  // 💾 Guardar carrito en localStorage
  function guardarCarrito(carrito) {
    localStorage.setItem('carritoMizza', JSON.stringify(carrito));
    // 🔔 Notificar al contador global (navbar u otro componente)
    document.dispatchEvent(new Event('carrito:actualizado'));
  }

  // 🔄 Sincronizar con backend (CarritoController::agregar)
  async function syncAgregarBackend(id, cantidad = 1) {
    try {
      const res = await fetch('index.php?controller=Carrito&action=agregar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_producto=${encodeURIComponent(id)}&cantidad=${encodeURIComponent(cantidad)}`
      });
      const data = await res.json();
      if (!data.success) console.warn('⚠️ No se pudo sincronizar con backend:', data.message);
    } catch (err) {
      console.error('❌ Error de sincronización:', err);
    }
  }

  // 🎉 Alerta visual de agregado
  function mostrarAlertaAgregado(nombre) {
    Swal.fire({
      icon: 'success',
      title: 'Producto agregado',
      text: `"${nombre}" se añadió al carrito.`,
      timer: 1400,
      showConfirmButton: false,
      background: '#fff6f8',
      color: '#d81b60',
      iconColor: '#d81b60'
    });
  }

  // ➕ Agregar producto al carrito con validación de stock
  function agregarProducto(id, nombre, precio, imagen = '/MizzaStore/assets/images/no-image.png', stock = 0) {
    let carrito = obtenerCarrito();
    const existente = carrito.find(p => p.id === id);

    if (existente) {
      // 🚫 Validar stock antes de incrementar cantidad
      if (existente.cantidad + 1 > stock) {
        Swal.fire({
          icon: 'warning',
          title: 'Stock insuficiente',
          text: `Solo hay ${stock} unidades disponibles de "${nombre}".`,
          background: '#fff6f8',
          color: '#d81b60',
          iconColor: '#d81b60'
        });
        return;
      }
      existente.cantidad += 1;
    } else {
      // 🚫 Validar que haya stock antes de agregar por primera vez
      if (stock < 1) {
        Swal.fire({
          icon: 'error',
          title: 'Sin stock disponible',
          text: `"${nombre}" no se puede agregar porque no hay unidades disponibles.`,
          background: '#fff6f8',
          color: '#d81b60',
          iconColor: '#d81b60'
        });
        return;
      }

      carrito.push({
        id,
        nombre,
        precio: parseFloat(precio),
        cantidad: 1,
        imagen,
        stock
      });
    }

    guardarCarrito(carrito);
    mostrarAlertaAgregado(nombre);
    syncAgregarBackend(id, 1);
  }

  // 🧷 Vincular botones "Agregar al carrito"
  function activarBotonesAgregar() {
    document.querySelectorAll('.btn-agregar-carrito').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const id = parseInt(btn.dataset.id);
        const nombre = btn.dataset.nombre;
        const precio = parseFloat(btn.dataset.precio);
        const stock = parseInt(btn.dataset.stock || 0);
        const card = btn.closest('.producto-card');
        const imagen = card?.querySelector('img')?.src || '/MizzaStore/assets/images/no-image.png';

        agregarProducto(id, nombre, precio, imagen, stock);
      });
    });
  }

  // 🔁 Activar eventos tras renderizado dinámico de productos
  document.addEventListener('productosRenderizados', activarBotonesAgregar);

  // 🧩 Inicialización
  activarBotonesAgregar();

});
</script>
