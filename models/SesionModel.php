<?php
// ============================================================
// Archivo: models/SesionModel.php
// ------------------------------------------------------------
// Modelo encargado de registrar y gestionar el estado de las
// sesiones de los usuarios dentro del sistema.
// ============================================================

require_once 'Conexion.php';

class SesionModel
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
    }

    /**
     * Marca la sesión como activa para el usuario especificado.
     *
     * Si el usuario ya tiene una fila en la tabla `sesion`,
     * actualiza la existente; de lo contrario, crea una nueva.
     */
    public function marcarSesionActiva(int $idUsuario): void
    {
        $sql = "
            INSERT INTO sesion (relacion_usuario, activa_sesion, fecha_ultimo_login)
            VALUES (:id, 1, NOW())
            ON DUPLICATE KEY UPDATE 
                activa_sesion = 1,
                fecha_ultimo_login = NOW()
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);
    }

    /**
     * Marca la sesión del usuario como inactiva.
     */
    public function marcarSesionInactiva(int $idUsuario): void
    {
        $sql = "UPDATE sesion SET activa_sesion = 0 WHERE relacion_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);
    }

    /**
     * Verifica si el usuario tiene una sesión activa actualmente.
     *
     * @return bool true si está activa, false si no.
     */
    public function sesionActiva(int $idUsuario): bool
    {
        $sql = "SELECT activa_sesion FROM sesion WHERE relacion_usuario = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row && (int)$row['activa_sesion'] === 1;
    }
}
