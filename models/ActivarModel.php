<?php
require_once 'Conexion.php';

class ActivacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conexion())->Conectar();
    }

    public function obtenerToken($token)
    {
        $sql = "SELECT relacion_usuario, expiracion, usado
                FROM tokens_usuario
                WHERE token = :token AND tipo = 'activacion'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function activarCuenta($idUsuario)
    {
        $sql = "UPDATE usuario SET cuenta_activada = 1 WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idUsuario]);
    }

    public function marcarTokenUsado($token)
    {
        $sql = "UPDATE tokens_usuario SET usado = 1 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }
}
