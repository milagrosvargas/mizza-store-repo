<?php

class HistorialModel
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
    }

    /**
     * Registrar automáticamente un pedido en el historial
     * (Se ejecuta después de generar un pedido o pago exitoso)
     */
    public function registrarPorPedido($idPedido)
    {
        try {
            $sql = "INSERT INTO historial_compra (id_cliente, id_pedido)
                    SELECT p.id_cliente, p.id_pedido
                    FROM pedido p
                    WHERE p.id_pedido = :pedido
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pedido', $idPedido, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener historial sin filtros (backup o uso interno)
    public function obtenerPorCliente($idCliente)
    {
        try {
            $sql = "SELECT hc.*, p.monto_total, p.fecha_pedido, p.id_estado_logico
                    FROM historial_compra hc
                    INNER JOIN pedido p ON hc.id_pedido = p.id_pedido
                    WHERE hc.id_cliente = :cliente
                    ORDER BY hc.fecha_registro DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Obtener un historial puntual
    public function obtenerPorPedido($idPedido)
    {
        try {
            $sql = "SELECT * FROM historial_compra
                    WHERE id_pedido = :pedido
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Método principal para historial del cliente (vista principal con filtros)
     * Devuelve registros paginados, con unión a todos los datos necesarios.
     */
    public function obtenerHistorialFiltrado($idCliente, $filtros, $inicio, $registrosPorPagina)
    {
        $sql = "SELECT 
                    hc.id_historial,
                    hc.fecha_registro,
                    p.id_pedido,
                    p.fecha_pedido,
                    p.monto_total,
                    el.nombre_estado AS estado_pedido,
                    pg.estado_pago,
                    mp.nombre_metodo_pago,
                    e.estado AS estado_envio,
                    CONCAT(d.calle_direccion, ' ', d.numero_direccion, 
                           IF(d.piso_direccion IS NOT NULL, CONCAT(', Piso ', d.piso_direccion), ''),
                           IF(d.info_extra_direccion IS NOT NULL, CONCAT(' (', d.info_extra_direccion, ')'), '')
                    ) AS domicilio,
                    GROUP_CONCAT(pr.nombre_producto SEPARATOR ', ') AS productos
                FROM historial_compra hc
                INNER JOIN pedido p ON hc.id_pedido = p.id_pedido
                LEFT JOIN estado_logico el ON p.id_estado_logico = el.id_estado_logico
                LEFT JOIN pago pg ON p.id_pedido = pg.id_pedido
                LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
                LEFT JOIN envio e ON p.id_pedido = e.id_pedido
                LEFT JOIN domicilio d ON e.id_domicilio = d.id_domicilio
                INNER JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                INNER JOIN producto pr ON dp.id_producto = pr.id_producto
                WHERE hc.id_cliente = :idCliente";

        $params = [':idCliente' => $idCliente];

        // Filtros dinámicos
        if (!empty($filtros['fechaInicio'])) {
            $sql .= " AND DATE(p.fecha_pedido) >= :fechaInicio";
            $params[':fechaInicio'] = $filtros['fechaInicio'];
        }

        if (!empty($filtros['fechaFin'])) {
            $sql .= " AND DATE(p.fecha_pedido) <= :fechaFin";
            $params[':fechaFin'] = $filtros['fechaFin'];
        }

        if (!empty($filtros['estadoPedido'])) {
            $sql .= " AND p.id_estado_logico = :estadoPedido"; // Corrección
            $params[':estadoPedido'] = $filtros['estadoPedido'];
        }

        if (!empty($filtros['estadoPago'])) {
            $sql .= " AND pg.estado_pago = :estadoPago";
            $params[':estadoPago'] = $filtros['estadoPago'];
        }

        if (!empty($filtros['metodoPago'])) {
            $sql .= " AND pg.id_metodo_pago = :metodoPago";
            $params[':metodoPago'] = $filtros['metodoPago'];
        }

        if (!empty($filtros['montoMin'])) {
            $sql .= " AND p.monto_total >= :montoMin";
            $params[':montoMin'] = $filtros['montoMin'];
        }

        if (!empty($filtros['montoMax'])) {
            $sql .= " AND p.monto_total <= :montoMax";
            $params[':montoMax'] = $filtros['montoMax'];
        }

        if (!empty($filtros['buscarPedido'])) {
            $sql .= " AND p.id_pedido LIKE :buscarPedido";
            $params[':buscarPedido'] = '%' . $filtros['buscarPedido'] . '%';
        }

        // Orden y límite
        $sql .= " GROUP BY p.id_pedido
                  ORDER BY p.fecha_pedido DESC
                  LIMIT :inicio, :registros";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':registros', (int)$registrosPorPagina, PDO::PARAM_INT);

        foreach ($params as $clave => $valor) {
            if ($clave === ':inicio' || $clave === ':registros') continue;
            $stmt->bindValue($clave, $valor);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function contarHistorialFiltrado($idCliente, $filtros)
    {
        $sql = "SELECT COUNT(DISTINCT p.id_pedido) AS total
                FROM historial_compra hc
                INNER JOIN pedido p ON hc.id_pedido = p.id_pedido
                LEFT JOIN pago pg ON p.id_pedido = pg.id_pedido
                WHERE hc.id_cliente = :idCliente";

        $params = [':idCliente' => $idCliente];

        // Mismo sistema de filtros
        if (!empty($filtros['fechaInicio'])) {
            $sql .= " AND DATE(p.fecha_pedido) >= :fechaInicio";
            $params[':fechaInicio'] = $filtros['fechaInicio'];
        }

        if (!empty($filtros['fechaFin'])) {
            $sql .= " AND DATE(p.fecha_pedido) <= :fechaFin";
            $params[':fechaFin'] = $filtros['fechaFin'];
        }

        if (!empty($filtros['estadoPedido'])) {
            $sql .= " AND p.id_estado_logico = :estadoPedido"; // Corrección
            $params[':estadoPedido'] = $filtros['estadoPedido'];
        }

        if (!empty($filtros['estadoPago'])) {
            $sql .= " AND pg.estado_pago = :estadoPago";
            $params[':estadoPago'] = $filtros['estadoPago'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Obtener todos los datos del pedido para el detalle (unico pedido)
     */
    public function obtenerDetallePedido($idPedido, $idCliente)
    {
        // 1. Datos generales del pedido
        $sqlPedido = "SELECT 
                        p.id_pedido,
                        p.fecha_pedido,
                        p.monto_total,
                        el.nombre_estado AS estado_pedido,
                        pg.estado_pago,
                        mp.nombre_metodo_pago,
                        e.estado AS estado_envio,
                        e.fecha_envio,
                        e.fecha_entrega
                    FROM pedido p
                    INNER JOIN historial_compra hc ON p.id_pedido = hc.id_pedido
                    LEFT JOIN estado_logico el ON p.id_estado_logico = el.id_estado_logico
                    LEFT JOIN pago pg ON p.id_pedido = pg.id_pedido
                    LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
                    LEFT JOIN envio e ON p.id_pedido = e.id_pedido
                    WHERE p.id_pedido = :pedido AND hc.id_cliente = :cliente
                    LIMIT 1";

        $stmtPedido = $this->db->prepare($sqlPedido);
        $stmtPedido->execute([
            ':pedido' => $idPedido,
            ':cliente' => $idCliente
        ]);
        $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            return null;
        }

        // 2. Productos del pedido
        $sqlProductos = "SELECT 
                            dp.id_producto,
                            pr.nombre_producto,
                            dp.cantidad_producto,
                            dp.precio_unitario,
                            (dp.cantidad_producto * dp.precio_unitario) AS subtotal,
                            pr.imagen_producto
                        FROM detalle_pedido dp
                        INNER JOIN producto pr ON dp.id_producto = pr.id_producto
                        WHERE dp.id_pedido = :pedido";

        $stmtProd = $this->db->prepare($sqlProductos);
        $stmtProd->execute([':pedido' => $idPedido]);
        $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

        // 3. Dirección de envío
        $sqlEnvio = "SELECT 
                        d.calle_direccion,
                        d.numero_direccion,
                        d.piso_direccion,
                        d.info_extra_direccion,
                        ba.nombre_barrio,
                        lo.nombre_localidad,
                        pr.nombre_provincia,
                        pa.nombre_pais
                    FROM envio e
                    INNER JOIN domicilio d ON e.id_domicilio = d.id_domicilio
                    INNER JOIN barrio ba ON d.id_barrio = ba.id_barrio
                    INNER JOIN localidad lo ON ba.id_localidad = lo.id_localidad
                    INNER JOIN provincia pr ON lo.id_provincia = pr.id_provincia
                    INNER JOIN pais pa ON pr.id_pais = pa.id_pais
                    WHERE e.id_pedido = :pedido
                    LIMIT 1";

        $stmtEnvio = $this->db->prepare($sqlEnvio);
        $stmtEnvio->execute([':pedido' => $idPedido]);
        $envio = $stmtEnvio->fetch(PDO::FETCH_ASSOC);

        return [
            'pedido' => $pedido,
            'productos' => $productos,
            'envio' => $envio
        ];
    }

    // Obtener id_cliente desde la tabla cliente usando relacion_persona (id_persona)
    public function obtenerIdClientePorRelacion($idPersona)
    {
        $sql = "SELECT id_cliente FROM cliente WHERE relacion_persona = :persona LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':persona', $idPersona, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }
}
