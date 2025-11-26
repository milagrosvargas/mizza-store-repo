<?php
require_once 'Conexion.php';

class EnvioModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =============================================================================
    // 1) SELECCIÓN TEMPORAL - Antes de crear pedido (guardado en sesión)
    // =============================================================================

    public function guardarSeleccionTemporal(string $tipoEnvio, ?int $idDomicilio = null): void
    {
        require_once 'core/Sesion.php';
        Sesion::iniciar();

        $_SESSION['envio_seleccion'] = [
            'tipo_envio'   => $tipoEnvio,
            'id_domicilio' => $idDomicilio,
            'estado'       => 'pendiente',
            'timestamp'    => time()
        ];
    }

    public function obtenerSeleccionTemporal(): ?array
    {
        require_once 'core/Sesion.php';
        Sesion::iniciar();
        return $_SESSION['envio_seleccion'] ?? null;
    }

    public function limpiarSeleccionTemporal(): void
    {
        require_once 'core/Sesion.php';
        Sesion::iniciar();
        unset($_SESSION['envio_seleccion']);
    }

    // =============================================================================
    // 2) CREACIÓN DE ENVÍO FINAL - Después de crear el pedido
    // =============================================================================

    public function crearEnvioFinal(int $idPedido, ?int $idDomicilio = null): int
    {
        try {
            $sql = "INSERT INTO envio (id_pedido, id_domicilio, estado, fecha_envio)
                    VALUES (:id_pedido, :id_domicilio, 'pendiente', NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);

            if ($idDomicilio !== null) {
                $stmt->bindParam(':id_domicilio', $idDomicilio, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':id_domicilio', null, PDO::PARAM_NULL);
            }

            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creando envío final: " . $e->getMessage());
            throw new Exception("No se pudo registrar el envío.");
        }
    }

    // =============================================================================
    // 3) OBTENER DOMICILIO COMPLETO POR USUARIO
    // =============================================================================

    public function obtenerDomicilioPorUsuario(int $idUsuario): ?array
    {
        try {
            $sql = "SELECT 
                        d.id_domicilio,
                        d.calle_direccion,
                        d.numero_direccion,
                        d.piso_direccion,
                        d.info_extra_direccion,
                        b.nombre_barrio,
                        l.nombre_localidad,
                        p.nombre_provincia,
                        pa.nombre_pais,
                        CONCAT(
                            d.calle_direccion, ' ',
                            COALESCE(d.numero_direccion, ''),
                            CASE WHEN d.piso_direccion <> '' THEN CONCAT(', Piso ', d.piso_direccion) ELSE '' END,
                            CASE WHEN d.info_extra_direccion <> '' THEN CONCAT(' (', d.info_extra_direccion, ')') ELSE '' END
                        ) AS direccion_completa
                    FROM usuario u
                    INNER JOIN persona per ON per.id_persona = u.relacion_persona
                    INNER JOIN domicilio d ON d.id_domicilio = per.id_domicilio
                    INNER JOIN barrio b ON b.id_barrio = d.id_barrio
                    INNER JOIN localidad l ON l.id_localidad = b.id_localidad
                    INNER JOIN provincia p ON p.id_provincia = l.id_provincia
                    INNER JOIN pais pa ON pa.id_pais = p.id_pais
                    WHERE u.id_usuario = :id_usuario
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $dom = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dom) {
                $dom['texto_formateado'] = sprintf(
                    "%s, %s, %s, %s, %s",
                    trim($dom['direccion_completa']),
                    $dom['nombre_barrio'],
                    $dom['nombre_localidad'],
                    $dom['nombre_provincia'],
                    $dom['nombre_pais']
                );
            }

            return $dom ?: null;
        } catch (PDOException $e) {
            error_log("Error obteniendo domicilio: " . $e->getMessage());
            return null;
        }
    }

    // =============================================================================
    // 4) ACTUALIZAR ESTADO DE ENVÍO
    // =============================================================================

    public function actualizarEstadoEnvio(int $idEnvio, string $nuevoEstado): bool
    {
        try {
            $estadosValidos = ['pendiente', 'en camino', 'entregado'];
            if (!in_array($nuevoEstado, $estadosValidos)) {
                throw new Exception("Estado no válido.");
            }

            $sql = "UPDATE envio SET estado = :estado";

            if ($nuevoEstado === 'en camino') {
                $sql .= ", fecha_envio = NOW()";
            }
            if ($nuevoEstado === 'entregado') {
                $sql .= ", fecha_entrega = NOW()";
            }

            $sql .= " WHERE id_envio = :idEnvio";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
            $stmt->bindParam(':idEnvio', $idEnvio, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar envío: " . $e->getMessage());
        }
    }

    // =============================================================================
    // 5) ESTADÍSTICAS DE ENVÍOS
    // =============================================================================

    public function obtenerEstadisticasEnvios(): array
    {
        try {
            $sql = "SELECT estado, COUNT(*) AS total
                    FROM envio
                    GROUP BY estado";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $resultados = ['pendiente' => 0, 'en camino' => 0, 'entregado' => 0];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $estado = strtolower($row['estado']);
                if (isset($resultados[$estado])) {
                    $resultados[$estado] = (int)$row['total'];
                }
            }

            return $resultados;
        } catch (PDOException $e) {
            return [];
        }
    }

    // =============================================================================
    // 6) LISTADO DE ENVÍOS (Panel empleado / seguimiento)
    // =============================================================================
    // =============================================================================
    // 6) LISTADO DE ENVÍOS (Panel empleado / seguimiento) - CORREGIDO
    // =============================================================================

    public function obtenerPedidosConEnvio(): array
    {
        try {
            $sql = "SELECT 
                    p.id_pedido,
                    e.id_envio,
                    p.monto_total,
                    per.nombre_persona AS nombre_cliente,
                    per.apellido_persona AS apellido_cliente,
                    est_pedido.nombre_estado AS estado_pedido,
                    e.estado AS estado_envio,
                    DATE_FORMAT(e.fecha_envio, '%d/%m/%Y') AS fecha_envio,
                    DATE_FORMAT(e.fecha_entrega, '%d/%m/%Y') AS fecha_entrega,
                    p.id_repartidor,
                    per_rep.nombre_persona AS nombre_repartidor,
                    per_rep.apellido_persona AS apellido_repartidor
                FROM pedido p
                LEFT JOIN envio e 
                    ON e.id_pedido = p.id_pedido
                LEFT JOIN repartidor rep 
                    ON rep.id_repartidor = p.id_repartidor
                LEFT JOIN persona per_rep 
                    ON per_rep.id_persona = rep.relacion_persona
                INNER JOIN cliente c 
                    ON c.id_cliente = p.id_cliente
                INNER JOIN persona per 
                    ON per.id_persona = c.relacion_persona
                INNER JOIN estado_logico est_pedido 
                    ON est_pedido.id_estado_logico = p.id_estado_logico
                ORDER BY p.id_pedido DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Podés loguear si querés:
            // error_log("Error obteniendo pedidos con envío: " . $e->getMessage());
            return [];
        }
    }


    // =============================================================================
    // 7) REPARTIDORES ACTIVOS
    // =============================================================================

    public function obtenerRepartidoresActivos(): array
    {
        try {
            $sql = "SELECT r.id_repartidor, p.nombre_persona, p.apellido_persona
                    FROM repartidor r
                    INNER JOIN persona p ON p.id_persona = r.relacion_persona
                    WHERE r.estado_repartidor = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    // =============================================================================
    // 8) ASIGNAR REPARTIDOR A ENVÍO EXISTENTE (CORREGIDO)
    // =============================================================================

    public function asignarRepartidorExistente(int $idPedido, int $idRepartidor): bool
    {
        try {
            // 1️⃣ Validar si existe un envío relacionado al pedido
            $sqlCheck = "SELECT id_envio FROM envio WHERE id_pedido = :id_pedido LIMIT 1";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmtCheck->execute();
            $envio = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$envio) {
                throw new Exception("No existe un envío para este pedido.");
            }

            // 2️⃣ Actualizar el repartidor en la tabla PEDIDO
            $sqlPedido = "UPDATE pedido
                      SET id_repartidor = :id_repartidor
                      WHERE id_pedido = :id_pedido";

            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->bindParam(':id_repartidor', $idRepartidor, PDO::PARAM_INT);
            $stmtPedido->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmtPedido->execute();

            // 3️⃣ Actualizar el estado del envío a "en camino" (si aún no lo está)
            $sqlEnvio = "UPDATE envio
                     SET estado = 'en camino',
                         fecha_envio = IFNULL(fecha_envio, NOW())
                     WHERE id_envio = :id_envio";

            $stmtEnvio = $this->db->prepare($sqlEnvio);
            $stmtEnvio->bindParam(':id_envio', $envio['id_envio'], PDO::PARAM_INT);

            return $stmtEnvio->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al asignar repartidor: " . $e->getMessage());
        }
    }


    // =============================================================================
    // 9) VERIFICAR EXISTENCIA DE ENVÍO
    // =============================================================================

    public function existeEnvio(int $idEnvio): bool
    {
        $sql = "SELECT 1 FROM envio WHERE id_envio = :idEnvio LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idEnvio', $idEnvio, PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }
    public function marcarComoEntregadoAdmin(int $idPedido)
{
    $sql1 = "UPDATE envio SET estado = 'entregado', fecha_entrega = NOW() 
             WHERE id_pedido = :id";

    $sql2 = "UPDATE pedido SET id_estado_logico = 6 
             WHERE id_pedido = :id";

    $stmt1 = $this->db->prepare($sql1);
    $stmt1->bindParam(':id', $idPedido, PDO::PARAM_INT);
    $stmt1->execute();

    $stmt2 = $this->db->prepare($sql2);
    $stmt2->bindParam(':id', $idPedido, PDO::PARAM_INT);
    $stmt2->execute();
}

}
