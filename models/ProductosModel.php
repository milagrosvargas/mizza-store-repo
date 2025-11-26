<?php
require_once 'models/Conexion.php';

class ProductosModel
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->Conectar();
    }

    # Consultas para obtener productos, para opción de filtrar por varias opciones

    public function obtenerProductos(): array
    {
        $sql = "SELECT 
                p.id_producto,
                p.codigo_barras,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_compra,
                p.precio_venta,
                p.stock_minimo,
                p.stock_actual,
                p.imagen_producto,
                p.id_categoria,
                p.id_sub_categoria,
                p.id_marca,
                p.id_unidad_medida,
                p.id_estado_logico,
                e.nombre_estado AS nombre_estado_logico,
                c.nombre_categoria,
                s.nombre_sub_categoria,
                m.nombre_marca,
                u.nombre_unidad_medida
            FROM producto p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN sub_categoria s ON p.id_sub_categoria = s.id_sub_categoria
            LEFT JOIN marca m ON p.id_marca = m.id_marca
            LEFT JOIN unidad_medida u ON p.id_unidad_medida = u.id_unidad_medida
            LEFT JOIN estado_logico e ON p.id_estado_logico = e.id_estado_logico
            WHERE p.id_estado_logico IN (8, 9, 10)"; 
            // 8, 9 y 10 <- Estados lógicos: disponible, no disponible, agotado 
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProductosFiltrado(
        string $buscar,
        ?int $idCategoria,
        ?int $idSubCategoria,
        string $orden,
        int $inicio,
        int $porPagina,
        ?float $precioMin = null,
        ?float $precioMax = null
    ): array {
        // Sanitizar orden
        $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT 
                p.id_producto,
                p.codigo_barras,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_compra,
                p.precio_venta,
                p.stock_minimo,
                p.stock_actual,
                p.imagen_producto,
                p.id_categoria,
                p.id_sub_categoria,
                p.id_marca,
                p.id_unidad_medida,
                p.id_estado_logico,
                e.nombre_estado AS nombre_estado_logico,
                c.nombre_categoria,
                s.nombre_sub_categoria,
                m.nombre_marca,
                u.nombre_unidad_medida
            FROM producto p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN sub_categoria s ON p.id_sub_categoria = s.id_sub_categoria
            LEFT JOIN marca m ON p.id_marca = m.id_marca
            LEFT JOIN unidad_medida u ON p.id_unidad_medida = u.id_unidad_medida
            LEFT JOIN estado_logico e ON p.id_estado_logico = e.id_estado_logico
            WHERE p.id_estado_logico IN (8, 9, 10)";

        $params = [];

        if ($buscar !== '') {
            $sql .= " AND (
                    p.nombre_producto LIKE :buscar
                    OR p.codigo_barras LIKE :buscar
                    OR c.nombre_categoria LIKE :buscar
                    OR s.nombre_sub_categoria LIKE :buscar
                    OR m.nombre_marca LIKE :buscar
                    OR e.nombre_estado LIKE :buscar
                )";
            $params[':buscar'] = "%{$buscar}%";
        }

        if (!is_null($idCategoria)) {
            $sql .= " AND p.id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
        }

        if (!is_null($idSubCategoria)) {
            $sql .= " AND p.id_sub_categoria = :id_sub_categoria";
            $params[':id_sub_categoria'] = $idSubCategoria;
        }

        if (!is_null($precioMin)) {
            $sql .= " AND p.precio_venta >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if (!is_null($precioMax)) {
            $sql .= " AND p.precio_venta <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $sql .= " ORDER BY p.nombre_producto {$orden} LIMIT :inicio, :porPagina";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_numeric($v) ? PDO::PARAM_STR : PDO::PARAM_STR);
        }

        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProductosFiltrado(
        string $buscar,
        ?int $idCategoria,
        ?int $idSubCategoria,
        ?float $precioMin = null,
        ?float $precioMax = null
    ): int {
        $sql = "SELECT COUNT(*)
            FROM producto p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN sub_categoria s ON p.id_sub_categoria = s.id_sub_categoria
            LEFT JOIN marca m ON p.id_marca = m.id_marca
            WHERE p.id_estado_logico IN (25, 26)";

        $params = [];

        if ($buscar !== '') {
            $sql .= " AND (
        p.nombre_producto LIKE :buscar OR p.codigo_barras LIKE :buscar OR c.nombre_categoria LIKE :buscar OR s.nombre_sub_categoria LIKE :buscar OR m.nombre_marca LIKE :buscar)";
            $params[':buscar'] = "%{$buscar}%";
        }

        if (!is_null($idCategoria)) {
            $sql .= " AND p.id_categoria = :id_categoria";
            $params[':id_categoria'] = $idCategoria;
        }

        if (!is_null($idSubCategoria)) {
            $sql .= " AND p.id_sub_categoria = :id_sub_categoria";
            $params[':id_sub_categoria'] = $idSubCategoria;
        }

        if (!is_null($precioMin)) {
            $sql .= " AND p.precio_venta >= :precio_min";
            $params[':precio_min'] = $precioMin;
        }

        if (!is_null($precioMax)) {
            $sql .= " AND p.precio_venta <= :precio_max";
            $params[':precio_max'] = $precioMax;
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_numeric($v) ? PDO::PARAM_STR : PDO::PARAM_STR);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function obtenerProductoPorId(int $idProducto): ?array
    {
        $sql = "SELECT 
                    p.id_producto,
                    p.codigo_barras,
                    p.nombre_producto,
                    p.descripcion_producto,
                    p.precio_compra,
                    p.precio_venta,
                    p.stock_minimo,
                    p.stock_actual,
                    p.imagen_producto,
                    p.id_categoria,
                    p.id_sub_categoria,
                    p.id_marca,
                    p.id_unidad_medida,
                    p.id_estado_logico
                FROM producto p
                WHERE p.id_producto = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idProducto]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    # Consultas para validaciones

    public function productoExiste(string $nombre, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) FROM producto WHERE nombre_producto = ?";
        $params = [$nombre];

        if (!empty($idActual)) {
            $sql .= " AND id_producto <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function codigoBarrasExiste(string $codigo, ?int $idActual = null): bool
    {
        if (empty($codigo)) return false;

        $sql = "SELECT COUNT(*) FROM producto WHERE codigo_barras = ?";
        $params = [$codigo];

        if (!empty($idActual)) {
            $sql .= " AND id_producto <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    # Crea un producto nuevo

    public function crearProducto(array $data): bool
    {
        $sql = "INSERT INTO producto (
                    codigo_barras,
                    nombre_producto,
                    descripcion_producto,
                    precio_compra,
                    precio_venta,
                    stock_minimo,
                    stock_actual,
                    imagen_producto,
                    id_marca,
                    id_categoria,
                    id_sub_categoria,
                    id_unidad_medida,
                    id_estado_logico
                ) VALUES (
                    :codigo,
                    :nombre,
                    :descripcion,
                    :precio_compra,
                    :precio_venta,
                    :stock_minimo,
                    :stock_actual,
                    :imagen,
                    :marca,
                    :categoria,
                    :subcategoria,
                    :unidad,
                    :estado
                )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo'       => $data['codigo_barras'] ?? null,
            ':nombre'       => $data['nombre_producto'],
            ':descripcion'  => $data['descripcion_producto'] ?? null,
            ':precio_compra' => $data['precio_compra'],
            ':precio_venta' => $data['precio_venta'],
            ':stock_minimo' => $data['stock_minimo'],
            ':stock_actual' => $data['stock_actual'],
            ':imagen'       => $data['imagen_producto'] ?? null,
            ':marca'        => $data['id_marca'] ?? null,
            ':categoria'    => $data['id_categoria'] ?? null,
            ':subcategoria' => $data['id_sub_categoria'] ?? null,
            ':unidad'       => $data['id_unidad_medida'] ?? null,
            ':estado'       => $data['id_estado_logico'] ?? 1
        ]);
    }

    # Actualiza un producto que ya exista
    public function actualizarProducto(array $data): bool
    {
        $sql = "UPDATE producto SET
                    codigo_barras = :codigo,
                    nombre_producto = :nombre,
                    descripcion_producto = :descripcion,
                    precio_compra = :precio_compra,
                    precio_venta = :precio_venta,
                    stock_minimo = :stock_minimo,
                    stock_actual = :stock_actual,
                    imagen_producto = :imagen,
                    id_marca = :marca,
                    id_categoria = :categoria,
                    id_sub_categoria = :subcategoria,
                    id_unidad_medida = :unidad,
                    id_estado_logico = :estado
                WHERE id_producto = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo'       => $data['codigo_barras'] ?? null,
            ':nombre'       => $data['nombre_producto'],
            ':descripcion'  => $data['descripcion_producto'] ?? null,
            ':precio_compra' => $data['precio_compra'],
            ':precio_venta' => $data['precio_venta'],
            ':stock_minimo' => $data['stock_minimo'],
            ':stock_actual' => $data['stock_actual'],
            ':imagen'       => $data['imagen_producto'] ?? null,
            ':marca'        => $data['id_marca'] ?? null,
            ':categoria'    => $data['id_categoria'] ?? null,
            ':subcategoria' => $data['id_sub_categoria'] ?? null,
            ':unidad'       => $data['id_unidad_medida'] ?? null,
            ':estado'       => $data['id_estado_logico'] ?? 25,
            ':id'           => $data['id_producto']
        ]);
    }

    # Eliminar producto (cambio de estado lógico de "disponible" a "no disponible")
    public function eliminarProducto(int $idProducto): bool
    {
        $sql = "UPDATE producto 
                SET id_estado_logico = 9 -- // 9 <- Estado lógico: no disponible 
                WHERE id_producto = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idProducto]);
    }

    public function contarProductos(): int
    {
        $sql = "SELECT COUNT(*) FROM producto WHERE id_estado_logico IN (25, 26)";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    # Combos anidados para cargar selectores dinámicos

    public function obtenerCategoriasActivas(): array
    {
        $sql = "SELECT id_categoria, nombre_categoria 
                FROM categoria 
                WHERE id_estado_logico = 1
                ORDER BY nombre_categoria ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSubCategoriasPorCategoria(int $idCategoria): array
    {
        $sql = "SELECT id_sub_categoria, nombre_sub_categoria 
                FROM sub_categoria 
                WHERE id_categoria = ? AND id_estado_logico = 1
                ORDER BY nombre_sub_categoria ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMarcas(): array
    {
        $sql = "SELECT id_marca, nombre_marca FROM marca ORDER BY nombre_marca ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUnidadesMedida(): array
    {
        $sql = "SELECT id_unidad_medida, nombre_unidad_medida 
                FROM unidad_medida 
                ORDER BY nombre_unidad_medida ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerEstadosLogicos()
    {
        $sql = "SELECT id_estado_logico, nombre_estado FROM estado_logico ORDER BY nombre_estado ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function columnaExiste(string $tabla, string $columna): bool
    {
        try {
            $sql = "SELECT 1 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = :tabla 
            AND COLUMN_NAME = :columna
            LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tabla' => $tabla, ':columna' => $columna]);
            return (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            // Si falla el chequeo, asumimos que no existe para no romper el flujo.
            return false;
        }
    }

    // Obtiene todos los productos con nombres legibles desde las tablas relacionadas
    public function obtenerTodosConRelaciones(): array
    {
        try {
            $tieneEstadoLogico = $this->columnaExiste('producto', 'id_estado_logico');

            $sql = "
                SELECT
                    p.nombre_producto                               AS nombre_producto,
                    p.descripcion_producto                           AS descripcion,
                    c.nombre_categoria                               AS categoria,
                    sc.nombre_sub_categoria                          AS subcategoria,
                    m.nombre_marca                                   AS marca,
                    um.nombre_unidad_medida                          AS unidad_medida,
                    p.precio_compra                                   AS precio_compra,
                    p.precio_venta                                    AS precio_venta,
                    p.stock_actual                                    AS stock_actual,
                    p.stock_minimo                                    AS stock_minimo,
                    p.alta_producto                                   AS alta_producto
                FROM producto p
                INNER JOIN categoria c       ON c.id_categoria = p.id_categoria
                INNER JOIN sub_categoria sc  ON sc.id_sub_categoria = p.id_sub_categoria
                INNER JOIN marca m           ON m.id_marca = p.id_marca
                INNER JOIN unidad_medida um  ON um.id_unidad_medida = p.id_unidad_medida
                " . ($tieneEstadoLogico ? "WHERE p.id_estado_logico IN (8, 9, 10)" : "") . "
                ORDER BY p.nombre_producto ASC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data ?: [];
        } catch (Throwable $e) {
            // Propaga como excepción genérica para que el controlador maneje el error.
            throw new Exception("Error al obtener productos: " . $e->getMessage(), 0, $e);
        }
    }

    
/**
 * 🔍 Obtener stock actual de un producto (retorna entero ≥ 0)
 */
public function obtenerStockPorId($idProducto)
{
    try {
        // Usamos fetchColumn() para simplificar y evitar errores de índices
        $stmt = $this->db->prepare(
            "SELECT stock_actual 
             FROM producto 
             WHERE id_producto = :id_producto 
             LIMIT 1"
        );
        $stmt->bindValue(':id_producto', (int)$idProducto, PDO::PARAM_INT);
        $stmt->execute();

        $stock = $stmt->fetchColumn();

        // Si no hay fila o es null, devolvemos 0
        if ($stock === false || $stock === null) {
            return 0;
        }

        return (int)$stock;

    } catch (PDOException $e) {
        error_log("Error al obtener stock (id {$idProducto}): " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtiene el producto más vendido y su imagen
 */
public function obtenerProductoMasVendido(): ?array
{
    $sql = "SELECT 
                pr.id_producto,
                pr.nombre_producto,
                pr.imagen_producto,
                SUM(dp.cantidad_producto) AS total_vendido
            FROM detalle_pedido dp
            INNER JOIN producto pr ON pr.id_producto = dp.id_producto
            GROUP BY pr.id_producto
            ORDER BY total_vendido DESC
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}



    
}
