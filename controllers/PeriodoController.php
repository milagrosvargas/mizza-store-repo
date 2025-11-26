<?php
require_once 'core/Sesion.php';
require_once 'models/Conexion.php';
require_once 'models/PeriodoModel.php';

class PeriodoController
{
    private $db;
    private $periodoModel;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
        $this->periodoModel = new PeriodoModel();
    }

    /* ===============================================================
       1) VISTA PRINCIPAL DE PERÍODOS Y ESTADÍSTICAS
       =============================================================== */
    public function ver()
    {
        Sesion::iniciar();

        // Obtener períodos para el <select>
        $periodos = $this->periodoModel->listarPeriodosParaSelect();

        $vista = 'views/periodos/periodos.php';
        require_once 'views/layouts/main.php';
    }

    /* ===============================================================
       2) LISTAR PERÍODOS (AJAX, carga parcial)
       =============================================================== */
    public function listar()
    {
        header('Content-Type: text/html');
        $periodos = $this->periodoModel->listarPeriodos();
        require_once 'views/periodos/listado_periodos.php';
    }

    /* ===============================================================
       3) CREAR NUEVO PERÍODO (AJAX, JSON)
       =============================================================== */
    public function crear()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['nombre_periodo'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $ok = $this->periodoModel->crearPeriodo($_POST);
        echo json_encode(['success' => $ok]);
    }

    /* ===============================================================
       4) ESTADÍSTICAS: PRODUCTOS VENDIDOS POR PERÍODO (JSON)
       =============================================================== */
    public function estadisticas()
    {
        header('Content-Type: application/json');
        $idPeriodo = $_GET['id_periodo'] ?? null;

        if (!$idPeriodo) {
            echo json_encode([]);
            return;
        }

        $datos = $this->periodoModel->getProductosVendidosPorPeriodo((int)$idPeriodo);
        echo json_encode($datos);
    }
    /* ===============================================================
   (5) Estadística: Categorías más vendidas (JSON)
   =============================================================== */
    public function estadisticasCategorias()
    {
        header('Content-Type: application/json');
        $idPeriodo = $_GET['id_periodo'] ?? null;

        if (!$idPeriodo) {
            echo json_encode([]);
            return;
        }

        $datos = $this->periodoModel->getCategoriasMasVendidas((int)$idPeriodo);
        echo json_encode($datos);
    }
    /* ===============================================================
   (6) Estadística: Subcategorías más vendidas (JSON)
   =============================================================== */
    public function estadisticasSubcategorias()
    {
        header('Content-Type: application/json');
        $idPeriodo = $_GET['id_periodo'] ?? null;

        if (!$idPeriodo) {
            echo json_encode([]);
            return;
        }

        $datos = $this->periodoModel->getSubcategoriasMasVendidas((int)$idPeriodo);
        echo json_encode($datos);
    }
}
