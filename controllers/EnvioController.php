<?php
require_once 'models/EnvioModel.php';
require_once 'models/PedidoModel.php';

class EnvioController
{
    private $envioModel;
    private $pedidoModel;

    public function __construct()
    {
        $conexion = new Conexion();
        $db = $conexion->Conectar();

        $this->envioModel = new EnvioModel($db);
        $this->pedidoModel = new PedidoModel($db);
    }

    // ==========================================================
    // 📄 VISTA PRINCIPAL
    // ==========================================================
    public function listarPedidosView()
    {
        Sesion::iniciar();
        Sesion::requerirLogin(); 

        $vista = 'views/empleado/envios_listado.php';
        $titulo = "Gestión de envíos";
        $data = [];

        require_once 'views/layouts/main.php';
    }

    // ==========================================================
    // LISTAR PEDIDOS Y ENVÍOS (AJAX)
    // ==========================================================
    public function listarPedidosEnvio()
    {
        header('Content-Type: application/json');

        try {
            $pedidos = $this->envioModel->obtenerPedidosConEnvio();
            echo json_encode(['success' => true, 'data' => $pedidos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================================
    // LISTAR REPARTIDORES (AJAX)
    // ==========================================================
    public function listarRepartidores()
    {
        header('Content-Type: application/json');

        try {
            $repartidores = $this->envioModel->obtenerRepartidoresActivos();
            echo json_encode(['success' => true, 'data' => $repartidores]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================================
    // 🏷️ ASIGNAR REPARTIDOR Y MARCAR COMO EN CAMINO
    // ==========================================================
    public function asignarEnvio()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['id_pedido'], $_POST['id_repartidor'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $idPedido = intval($_POST['id_pedido']);
        $idRepartidor = intval($_POST['id_repartidor']);

        try {
            $asignado = $this->envioModel->asignarRepartidorExistente($idPedido, $idRepartidor);

            if (!$asignado) {
                throw new Exception("No se pudo asignar el repartidor.");
            }

            $this->pedidoModel->actualizarEstadoPedido($idPedido, 'Enviado');

            echo json_encode(['success' => true, 'message' => 'Pedido autorizado al transportista']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================================
    // MARCAR ENVÍO "EN CAMINO"
    // ==========================================================
    public function marcarEnCamino()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['id_envio'])) {
            echo json_encode(['success' => false, 'message' => 'ID de envío no recibido']);
            return;
        }

        $idEnvio = intval($_POST['id_envio']);

        try {
            if (!$this->envioModel->existeEnvio($idEnvio)) {
                echo json_encode(['success' => false, 'message' => 'El envío no existe']);
                return;
            }

            $this->envioModel->actualizarEstadoEnvio($idEnvio, 'en camino');

            echo json_encode([
                'success' => true,
                'message' => 'El envío está en camino'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================================
    // 📦 MARCAR ENVÍO "ENTREGADO"
    // ==========================================================
    public function marcarEntregado()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['id_envio'])) {
            echo json_encode(['success' => false, 'message' => 'ID de envío no recibido']);
            return;
        }

        $idEnvio = intval($_POST['id_envio']);

        try {
            if (!$this->envioModel->existeEnvio($idEnvio)) {
                echo json_encode(['success' => false, 'message' => 'El envío no existe']);
                return;
            }

            $this->envioModel->actualizarEstadoEnvio($idEnvio, 'entregado');

            echo json_encode([
                'success' => true,
                'message' => 'El envío ha sido entregado'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==========================================================
    // 📊 ESTADÍSTICAS GRÁFICAS (CHART.JS)
    // ==========================================================
    public function obtenerEstadisticasEnvio()
    {
        header('Content-Type: application/json');

        try {
            $data = $this->envioModel->obtenerEstadisticasEnvios();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function marcarEntregadoAdmin()
{
    header('Content-Type: application/json');
    Sesion::iniciar();
    Sesion::requerirLogin();

    if (empty($_POST['id_pedido'])) {
        echo json_encode(['success' => false, 'message' => 'Pedido inválido.']);
        return;
    }

    $idPedido = intval($_POST['id_pedido']);

    try {
        $this->envioModel->marcarComoEntregadoAdmin($idPedido);

        echo json_encode([
            'success' => true,
            'message' => 'Pedido marcado como entregado correctamente.'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

}
