<?php
require_once 'core/Sesion.php';
require_once 'models/Conexion.php';
require_once 'models/EnvioModel.php';
require_once 'models/PedidoModel.php';
require_once 'models/HistorialModel.php';

class CheckoutController
{
    private $envioModel;
    private $pedidoModel;
    private $historialModel;
    private $db;

    public function __construct()
    {
        Sesion::iniciar();

        $conexion = new Conexion();
        $this->db = $conexion->Conectar();

        $this->envioModel      = new EnvioModel($this->db);
        $this->pedidoModel     = new PedidoModel($this->db);
        $this->historialModel  = new HistorialModel();
    }

    // ======================================================
    // Validar sesión antes de checkout (AJAX frontend)
    // ======================================================
    public function validarSesion()
    {
        header('Content-Type: application/json; charset=utf-8');
        Sesion::iniciar();

        try {
            $usuario = Sesion::obtenerUsuario();

            if (!$usuario || empty($usuario['id_usuario']) || (int)($usuario['relacion_perfil'] ?? 5) === 5) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para continuar con la compra.'
                ]);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error validando sesión: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno al validar sesión.'
            ]);
        }
    }

    // ======================================================
    // Guardar selección temporal de envío
    // ======================================================
    public function seleccionarEnvio()
    {
        header('Content-Type: application/json; charset=utf-8');
        Sesion::iniciar();

        try {
            $tipoEnvio   = $_POST['tipo_envio']   ?? null;
            $idDomicilio = $_POST['id_domicilio'] ?? null;

            if (!$tipoEnvio || !in_array($tipoEnvio, ['retiro', 'domicilio'])) {
                echo json_encode(['success' => false, 'message' => 'Tipo de envío inválido.']);
                return;
            }

            if ($tipoEnvio === 'domicilio' && empty($idDomicilio)) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionarse un domicilio válido.']);
                return;
            }

            $this->envioModel->guardarSeleccionTemporal($tipoEnvio, $idDomicilio);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Error seleccionando envío: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al guardar la selección de envío.']);
        }
    }

    // ======================================================
    // Obtener domicilio del usuario logueado
    // ======================================================
    public function obtenerDomicilio()
    {
        header('Content-Type: application/json; charset=utf-8');
        Sesion::iniciar();

        try {
            $usuario = Sesion::obtenerUsuario();

            if (!$usuario || empty($usuario['id_usuario'])) {
                echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
                return;
            }

            $domicilio = $this->envioModel->obtenerDomicilioPorUsuario((int)$usuario['id_usuario']);

            echo json_encode([
                'success' => (bool)$domicilio,
                'data'    => $domicilio
            ]);
        } catch (Exception $e) {
            error_log("Error obteniendo domicilio: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener domicilio.']);
        }
    }

    // ======================================================
    // Generar pedido completo desde sesión
    // ======================================================
    public function generarPedido()
    {
        header('Content-Type: application/json; charset=utf-8');
        Sesion::iniciar();

        try {
            $usuario = Sesion::obtenerUsuario();

            if (!$usuario || empty($usuario['id_usuario'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para generar el pedido.'
                ]);
                return;
            }

            // ⚠ Validar que exista carrito real en sesión
            if (empty($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El carrito está vacío o no fue encontrado en la sesión.'
                ]);
                return;
            }

            // Crear el pedido (PedidoModel se encarga del resto)
            $result = $this->pedidoModel->crearPedidoDesdeSesion((int)$usuario['id_usuario']);

            // Registrar historial (solo si el pedido se creó con éxito)
            if (!empty($result['success']) && !empty($result['id_pedido'])) {
                $this->historialModel->registrarPorPedido((int)$result['id_pedido']);
            }

            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error en generarPedido: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar el pedido.'
            ]);
        }
    }

    // ======================================================
    // Mostrar vista de pago
    // ======================================================
    public function pago()
    {
        Sesion::iniciar();

        if (!Sesion::usuarioAutenticado()) {
            header('Location: index.php?controller=Login&action=index');
            exit;
        }

        $idPedido = $_GET['id_pedido'] ?? null;

        if (!$idPedido) {
            die("Código de pedido no recibido");
        }

        $pedido    = $this->pedidoModel->obtenerPedidoConDetalles((int)$idPedido);
        $envio     = $this->pedidoModel->obtenerEnvioPorPedido((int)$idPedido);

        $domicilio = null;
        $usuario   = Sesion::obtenerUsuario();

        if ($envio && !empty($envio['id_domicilio']) && $usuario) {
            $domicilio = $this->envioModel->obtenerDomicilioPorUsuario((int)$usuario['id_usuario']);
        }

        $data = [
            'pedido'    => $pedido,
            'envio'     => $envio,
            'domicilio' => $domicilio
        ];

        $vista = 'views/checkout/pago.php';
        require_once 'views/layouts/main.php';
    }





    
}
?>