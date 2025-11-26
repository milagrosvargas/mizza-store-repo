<?php
require_once 'Conexion.php';

class UbicacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conexion())->Conectar();
    }

    /** ===============================
     *  📍 PAÍSES
     *  =============================== */
    public function obtenerPaises()
    {
        $sql = "SELECT id_pais, nombre_pais FROM pais ORDER BY nombre_pais";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ===============================
     *  🗺️ PROVINCIAS POR PAÍS
     *  =============================== */
    public function obtenerProvinciasPorPais($idPais)
    {
        $sql = "SELECT id_provincia, nombre_provincia FROM provincia WHERE id_pais = ? ORDER BY nombre_provincia";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idPais]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ===============================
     *  🌆 LOCALIDADES POR PROVINCIA
     *  =============================== */
    public function obtenerLocalidadesPorProvincia($idProvincia)
    {
        $sql = "SELECT id_localidad, nombre_localidad FROM localidad WHERE id_provincia = ? ORDER BY nombre_localidad";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idProvincia]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ===============================
     *  🏘️ BARRIOS POR LOCALIDAD
     *  =============================== */
    public function obtenerBarriosPorLocalidad($idLocalidad)
    {
        $sql = "SELECT id_barrio, nombre_barrio FROM barrio WHERE id_localidad = ? ORDER BY nombre_barrio";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idLocalidad]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
