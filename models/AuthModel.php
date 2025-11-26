<?php
require_once 'Conexion.php';

class AuthModel
{
    /**
     * Conexión a la base de datos (PDO)
     * Protected → accesible desde controladores o clases hijas.
     */
    protected $db;

    public function __construct()
    {
        $this->db = (new Conexion())->Conectar();
    }

    // =========================================================
    // 🔐 LOGIN Y VERIFICACIÓN DE CREDENCIALES
    // =========================================================

    /**
     * Obtiene datos del usuario por nombre de usuario (para login directo).
     */
    public function obtenerUsuarioParaLogin(string $nombreUsuario)
    {
        $sql = "
            SELECT 
                id_usuario, 
                nombre_usuario, 
                password_usuario, 
                estado_usuario
            FROM usuario
            WHERE nombre_usuario = :nombre_usuario 
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre_usuario', $nombreUsuario, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica credenciales por nombre de usuario o correo electrónico.
     */
    public function verificarCredenciales(string $usuarioOEmail)
    {
        $sql = "
            SELECT 
                u.id_usuario, 
                u.nombre_usuario, 
                u.password_usuario, 
                u.estado_usuario,
                u.cuenta_activada,
                u.relacion_persona,
                u.relacion_perfil,
                p.descripcion_perfil AS descripcion_perfil
            FROM usuario AS u
            INNER JOIN perfil AS p 
                ON u.relacion_perfil = p.id_perfil
            INNER JOIN persona AS per 
                ON u.relacion_persona = per.id_persona
            INNER JOIN detalle_contacto AS dc
                ON per.id_detalle_contacto = dc.id_detalle_contacto
            WHERE 
                (
                    u.nombre_usuario = :usuario
                    OR (
                        dc.descripcion_contacto = :usuario
                        AND dc.id_tipo_contacto = 1  -- 1 = correo electrónico
                    )
                )
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario', $usuarioOEmail, PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    // =========================================================
    // 🔁 RECUPERACIÓN DE CONTRASEÑA
    // =========================================================

    /**
     * Busca un usuario por correo electrónico.
     */
    public function obtenerUsuarioPorCorreo(string $correo)
    {
        $sql = "
            SELECT 
                u.id_usuario, 
                u.nombre_usuario, 
                u.estado_usuario, 
                u.cuenta_activada
            FROM usuario AS u
            INNER JOIN persona AS p ON u.relacion_persona = p.id_persona
            INNER JOIN detalle_contacto AS dc ON p.id_detalle_contacto = dc.id_detalle_contacto
            WHERE dc.descripcion_contacto = :correo
              AND dc.id_tipo_contacto = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserta un token de recuperación.
     */
    public function guardarTokenRecuperacion(int $idUsuario, string $token, string $expiracion): bool
    {
        $sql = "
            INSERT INTO tokens_usuario (relacion_usuario, token, tipo, expiracion, usado)
            VALUES (:usuario, :token, 'recuperacion', :expira, 0)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expira', $expiracion, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Valida un token de recuperación.
     * Retorna array con estado (válido/motivo) y datos del usuario si corresponde.
     */
    public function validarTokenRecuperacion(string $token)
    {
        $sql = "
            SELECT 
                t.id_token,
                t.relacion_usuario,
                t.expiracion,
                t.usado,
                u.nombre_usuario
            FROM tokens_usuario AS t
            INNER JOIN usuario AS u 
                ON t.relacion_usuario = u.id_usuario
            WHERE t.token = :token
              AND t.tipo = 'recuperacion'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            return ['valido' => false, 'motivo' => 'El enlace de recuperación no es válido.'];
        }

        if ((int)$registro['usado'] === 1) {
            return ['valido' => false, 'motivo' => 'Este enlace ya fue utilizado.'];
        }

        if (strtotime($registro['expiracion']) < time()) {
            return ['valido' => false, 'motivo' => 'El enlace de recuperación ha expirado.'];
        }

        return [
            'valido'         => true,
            'id_usuario'     => $registro['relacion_usuario'],
            'nombre_usuario' => $registro['nombre_usuario']
        ];
    }

    /**
     * Actualiza la contraseña de un usuario.
     */
    public function actualizarContrasena(int $idUsuario, string $nuevaPassword): bool
    {
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT);

        $sql = "UPDATE usuario SET password_usuario = :pass WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pass', $hash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Marca un token de recuperación como usado.
     */
    public function marcarTokenUsado(string $token): bool
    {
        $sql = "UPDATE tokens_usuario SET usado = 1 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
