<?php
require_once 'models/RepartidorModel.php';

class RepartidorController
{
    private $repartidorModel;
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
        $this->repartidorModel = new RepartidorModel($this->db);
    }

    // ==========================================================
    // LISTAR PEDIDOS ASIGNADOS (AJAX)
    // ==========================================================
    public function listarPedidosAsignados()
    {
        header('Content-Type: application/json');
        Sesion::iniciar();
        Sesion::requerirLogin();

        try {
            $idRepartidor = $this->obtenerIdRepartidorDesdeSesion();

            if (!$idRepartidor) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró un repartidor asociado a este usuario.'
                ]);
                return;
            }

            $pedidos = $this->repartidorModel->obtenerPedidosAsignados($idRepartidor);

            echo json_encode([
                'success' => true,
                'data' => $pedidos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ==========================================================
    // MÉTODO PRIVADO - OBTENER ID_REPARTIDOR DESDE LA SESIÓN
    // ==========================================================
    private function obtenerIdRepartidorDesdeSesion(): ?int
    {
        $usuario = Sesion::obtenerUsuario();
        $idPersona = $usuario['relacion_persona'] ?? null;

        if (!$idPersona) {
            return null;
        }

        try {
            $sql = "SELECT id_repartidor 
                    FROM repartidor 
                    WHERE relacion_persona = :idPersona 
                      AND estado_repartidor = 1
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() ?: null;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener repartidor: " . $e->getMessage());
        }
    }
    public function marcarEntrega()
    {
        header('Content-Type: application/json');
        Sesion::iniciar();
        Sesion::requerirLogin();

        if (!isset($_POST['id_pedido'])) {
            echo json_encode(['success' => false, 'message' => 'Código de pedido requerido']);
            return;
        }

        try {
            $idPedido = intval($_POST['id_pedido']);
            $idRepartidor = $this->obtenerIdRepartidorDesdeSesion();

            if (!$idRepartidor) {
                echo json_encode(['success' => false, 'message' => 'Repartidor no válido']);
                return;
            }

            $resultado = $this->repartidorModel->marcarPedidoEntregado($idPedido, $idRepartidor);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Confirmación de entrega registrada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo registrar la confirmación de entrega']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    // ==========================================================
    // VISTA COMPLETA DE PEDIDOS ASIGNADOS
    // ==========================================================
    public function ver()
    {
        Sesion::iniciar();
        Sesion::requerirLogin();

        try {
            $idRepartidor = $this->obtenerIdRepartidorDesdeSesion();

            if (!$idRepartidor) {
                throw new Exception('No se encontró repartidor asociado al usuario.');
            }

            // Obtenemos pedidos SIN AJAX para carga inicial
            $pedidos = $this->repartidorModel->obtenerPedidosAsignados($idRepartidor);

            $vista = 'views/repartidor/ver_pedidos.php';
            $titulo = 'Mis pedidos asignados';
            $data = ['pedidos' => $pedidos];

            require_once 'views/layouts/main.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
        }
    }
}