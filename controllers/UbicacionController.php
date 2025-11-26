<?php
require_once 'models/UbicacionModel.php';

class UbicacionController
{
    private $ubicacionModel;

    public function __construct()
    {
        $this->ubicacionModel = new UbicacionModel();
    }

    /** ===============================
     *  📍 LISTAR PAÍSES
     *  =============================== */
    public function obtenerPaises()
    {
        $data = $this->ubicacionModel->obtenerPaises();
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /** ===============================
     *  🗺️ PROVINCIAS POR PAÍS
     *  =============================== */
    public function obtenerProvincias()
    {
        $idPais = isset($_GET['id_pais']) ? intval($_GET['id_pais']) : 0;

        if ($idPais <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de país inválido']);
            return;
        }

        $data = $this->ubicacionModel->obtenerProvinciasPorPais($idPais);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /** ===============================
     *  🌆 LOCALIDADES POR PROVINCIA
     *  =============================== */
    public function obtenerLocalidades()
    {
        $idProvincia = isset($_GET['id_provincia']) ? intval($_GET['id_provincia']) : 0;

        if ($idProvincia <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de provincia inválido']);
            return;
        }

        $data = $this->ubicacionModel->obtenerLocalidadesPorProvincia($idProvincia);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /** ===============================
     *  🏘️ BARRIOS POR LOCALIDAD
     *  =============================== */
    public function obtenerBarrios()
    {
        $idLocalidad = isset($_GET['id_localidad']) ? intval($_GET['id_localidad']) : 0;

        if ($idLocalidad <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de localidad inválido']);
            return;
        }

        $data = $this->ubicacionModel->obtenerBarriosPorLocalidad($idLocalidad);
        echo json_encode(['success' => true, 'data' => $data]);
    }
}
?>
