<?php
require_once "models/Conexion.php";
require_once "models/MasterModel.php";

/**
 * =========================================================
 * MODELO: PersonaModel
 * ---------------------------------------------------------
 * Gestiona los datos personales, domicilio y contacto
 * de la persona asociada a un usuario del sistema.
 * =========================================================
 */
class PersonaModel
{
    // =========================================================
    // ATRIBUTOS
    // =========================================================
    private $conn;
    private $masterModel;

    // =========================================================
    // CONSTRUCTOR
    // =========================================================
    public function __construct()
    {
        $conexion = new Conexion();
        $this->conn = $conexion->Conectar();

        // El MasterModel no necesita pasar conexión si crea la suya propia,
        // pero si tu versión la recibe, esto lo mantiene compatible:
        $this->masterModel = new MasterModel();
    }

    // =========================================================
    // OBTENER PERSONA POR ID (con datos relacionados)
    // =========================================================
    public function obtenerPorId($idPersona)
    {
        try {
            $sql = "SELECT 
                        p.id_persona,
                        p.nombre_persona,
                        p.apellido_persona,
                        p.fecha_nac_persona,
                        p.id_genero,
                        p.id_domicilio,
                        p.id_detalle_documento,
                        p.id_detalle_contacto
                    FROM persona p
                    WHERE p.id_persona = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $idPersona, PDO::PARAM_INT);
            $stmt->execute();
            $persona = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$persona) return null;

            // Cargar relaciones usando MasterModel
            $persona["genero"]    = $this->masterModel->obtenerGeneroPorId($persona["id_genero"]);
            $persona["domicilio"] = $this->masterModel->obtenerDomicilioPorId($persona["id_domicilio"]);
            $persona["documento"] = $this->masterModel->obtenerDocumentoPorId($persona["id_detalle_documento"]);
            $persona["contacto"]  = $this->masterModel->obtenerContactoPorId($persona["id_detalle_contacto"]);

            return $persona;

        } catch (PDOException $e) {
            error_log("Error en PersonaModel::obtenerPorId -> " . $e->getMessage());
            return null;
        }
    }

    // =========================================================
    // ACTUALIZAR DATOS PERSONALES
    // =========================================================
    public function actualizarDatos($id, $nombre, $apellido, $fecha, $idGenero)
    {
        try {
            $sql = "UPDATE persona 
                    SET nombre_persona = :nombre,
                        apellido_persona = :apellido,
                        fecha_nac_persona = :fecha,
                        id_genero = :genero
                    WHERE id_persona = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellido", $apellido);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":genero", $idGenero, PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en PersonaModel::actualizarDatos -> " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // ACTUALIZAR DOMICILIO
    // =========================================================
    public function actualizarDomicilio($idDomicilio, $calle, $numero, $piso, $info, $idBarrio)
    {
        try {
            $sql = "UPDATE domicilio 
                    SET calle_direccion = :calle,
                        numero_direccion = :numero,
                        piso_direccion = :piso,
                        info_extra_direccion = :info,
                        id_barrio = :barrio
                    WHERE id_domicilio = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":calle", $calle);
            $stmt->bindParam(":numero", $numero);
            $stmt->bindParam(":piso", $piso);
            $stmt->bindParam(":info", $info);
            $stmt->bindParam(":barrio", $idBarrio, PDO::PARAM_INT);
            $stmt->bindParam(":id", $idDomicilio, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en PersonaModel::actualizarDomicilio -> " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // ACTUALIZAR CONTACTO (teléfono o email)
    // =========================================================
    public function actualizarContacto($idContacto, $descripcion, $idTipoContacto)
    {
        try {
            $sql = "UPDATE detalle_contacto
                    SET descripcion_contacto = :descripcion,
                        id_tipo_contacto = :tipo
                    WHERE id_detalle_contacto = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":tipo", $idTipoContacto, PDO::PARAM_INT);
            $stmt->bindParam(":id", $idContacto, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en PersonaModel::actualizarContacto -> " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // OBTENER PERSONA COMPLETA (con todo el árbol de relaciones)
    // =========================================================
    public function obtenerPersonaCompleta($idPersona)
    {
        try {
            $sql = "SELECT 
                        p.id_persona,
                        p.nombre_persona,
                        p.apellido_persona,
                        p.fecha_nac_persona,
                        g.nombre_genero,
                        d.calle_direccion,
                        d.numero_direccion,
                        d.piso_direccion,
                        d.info_extra_direccion,
                        b.nombre_barrio,
                        l.nombre_localidad,
                        pr.nombre_provincia,
                        pa.nombre_pais
                    FROM persona p
                    LEFT JOIN genero g ON p.id_genero = g.id_genero
                    LEFT JOIN domicilio d ON p.id_domicilio = d.id_domicilio
                    LEFT JOIN barrio b ON d.id_barrio = b.id_barrio
                    LEFT JOIN localidad l ON b.id_localidad = l.id_localidad
                    LEFT JOIN provincia pr ON l.id_provincia = pr.id_provincia
                    LEFT JOIN pais pa ON pr.id_pais = pa.id_pais
                    WHERE p.id_persona = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $idPersona, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en PersonaModel::obtenerPersonaCompleta -> " . $e->getMessage());
            return null;
        }
    }
}
?>
