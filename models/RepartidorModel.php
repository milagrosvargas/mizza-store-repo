<?php
require_once 'models/Conexion.php';

class RepartidorModel
{
    private $db;

    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $conexion = new Conexion();
            $this->db = $conexion->Conectar();
        }
    }

    public function obtenerPedidosAsignados(int $idRepartidor): array
    {
        try {
            $sql = "SELECT 
                        p.id_pedido,
                        e.id_envio,
                        e.estado AS estado_envio,
                        DATE_FORMAT(e.fecha_envio, '%d/%m/%Y %H:%i') AS fecha_envio,
                        DATE_FORMAT(e.fecha_entrega, '%d/%m/%Y %H:%i') AS fecha_entrega,
                        p.monto_total,
                        p.fecha_pedido,
                        d.calle_direccion,
                        d.numero_direccion,
                        d.piso_direccion,
                        d.info_extra_direccion,
                        b.nombre_barrio,
                        l.nombre_localidad,
                        pr.nombre_provincia,
                        pa.nombre_pais,
                        CONCAT(
                            d.calle_direccion, ' ',
                            COALESCE(d.numero_direccion, ''),
                            CASE WHEN d.piso_direccion <> '' THEN CONCAT(', Piso ', d.piso_direccion) ELSE '' END,
                            CASE WHEN d.info_extra_direccion <> '' THEN CONCAT(' (', d.info_extra_direccion, ')') ELSE '' END,
                            ', ', b.nombre_barrio,
                            ', ', l.nombre_localidad,
                            ', ', pr.nombre_provincia,
                            ', ', pa.nombre_pais
                        ) AS direccion_completa
                    FROM pedido p
                    INNER JOIN envio e ON e.id_pedido = p.id_pedido
                    INNER JOIN cliente c ON c.id_cliente = p.id_cliente
                    INNER JOIN persona per ON per.id_persona = c.relacion_persona
                    INNER JOIN domicilio d ON d.id_domicilio = per.id_domicilio
                    INNER JOIN barrio b ON b.id_barrio = d.id_barrio
                    INNER JOIN localidad l ON l.id_localidad = b.id_localidad
                    INNER JOIN provincia pr ON pr.id_provincia = l.id_provincia
                    INNER JOIN pais pa ON pa.id_pais = pr.id_pais
                    WHERE p.id_repartidor = :id_repartidor
                    ORDER BY 
                        CASE e.estado
                            WHEN 'pendiente' THEN 1
                            WHEN 'en camino' THEN 2
                            WHEN 'entregado' THEN 3
                            ELSE 4
                        END,
                        e.fecha_envio DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_repartidor', $idRepartidor, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Pedido entregado pero pendiente de confirmación desde el administrador
    public function marcarPedidoEntregado(int $idPedido, int $idRepartidor): bool
    {
        try {
            $sql = "UPDATE envio e
                INNER JOIN pedido p ON p.id_pedido = e.id_pedido
                SET 
                    e.estado = 'entregado',
                    e.fecha_entrega = NOW(),
                    p.id_estado_logico = 6
                WHERE e.id_pedido = :idPedido
                  AND p.id_repartidor = :idRepartidor
                  AND e.estado != 'entregado'";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':idRepartidor', $idRepartidor, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar estado de entrega: " . $e->getMessage());
        }
    }
}