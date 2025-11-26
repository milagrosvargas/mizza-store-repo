<?php
require_once 'core/Sesion.php';
require_once 'models/Conexion.php';
require_once 'models/PedidoModel.php';
require_once 'models/PeriodoModel.php'; 

class PedidoController
{
    private $db;
    private $pedidoModel;
    private $periodoModel;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();

        $this->pedidoModel = new PedidoModel($this->db);
        $this->periodoModel = new PeriodoModel(); 
    }

    // ===============================================================
    // 1) MOSTRAR LISTADO DE PEDIDOS (vista para administrador/cliente)
    // ===============================================================
    public function verListado()
    {
        Sesion::iniciar();
        
        // Si es cliente, se podría filtrar por id_usuario o id_cliente
        $idCliente = $_SESSION['id_cliente'] ?? null;
        
        if ($idCliente) {
            $pedidos = $this->pedidoModel->obtenerPedidosPorCliente($idCliente);
        } else { 
            // aquí iría un método para listar todos si es admin (aún no definido)
            $pedidos = []; 
        }
        
        $vista = 'views/pedido/listado.php';
        require_once 'views/layouts/main.php';
    }

    // ===============================================================
    // 2) CREAR PEDIDO DESDE EL CARRITO (JSON / AJAX)
    // ===============================================================
    public function crearDesdeCarrito()
    {
        header('Content-Type: application/json');
        Sesion::iniciar();

        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para continuar.']);
            return;
        }

        $idUsuario = (int)$_SESSION['id_usuario'];

        $resultado = $this->pedidoModel->crearPedidoDesdeSesion($idUsuario);

        // Si se creó correctamente, podemos (opcional) vincular el pedido a un período
        if ($resultado['success'] && isset($_POST['id_periodo'])) {
            $idPeriodo = (int)$_POST['id_periodo'];
            $idPedido  = (int)$resultado['id_pedido'];

            $this->periodoModel->asociarPedidoPeriodo($idPeriodo, $idPedido);
        }

        echo json_encode($resultado);
    }

    // ===============================================================
    // 3) DETALLE DE UN PEDIDO POR ID (Vista normal)
    // ===============================================================
    public function verDetalle()
    {
        Sesion::iniciar();

        $idPedido = $_GET['id_pedido'] ?? null;
        if (!$idPedido) {
            echo "Pedido no especificado.";
            return;
        }

        $pedido = $this->pedidoModel->obtenerPedidoConDetalles((int)$idPedido);
        if (!$pedido) {
            echo "Pedido no encontrado.";
            return;
        }

        $vista = 'views/pedido/detalle.php';
        require_once 'views/layouts/main.php';
    }

    // ===============================================================
    // 4) LISTAR PEDIDOS CON ESTADO DE ENVÍO (Cliente)
    // ===============================================================
    public function verHistorial()
    {
        Sesion::iniciar();

        if (!isset($_SESSION['id_cliente'])) {
            echo "Debe iniciar sesión.";
            return;
        }

        $idCliente = (int)$_SESSION['id_cliente'];
        $pedidos = $this->pedidoModel->obtenerPedidosConEstadoEnvio($idCliente);

        $vista = 'views/pedido/historial.php';
        require_once 'views/layouts/main.php';
    }

    // ===============================================================
    // 5) ACTUALIZAR ESTADO LÓGICO DEL PEDIDO (admin)
    // ===============================================================
    public function actualizarEstado()
    {
        header('Content-Type: application/json');
        Sesion::iniciar();

        if (!isset($_POST['id_pedido'], $_POST['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        try {
            $idPedido = (int)$_POST['id_pedido'];
            $nuevoEstado = $_POST['estado'];

            $ok = $this->pedidoModel->actualizarEstadoPedido($idPedido, $nuevoEstado);

            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Estado actualizado correctamente.' : 'No se pudo actualizar.'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}