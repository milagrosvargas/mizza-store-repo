<?php
require_once 'core/Sesion.php';
require_once 'models/ProductosModel.php';

class CarritoController
{
    private $session;
    private $productoModel;

    public function __construct()
    {
        $this->session = new Sesion();
        $this->productoModel = new ProductosModel();
    }

    // =====================================================
    // VISTA PRINCIPAL
    // =====================================================
    public function ver()
    {
        Sesion::iniciar();
        Sesion::inicializarInvitado();

        $carrito = $this->session->get('carrito') ?? [];
        $vista = 'views/carrito/ver.php';
        require_once 'views/layouts/main.php';
    }

    // =====================================================
    // AGREGAR PRODUCTO
    // =====================================================
    public function agregar()
    {
        header('Content-Type: application/json');

        $id = $_POST['id_producto'] ?? null;
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 1;

        if (!$id || $cantidad < 1) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $producto = $this->productoModel->obtenerProductoPorId((int)$id);

        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        // Validar estado de venta
        if (!in_array($producto['id_estado_logico'], [8, 9, 10])) {
            echo json_encode(['success' => false, 'message' => 'Producto no disponible']);
            return;
        }

        // Validar stock real
        $stockActual = $this->productoModel->obtenerStockPorId((int)$id);
        if ($stockActual <= 0) {
            echo json_encode(['success' => false, 'message' => 'No hay stock disponible']);
            return;
        }

        // Obtener carrito de sesión
        $carrito = $this->session->get('carrito') ?? [];

        // Si ya existe → sumar
        if (isset($carrito[$id])) {
            $nuevaCantidad = $carrito[$id]['cantidad'] + $cantidad;

            if ($nuevaCantidad > $stockActual) {
                echo json_encode(['success' => false, 'message' => "Stock insuficiente (máximo $stockActual unidades)."]);
                return;
            }

            $carrito[$id]['cantidad'] = $nuevaCantidad;
        } else {
            if ($cantidad > $stockActual) {
                echo json_encode(['success' => false, 'message' => "Stock insuficiente (máximo $stockActual unidades)."]);
                return;
            }

            $carrito[$id] = [
                'id_producto' => $producto['id_producto'],
                'nombre'      => $producto['nombre_producto'],
                'precio'      => (float)$producto['precio_venta'],
                'imagen'      => $producto['imagen_producto'] ?: 'assets/images/no-image.png',
                'cantidad'    => $cantidad,
                'stock'       => $stockActual
            ];
        }

        // Guardar carrito actualizado
        $this->session->set('carrito', $carrito);

        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado correctamente',
            'carrito' => $carrito
        ]);
    }

    // =====================================================
    // ELIMINAR PRODUCTO
    // =====================================================
    public function eliminar()
    {
        header('Content-Type: application/json');

        $id = $_POST['id_producto'] ?? null;
        $carrito = $this->session->get('carrito') ?? [];

        if (!$id || !isset($carrito[$id])) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }

        unset($carrito[$id]);
        $this->session->set('carrito', $carrito);

        echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
    }

    // =====================================================
    // ACTUALIZAR CANTIDAD
    // =====================================================
    public function actualizar()
    {
        header('Content-Type: application/json');

        $id = $_POST['id_producto'] ?? null;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

        $carrito = $this->session->get('carrito') ?? [];

        if (!$id || !isset($carrito[$id]) || $cantidad < 1) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $stockActual = $this->productoModel->obtenerStockPorId((int)$id);
        if ($cantidad > $stockActual) {
            echo json_encode(['success' => false, 'message' => "Stock insuficiente (máximo $stockActual unidades)."]);
            return;
        }

        $carrito[$id]['cantidad'] = $cantidad;
        $this->session->set('carrito', $carrito);

        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada']);
    }

    // =====================================================
    // VACIAR CARRITO
    // =====================================================
    public function vaciar()
    {
        header('Content-Type: application/json');
        $this->session->remove('carrito');
        echo json_encode(['success' => true, 'message' => 'Carrito vaciado']);
    }

    // =====================================================
    // OBTENER CARRITO
    // =====================================================
    public function obtener()
    {
        header('Content-Type: application/json');
        $carrito = $this->session->get('carrito') ?? [];
        echo json_encode([
            'success' => true,
            'carrito' => $carrito,
            'total'   => $this->calcularTotal($carrito)
        ]);
    }

    // =====================================================
    // CONTAR PRODUCTOS (Navbar)
    // =====================================================
    public function contar()
    {
        header('Content-Type: application/json');
        $carrito = $this->session->get('carrito') ?? [];

        $cantidad = array_sum(array_column($carrito, 'cantidad'));

        echo json_encode(['success' => true, 'cantidad' => $cantidad]);
    }

    // =====================================================
    // CALCULAR TOTAL
    // =====================================================
    private function calcularTotal($carrito)
    {
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return number_format($total, 2, '.', '');
    }
}

