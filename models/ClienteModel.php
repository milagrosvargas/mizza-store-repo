<?php
require_once 'Conexion.php';

class ClienteModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conexion())->Conectar();
    }

    /* ======================================================
    VALIDACIONES DE EXISTENCIA (AJAX / PREVIA AL ALTA)
    ====================================================== */

    public function existeEmail($email)
    {
        $sql = "SELECT COUNT(*) FROM detalle_contacto WHERE descripcion_contacto = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeTelefono($telefono)
    {
        $sql = "SELECT COUNT(*) FROM detalle_contacto WHERE descripcion_contacto = :tel";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tel' => $telefono]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeUsuario($usuario)
    {
        $sql = "SELECT COUNT(*) FROM usuario WHERE nombre_usuario = :u";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':u' => $usuario]);
        return $stmt->fetchColumn() > 0;
    }

    /* ======================================================
    INSERCIÓN COMPLETA DE CLIENTE + USUARIO + TOKEN
    ====================================================== */

    public function insertarCliente($data)
    {
        try {
            $this->db->beginTransaction();

            /* DOCUMENTO */
            $sqlDoc = "INSERT INTO detalle_documento (id_tipo_documento, descripcion_documento)
            VALUES (:tipo, :descripcion)";
            $stmtDoc = $this->db->prepare($sqlDoc);
            $stmtDoc->execute([
                ':tipo' => $data['tipo_documento'],
                ':descripcion' => $data['numero_documento']
            ]);
            $idDetalleDocumento = $this->db->lastInsertId();

            /* CONTACTOS */

            // Siempre usar el tipo correcto (ID real, no texto ambiguo)
            $idTipoEmail = 1; // Email principal
            $stmtEmail = $this->db->prepare("
    INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
    VALUES (:descripcion, :tipo)");
            $stmtEmail->execute([
                ':descripcion' => $data['email'],
                ':tipo' => $idTipoEmail
            ]);
            $idEmail = $this->db->lastInsertId();

            // Teléfono - tipo 2
            $idTipoTel = 2;
            $stmtTel = $this->db->prepare("
    INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
    VALUES (:descripcion, :tipo)");
            $stmtTel->execute([
                ':descripcion' => $data['telefono'],
                ':tipo' => $idTipoTel
            ]);
            $idTelefono = $this->db->lastInsertId();


            /* DOMICILIO */
            $idPais = $data['pais'];
            $idProvincia = $data['provincia'];
            $idLocalidad = $data['ciudad'];
            $idBarrio = $data['barrio'];

            $sqlDom = "INSERT INTO domicilio (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
            VALUES (:calle, :numero, '', '', :barrio)";
            $stmtDom = $this->db->prepare($sqlDom);
            $stmtDom->execute([
                ':calle' => $data['direccion'],
                ':numero' => $data['numero'],
                ':barrio' => $idBarrio
            ]);
            $idDomicilio = $this->db->lastInsertId();

            /* PERSONA */
            $sqlPersona = "INSERT INTO persona 
    (nombre_persona, apellido_persona, fecha_nac_persona, id_genero, id_domicilio, id_detalle_documento, id_detalle_contacto)
    VALUES (:nombre, :apellido, :fecha_nac, :genero, :domicilio, :documento, :contacto)";

            $stmtPer = $this->db->prepare($sqlPersona);
            $stmtPer->execute([
                ':nombre'     => $data['nombre'],
                ':apellido'   => $data['apellido'],
                ':fecha_nac'  => !empty($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null,
                ':genero'     => $data['genero'],
                ':domicilio'  => $idDomicilio,
                ':documento'  => $idDetalleDocumento,
                ':contacto'   => $idEmail
            ]);

            $idPersona = $this->db->lastInsertId();

            /* CLIENTE */
            $sqlCliente = "INSERT INTO cliente (estado_cliente, relacion_persona) VALUES (1, :idPersona)";
            $stmtCli = $this->db->prepare($sqlCliente);
            $stmtCli->execute([':idPersona' => $idPersona]);
            $idCliente = $this->db->lastInsertId();

            /* USUARIO */
            $hashPass = password_hash($data['password'], PASSWORD_BCRYPT);
            $sqlUser = "INSERT INTO usuario 
                        (nombre_usuario, password_usuario, estado_usuario, cuenta_activada, relacion_persona, relacion_perfil)
                        VALUES (:usuario, :pass, 1, 0, :persona, :perfil)";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':usuario' => $data['usuario'],
                ':pass' => $hashPass,
                ':persona' => $idPersona,
                ':perfil' => 4
            ]);
            $idUsuario = $this->db->lastInsertId();

            /* TOKEN DE ACTIVACIÓN */
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 day'));

            $sqlTok = "INSERT INTO tokens_usuario (relacion_usuario, token, tipo, expiracion, usado)
            VALUES (:idUser, :token, 'activacion', :exp, 0)";
            $stmtTok = $this->db->prepare($sqlTok);
            $stmtTok->execute([
                ':idUser' => $idUsuario,
                ':token' => $token,
                ':exp' => $expira
            ]);

            $this->db->commit();

            // Retorna datos útiles para PHPMailer
            return [
                'success' => true,
                'token' => $token,
                'email' => $data['email'],
                'usuario' => $data['usuario']
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al insertar cliente: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /* ======================================================
    MÉTODOS AUXILIARES
    ====================================================== */

    private function obtenerIdTipoContacto($nombre)
    {
        $sql = "SELECT id_tipo_contacto FROM tipo_contacto WHERE nombre_tipo_contacto = :n";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':n' => $nombre]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            $sqlIns = "INSERT INTO tipo_contacto (nombre_tipo_contacto) VALUES (:n)";
            $stmtIns = $this->db->prepare($sqlIns);
            $stmtIns->execute([':n' => $nombre]);
            $id = $this->db->lastInsertId();
        }
        return $id;
    }

    public function obtenerGeneros()
    {
        return $this->db->query("SELECT id_genero, nombre_genero FROM genero ORDER BY nombre_genero")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPaises()
    {
        return $this->db->query("SELECT id_pais, nombre_pais FROM pais ORDER BY nombre_pais")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProvincias()
    {
        return $this->db->query("SELECT id_provincia, nombre_provincia FROM provincia ORDER BY nombre_provincia")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerLocalidades()
    {
        return $this->db->query("SELECT id_localidad, nombre_localidad FROM localidad ORDER BY nombre_localidad")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerBarrios()
    {
        return $this->db->query("SELECT id_barrio, nombre_barrio FROM barrio ORDER BY nombre_barrio")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerTiposDocumento()
    {
        $sql = "SELECT id_tipo_documento, nombre_tipo_documento FROM tipo_documento ORDER BY nombre_tipo_documento";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
