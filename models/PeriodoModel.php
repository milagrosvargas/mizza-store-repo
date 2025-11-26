<?php
require_once 'models/Conexion.php';

class PeriodoModel
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
    }

    /* ================================
       Obtener todos los períodos
    ================================== */
    public function listarPeriodos()
    {
        $sql = "SELECT * FROM periodo ORDER BY fecha_inicio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================================
       Obtener un período por ID
    ================================== */
    public function obtenerPeriodoPorId($idPeriodo)
    {
        $sql = "SELECT * FROM periodo WHERE id_periodo = :idPeriodo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ================================
       Crear período nuevo
    ================================== */
    public function crearPeriodo($data)
    {
        $sql = "INSERT INTO periodo (nombre_periodo, fecha_inicio, fecha_fin, cantidad_vendida)
                VALUES (:nombre, :inicio, :fin, NULL)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $data['nombre_periodo'], PDO::PARAM_STR);
        $stmt->bindParam(':inicio', $data['fecha_inicio'], PDO::PARAM_STR);
        $stmt->bindParam(':fin', $data['fecha_fin'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    /* ==================================================
       Listar períodos para SELECT (solo id + nombre)
    ==================================================== */
    public function listarPeriodosParaSelect()
    {
        $sql = "SELECT id_periodo, nombre_periodo 
                FROM periodo 
                ORDER BY fecha_inicio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==============================================
       Asociar un pedido a un período existente
       (tabla periodo_pedido)
    =============================================== */
    public function asociarPedidoPeriodo($idPeriodo, $idPedido)
    {
        // Evitar duplicados
        $sqlCheck = "SELECT COUNT(*) FROM periodo_pedido 
                     WHERE id_periodo = :idPeriodo AND id_pedido = :idPedido";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bindParam(':idPeriodo', $idPeriodo);
        $stmtCheck->bindParam(':idPedido', $idPedido);
        $stmtCheck->execute();

        if ($stmtCheck->fetchColumn() > 0) {
            return true; // Ya está asociado
        }

        // Insertar si no existe
        $sql = "INSERT INTO periodo_pedido (id_periodo, id_pedido) 
                VALUES (:idPeriodo, :idPedido)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo);
        $stmt->bindParam(':idPedido', $idPedido);
        return $stmt->execute();
    }

    /* ===================================================
       Estadística: Productos más vendidos por período
    ====================================================== */
    public function getProductosVendidosPorPeriodo($idPeriodo)
    {
        $sql = "
            SELECT 
                p.id_producto,
                p.nombre_producto,
                SUM(dp.cantidad_producto) AS total_vendido,
                SUM(dp.cantidad_producto * dp.precio_unitario) AS total_recaudado
            FROM periodo_pedido pp
            INNER JOIN pedido pe ON pp.id_pedido = pe.id_pedido
            INNER JOIN detalle_pedido dp ON pe.id_pedido = dp.id_pedido
            INNER JOIN producto p ON dp.id_producto = p.id_producto
            WHERE pp.id_periodo = :idPeriodo
            GROUP BY p.id_producto, p.nombre_producto
            ORDER BY total_vendido DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================================================
       Obtener el producto más vendido de un período
    ====================================================== */
    public function getProductoMasVendido($idPeriodo)
    {
        $sql = "
            SELECT 
                p.id_producto,
                p.nombre_producto,
                SUM(dp.cantidad_producto) AS total_vendido
            FROM periodo_pedido pp
            INNER JOIN pedido pe ON pp.id_pedido = pe.id_pedido
            INNER JOIN detalle_pedido dp ON pe.id_pedido = dp.id_pedido
            INNER JOIN producto p ON dp.id_producto = p.id_producto
            WHERE pp.id_periodo = :idPeriodo
            GROUP BY p.id_producto, p.nombre_producto
            ORDER BY total_vendido DESC
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /* ===================================================
   Categorías más vendidas por período
   =================================================== */
    public function getCategoriasMasVendidas($idPeriodo)
    {
        $sql = "
        SELECT 
            c.id_categoria,
            c.nombre_categoria,
            SUM(dp.cantidad_producto) AS total_vendido
        FROM periodo_pedido pp
        INNER JOIN pedido pe ON pp.id_pedido = pe.id_pedido
        INNER JOIN detalle_pedido dp ON dp.id_pedido = pe.id_pedido
        INNER JOIN producto p ON p.id_producto = dp.id_producto
        INNER JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE pp.id_periodo = :idPeriodo
        GROUP BY c.id_categoria, c.nombre_categoria
        ORDER BY total_vendido DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /* ===================================================
   Subcategorías más vendidas por período
   =================================================== */
    public function getSubcategoriasMasVendidas($idPeriodo)
    {
        $sql = "
        SELECT 
            sc.id_sub_categoria,
            sc.nombre_sub_categoria,
            SUM(dp.cantidad_producto) AS total_vendido
        FROM periodo_pedido pp
        INNER JOIN pedido pe ON pp.id_pedido = pe.id_pedido
        INNER JOIN detalle_pedido dp ON dp.id_pedido = pe.id_pedido
        INNER JOIN producto p ON p.id_producto = dp.id_producto
        INNER JOIN sub_categoria sc ON sc.id_sub_categoria = p.id_sub_categoria
        WHERE pp.id_periodo = :idPeriodo
        GROUP BY sc.id_sub_categoria, sc.nombre_sub_categoria
        ORDER BY total_vendido DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPeriodo', $idPeriodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}