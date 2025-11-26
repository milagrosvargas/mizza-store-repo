<?php
require_once 'models/Conexion.php';

class MasterModel
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * =========================================================
     * CONSTRUCTOR FLEXIBLE
     * =========================================================
     * Permite:
     *  - Inyectar una conexión existente (desde otros modelos).
     *  - O crear una nueva automáticamente si no se pasa ninguna.
     */
    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            // Reutiliza la conexión inyectada
            $this->db = $db;
        } else {
            // Crea una nueva conexión si no se pasó ninguna
            $conexion = new Conexion();
            $this->db = $conexion->Conectar();
        }
    }

    /**
     * 📄 Obtiene la lista de estados desde la BD con filtros, orden y paginación.
     */
    public function obtenerEstados($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        try {
            $inicio = ($pagina - 1) * $porPagina;
            $params = [];
            $where = '';

            // ✅ Filtro de búsqueda opcional
            if (!empty($buscar)) {
                $where = "WHERE nombre_estado LIKE :buscar";
                $params[':buscar'] = "%$buscar%";
            }

            // ✅ Validar orden (evitar SQL injection)
            $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';

            // ✅ Consulta principal
            $sql = "SELECT SQL_CALC_FOUND_ROWS 
                        id_estado_logico, 
                        nombre_estado
                    FROM estado_logico
                    $where
                    ORDER BY nombre_estado $orden
                    LIMIT :inicio, :porPagina";

            $stmt = $this->db->prepare($sql);

            // Bind de parámetros dinámicos
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
            $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);

            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ✅ Total de registros
            $total = $this->db->query("SELECT FOUND_ROWS() AS total")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            $totalPaginas = $total > 0 ? ceil($total / $porPagina) : 1;

            return [
                'datos' => $datos,
                'total_paginas' => $totalPaginas
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerEstados(): " . $e->getMessage());
            return ['datos' => [], 'total_paginas' => 1];
        }
    }

    /**
     * 🔍 Verifica si un estado existe (opcionalmente excluyendo uno por ID).
     */
    public function existeEstado($nombre, $excluirId = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM estado_logico 
                    WHERE LOWER(nombre_estado) = LOWER(:nombre)";

            if (!empty($excluirId)) {
                $sql .= " AND id_estado_logico != :id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);

            if (!empty($excluirId)) {
                $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res && $res['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeEstado(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ➕ Inserta un nuevo estado.
     */
    public function insertarEstado($nombre)
    {
        try {
            $sql = "INSERT INTO estado_logico (nombre_estado) VALUES (:nombre)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en insertarEstado(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✏️ Actualiza el nombre de un estado lógico.
     */
    public function actualizarEstado($id_estado, $nombre)
    {
        try {
            $sql = "UPDATE estado_logico 
                    SET nombre_estado = :nombre 
                    WHERE id_estado_logico = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id_estado, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarEstado(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🧩 Verifica si un estado lógico está en uso por otras tablas (integridad referencial).
     */
    public function estadoEnUso($id_estado)
    {
        try {
            $tablas = ['pedido']; // 🔸 Aquí agregás más tablas si las usan
            foreach ($tablas as $tabla) {
                $sql = "SELECT COUNT(*) AS total FROM $tabla WHERE id_estado_logico = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':id', $id_estado, PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                if ($count > 0) {
                    return true; // En uso
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en estadoEnUso(): " . $e->getMessage());
            // 🔐 Por seguridad asumimos que está en uso si hay error
            return true;
        }
    }

    /**
     * 🗑️ Elimina un estado lógico, solo si no está en uso.
     */
    public function eliminarEstado($id_estado)
    {
        try {
            $sql = "DELETE FROM estado_logico WHERE id_estado_logico = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_estado, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarEstado(): " . $e->getMessage());
            return false;
        }
    }

    /* ===================================================
SECCIÓN: PAISES
=================================================== */

    /**
     * 📋 Listado de países con búsqueda, orden y paginación
     */
    public function obtenerPaises($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        try {
            $inicio = ($pagina - 1) * $porPagina;
            $params = [];
            $where  = '';

            if (!empty($buscar)) {
                $where = "WHERE nombre_pais LIKE :buscar";
                $params[':buscar'] = "%$buscar%";
            }

            $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';

            $sql = "SELECT SQL_CALC_FOUND_ROWS id_pais, nombre_pais
                    FROM pais
                    $where
                    ORDER BY nombre_pais $orden
                    LIMIT :inicio, :porPagina";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
            $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
            $stmt->execute();

            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total = $this->db->query("SELECT FOUND_ROWS() AS total")->fetch()['total'] ?? 0;

            return [
                'datos' => $datos,
                'total_paginas' => $total > 0 ? ceil($total / $porPagina) : 1
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerPaises(): " . $e->getMessage());
            return ['datos' => [], 'total_paginas' => 1];
        }
    }

    /**
     * 🔍 Verifica si ya existe un país con ese nombre.
     */
    public function existePais($nombre, $excluirId = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM pais 
                    WHERE LOWER(nombre_pais) = LOWER(:nombre)";
            if (!empty($excluirId)) {
                $sql .= " AND id_pais != :id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            if (!empty($excluirId)) {
                $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
            }
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res && $res['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existePais(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ➕ Inserta un nuevo país.
     */
    public function insertarPais($nombre)
    {
        try {
            $sql = "INSERT INTO pais (nombre_pais) VALUES (:nombre)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en insertarPais(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✏️ Actualiza el nombre del país.
     */
    public function actualizarPais($id, $nombre)
    {
        try {
            $sql = "UPDATE pais SET nombre_pais = :nombre WHERE id_pais = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarPais(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🧩 Verifica si el país está siendo usado por alguna provincia.
     */
    public function paisEnUso($id_pais)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM provincia WHERE id_pais = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_pais, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error en paisEnUso(): " . $e->getMessage());
            return true; // Por seguridad, se asume en uso si hay error
        }
    }

    /**
     * 🗑️ Elimina un país si no está en uso.
     */
    public function eliminarPais($id)
    {
        try {
            $sql = "DELETE FROM pais WHERE id_pais = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarPais(): " . $e->getMessage());
            return false;
        }
    }

    /**
 * 🔎 Obtiene un país específico por su ID
 */
public function obtenerPaisPorId($id_pais)
{
    try {
        $sql = "SELECT id_pais, nombre_pais
                FROM pais
                WHERE id_pais = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_pais, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerPaisPorId(): " . $e->getMessage());
        return null;
    }
}

    /* ===================================================
SECCIÓN: PROVINCIAS
=================================================== */

    public function obtenerProvincias($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT pr.id_provincia, pr.nombre_provincia, pa.nombre_pais, pr.id_pais
                FROM provincia pr
                INNER JOIN pais pa ON pr.id_pais = pa.id_pais
                WHERE pr.nombre_provincia LIKE :buscar
                ORDER BY pr.nombre_provincia $orden
                LIMIT :inicio, :porPagina";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM provincia WHERE nombre_provincia LIKE '%$buscar%'")->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeProvincia($nombre, $id_pais, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM provincia WHERE nombre_provincia = ? AND id_pais = ?";
        $params = [$nombre, $id_pais];

        if ($excluirId) {
            $sql .= " AND id_provincia != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarProvincia($nombre, $id_pais)
    {
        $stmt = $this->db->prepare("INSERT INTO provincia (nombre_provincia, id_pais) VALUES (?, ?)");
        return $stmt->execute([$nombre, $id_pais]);
    }

    public function actualizarProvincia($id, $nombre, $id_pais)
    {
        $stmt = $this->db->prepare("UPDATE provincia SET nombre_provincia = ?, id_pais = ? WHERE id_provincia = ?");
        return $stmt->execute([$nombre, $id_pais, $id]);
    }

    public function eliminarProvincia($id)
    {
        $stmt = $this->db->prepare("DELETE FROM provincia WHERE id_provincia = ?");
        return $stmt->execute([$id]);
    }

    public function provinciaEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM localidad WHERE id_provincia = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerPaisesSelect()
    {
        $stmt = $this->db->query("SELECT id_pais, nombre_pais FROM pais ORDER BY nombre_pais ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function obtenerProvinciaPorId($id_provincia)
    {
        try {
            $sql = "SELECT pr.id_provincia, pr.nombre_provincia, pa.id_pais, pa.nombre_pais
                    FROM provincia pr
                    LEFT JOIN pais pa ON pr.id_pais = pa.id_pais
                    WHERE pr.id_provincia = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id_provincia, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerProvinciaPorId(): " . $e->getMessage());
            return null;
        }
    }

    /* ===================================================
SECCIÓN: LOCALIDADES
=================================================== */

    public function obtenerLocalidades($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT l.id_localidad, l.nombre_localidad, p.nombre_provincia, l.id_provincia
            FROM localidad l
            INNER JOIN provincia p ON l.id_provincia = p.id_provincia
            WHERE l.nombre_localidad LIKE :buscar
            ORDER BY l.nombre_localidad $orden
            LIMIT :inicio, :porPagina";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM localidad WHERE nombre_localidad LIKE '%$buscar%'")->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeLocalidad($nombre, $id_provincia, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM localidad WHERE nombre_localidad = ? AND id_provincia = ?";
        $params = [$nombre, $id_provincia];
        if ($excluirId) {
            $sql .= " AND id_localidad != ?";
            $params[] = $excluirId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarLocalidad($nombre, $id_provincia)
    {
        $stmt = $this->db->prepare("INSERT INTO localidad (nombre_localidad, id_provincia) VALUES (?, ?)");
        return $stmt->execute([$nombre, $id_provincia]);
    }

    public function actualizarLocalidad($id, $nombre, $id_provincia)
    {
        $stmt = $this->db->prepare("UPDATE localidad SET nombre_localidad = ?, id_provincia = ? WHERE id_localidad = ?");
        return $stmt->execute([$nombre, $id_provincia, $id]);
    }

    public function eliminarLocalidad($id)
    {
        $stmt = $this->db->prepare("DELETE FROM localidad WHERE id_localidad = ?");
        return $stmt->execute([$id]);
    }

    public function localidadEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM barrio WHERE id_localidad = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerProvinciasSelect()
    {
        $stmt = $this->db->query("SELECT id_provincia, nombre_provincia FROM provincia ORDER BY nombre_provincia ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function obtenerLocalidadPorId($id_localidad)
    {
        try {
            $sql = "SELECT l.id_localidad, l.nombre_localidad,
                           pr.id_provincia, pr.nombre_provincia,
                           pa.id_pais, pa.nombre_pais
                    FROM localidad l
                    LEFT JOIN provincia pr ON l.id_provincia = pr.id_provincia
                    LEFT JOIN pais pa ON pr.id_pais = pa.id_pais
                    WHERE l.id_localidad = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id_localidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerLocalidadPorId(): " . $e->getMessage());
            return null;
        }
    }

    /* ===================================================
SECCIÓN: BARRIOS
=================================================== */
    public function obtenerBarrios($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;

        $sql = "SELECT b.id_barrio, b.nombre_barrio, l.nombre_localidad, b.id_localidad
                FROM barrio b
                INNER JOIN localidad l ON b.id_localidad = l.id_localidad
                WHERE b.nombre_barrio LIKE :buscar
                ORDER BY b.nombre_barrio $orden
                LIMIT :inicio, :porPagina";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db
            ->query("SELECT COUNT(*) FROM barrio WHERE nombre_barrio LIKE '%$buscar%'")
            ->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeBarrio($nombre, $id_localidad, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM barrio WHERE nombre_barrio = ? AND id_localidad = ?";
        $params = [$nombre, $id_localidad];

        if ($excluirId) {
            $sql .= " AND id_barrio != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarBarrio($nombre, $id_localidad)
    {
        $stmt = $this->db->prepare("INSERT INTO barrio (nombre_barrio, id_localidad) VALUES (?, ?)");
        return $stmt->execute([$nombre, $id_localidad]);
    }

    public function actualizarBarrio($id, $nombre, $id_localidad)
    {
        $stmt = $this->db->prepare("UPDATE barrio SET nombre_barrio = ?, id_localidad = ? WHERE id_barrio = ?");
        return $stmt->execute([$nombre, $id_localidad, $id]);
    }

    public function eliminarBarrio($id)
    {
        $stmt = $this->db->prepare("DELETE FROM barrio WHERE id_barrio = ?");
        return $stmt->execute([$id]);
    }

    public function barrioEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM domicilio WHERE id_barrio = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerLocalidadesSelect()
    {
        $stmt = $this->db->query("SELECT id_localidad, nombre_localidad FROM localidad ORDER BY nombre_localidad ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerBarrioPorId($id_barrio)
    {
        try {
            $sql = "SELECT b.id_barrio, b.nombre_barrio,
                           l.id_localidad, l.nombre_localidad,
                           pr.id_provincia, pr.nombre_provincia,
                           pa.id_pais, pa.nombre_pais
                    FROM barrio b
                    LEFT JOIN localidad l ON b.id_localidad = l.id_localidad
                    LEFT JOIN provincia pr ON l.id_provincia = pr.id_provincia
                    LEFT JOIN pais pa ON pr.id_pais = pa.id_pais
                    WHERE b.id_barrio = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id_barrio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerBarrioPorId(): " . $e->getMessage());
            return null;
        }
    }

/* =========================================================
   SECCIÓN: DOMICILIOS
   ========================================================= */

/**
 * 📦 Obtiene un domicilio completo (con país, provincia, localidad y barrio)
 * por su ID.
 */
public function obtenerDomicilioPorId($id_domicilio)
{
    try {
        $sql = "SELECT 
                    d.id_domicilio,
                    d.calle_direccion,
                    d.numero_direccion,
                    d.piso_direccion,
                    d.info_extra_direccion,
                    b.id_barrio,
                    b.nombre_barrio,
                    l.id_localidad,
                    l.nombre_localidad,
                    pr.id_provincia,
                    pr.nombre_provincia,
                    p.id_pais,
                    p.nombre_pais
                FROM domicilio d
                LEFT JOIN barrio b ON d.id_barrio = b.id_barrio
                LEFT JOIN localidad l ON b.id_localidad = l.id_localidad
                LEFT JOIN provincia pr ON l.id_provincia = pr.id_provincia
                LEFT JOIN pais p ON pr.id_pais = p.id_pais
                WHERE d.id_domicilio = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_domicilio, PDO::PARAM_INT);
        $stmt->execute();
        $dom = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dom) return null;

        return [
            "id_domicilio" => $dom["id_domicilio"],
            "calle" => $dom["calle_direccion"],
            "numero" => $dom["numero_direccion"],
            "piso" => $dom["piso_direccion"],
            "info_extra" => $dom["info_extra_direccion"],
            "barrio" => [
                "id_barrio" => $dom["id_barrio"],
                "nombre_barrio" => $dom["nombre_barrio"],
                "localidad" => [
                    "id_localidad" => $dom["id_localidad"],
                    "nombre_localidad" => $dom["nombre_localidad"],
                    "provincia" => [
                        "id_provincia" => $dom["id_provincia"],
                        "nombre_provincia" => $dom["nombre_provincia"],
                        "pais" => [
                            "id_pais" => $dom["id_pais"],
                            "nombre_pais" => $dom["nombre_pais"]
                        ]
                    ]
                ]
            ]
        ];
    } catch (PDOException $e) {
        error_log("Error en obtenerDomicilioPorId(): " . $e->getMessage());
        return null;
    }
}

/**
 * ➕ Inserta un nuevo domicilio.
 */
public function insertarDomicilio($calle, $numero, $piso, $info, $id_barrio)
{
    try {
        $sql = "INSERT INTO domicilio 
                    (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
                VALUES (:calle, :numero, :piso, :info, :barrio)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calle', $calle);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':piso', $piso);
        $stmt->bindParam(':info', $info);
        $stmt->bindParam(':barrio', $id_barrio, PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error en insertarDomicilio(): " . $e->getMessage());
        return false;
    }
}

/**
 * ✏️ Actualiza un domicilio existente.
 */
public function actualizarDomicilio($id, $calle, $numero, $piso, $info, $id_barrio)
{
    try {
        $sql = "UPDATE domicilio 
                SET calle_direccion = :calle,
                    numero_direccion = :numero,
                    piso_direccion = :piso,
                    info_extra_direccion = :info,
                    id_barrio = :barrio
                WHERE id_domicilio = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calle', $calle);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':piso', $piso);
        $stmt->bindParam(':info', $info);
        $stmt->bindParam(':barrio', $id_barrio, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en actualizarDomicilio(): " . $e->getMessage());
        return false;
    }
}

/**
 * 🧩 Elimina un domicilio (si no está en uso por persona).
 */
public function eliminarDomicilio($id)
{
    try {
        // Verifica uso en persona
        $sqlCheck = "SELECT COUNT(*) AS total FROM persona WHERE id_domicilio = :id";
        $check = $this->db->prepare($sqlCheck);
        $check->bindParam(':id', $id, PDO::PARAM_INT);
        $check->execute();
        $count = $check->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        if ($count > 0) {
            return false; // No se puede eliminar si está en uso
        }

        $sql = "DELETE FROM domicilio WHERE id_domicilio = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en eliminarDomicilio(): " . $e->getMessage());
        return false;
    }
}

/* =========================================================
   SECCIÓN: COMBOS ANIDADOS DE UBICACIÓN
   ========================================================= */

/**
 * 🗺️ Provincias por país
 */
public function obtenerProvinciasPorPais($id_pais)
{
    try {
        $sql = "SELECT id_provincia, nombre_provincia 
                FROM provincia 
                WHERE id_pais = :id
                ORDER BY nombre_provincia ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_pais, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerProvinciasPorPais(): " . $e->getMessage());
        return [];
    }
}

/**
 * 🏙️ Localidades por provincia
 */
public function obtenerLocalidadesPorProvincia($id_provincia)
{
    try {
        $sql = "SELECT id_localidad, nombre_localidad 
                FROM localidad 
                WHERE id_provincia = :id
                ORDER BY nombre_localidad ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_provincia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerLocalidadesPorProvincia(): " . $e->getMessage());
        return [];
    }
}

/**
 * 🏘️ Barrios por localidad
 */
public function obtenerBarriosPorLocalidad($id_localidad)
{
    try {
        $sql = "SELECT id_barrio, nombre_barrio 
                FROM barrio 
                WHERE id_localidad = :id
                ORDER BY nombre_barrio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_localidad, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerBarriosPorLocalidad(): " . $e->getMessage());
        return [];
    }
}

    /* ===================================================
SECCIÓN: TIPO DE DOCUMENTO
=================================================== */

    public function obtenerTiposDocumento($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT id_tipo_documento, nombre_tipo_documento
            FROM tipo_documento
            WHERE nombre_tipo_documento LIKE :buscar
            ORDER BY nombre_tipo_documento $orden
            LIMIT :inicio, :porPagina";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM tipo_documento WHERE nombre_tipo_documento LIKE '%$buscar%'")->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeTipoDocumento($nombre, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM tipo_documento WHERE nombre_tipo_documento = ?";
        $params = [$nombre];

        if ($excluirId) {
            $sql .= " AND id_tipo_documento != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarTipoDocumento($nombre)
    {
        $stmt = $this->db->prepare("INSERT INTO tipo_documento (nombre_tipo_documento) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function actualizarTipoDocumento($id, $nombre)
    {
        $stmt = $this->db->prepare("UPDATE tipo_documento SET nombre_tipo_documento = ? WHERE id_tipo_documento = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public function eliminarTipoDocumento($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tipo_documento WHERE id_tipo_documento = ?");
        return $stmt->execute([$id]);
    }

    public function tipoDocumentoEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM detalle_documento WHERE id_tipo_documento = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }


/**
 * 📄 Obtiene el detalle del documento (DNI, Pasaporte, etc.)
 * junto con su tipo asociado.
 */
public function obtenerDocumentoPorId($id_detalle_documento)
{
    try {
        $sql = "SELECT 
                    dd.id_detalle_documento,
                    dd.descripcion_documento,
                    td.id_tipo_documento,
                    td.nombre_tipo_documento
                FROM detalle_documento dd
                LEFT JOIN tipo_documento td 
                    ON dd.id_tipo_documento = td.id_tipo_documento
                WHERE dd.id_detalle_documento = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_detalle_documento, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) return null;

        return [
            "id_detalle_documento" => $doc["id_detalle_documento"],
            "descripcion_documento" => $doc["descripcion_documento"],
            "tipo_documento" => [
                "id_tipo_documento" => $doc["id_tipo_documento"],
                "nombre_tipo_documento" => $doc["nombre_tipo_documento"]
            ]
        ];
    } catch (PDOException $e) {
        error_log("Error en obtenerDocumentoPorId(): " . $e->getMessage());
        return null;
    }
}

/**
 * ➕ Inserta un nuevo documento (detalle + tipo).
 */
public function insertarDocumento($descripcion, $id_tipo_documento)
{
    try {
        $sql = "INSERT INTO detalle_documento (id_tipo_documento, descripcion_documento)
                VALUES (:tipo, :desc)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tipo', $id_tipo_documento, PDO::PARAM_INT);
        $stmt->bindParam(':desc', $descripcion, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error en insertarDocumento(): " . $e->getMessage());
        return false;
    }
}

/**
 * ✏️ Actualiza el documento de identidad.
 */
public function actualizarDocumento($id_detalle_documento, $descripcion, $id_tipo_documento)
{
    try {
        $sql = "UPDATE detalle_documento 
                SET descripcion_documento = :desc,
                    id_tipo_documento = :tipo
                WHERE id_detalle_documento = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':tipo', $id_tipo_documento, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_detalle_documento, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en actualizarDocumento(): " . $e->getMessage());
        return false;
    }
}
    

    /* ===================================================
SECCIÓN: TIPO DE CONTACTO
=================================================== */

    public function obtenerTiposContacto($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT id_tipo_contacto, nombre_tipo_contacto
            FROM tipo_contacto
            WHERE nombre_tipo_contacto LIKE :buscar
            ORDER BY nombre_tipo_contacto $orden
            LIMIT :inicio, :porPagina";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM tipo_contacto WHERE nombre_tipo_contacto LIKE '%$buscar%'")->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeTipoContacto($nombre, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM tipo_contacto WHERE nombre_tipo_contacto = ?";
        $params = [$nombre];

        if ($excluirId) {
            $sql .= " AND id_tipo_contacto != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarTipoContacto($nombre)
    {
        $stmt = $this->db->prepare("INSERT INTO tipo_contacto (nombre_tipo_contacto) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function actualizarTipoContacto($id, $nombre)
    {
        $stmt = $this->db->prepare("UPDATE tipo_contacto SET nombre_tipo_contacto = ? WHERE id_tipo_contacto = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public function eliminarTipoContacto($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tipo_contacto WHERE id_tipo_contacto = ?");
        return $stmt->execute([$id]);
    }

    public function tipoContactoEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM detalle_contacto WHERE id_tipo_contacto = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

/**
 * ☎️ Obtiene el detalle del contacto (teléfono, email, etc.)
 * junto con su tipo.
 */
public function obtenerContactoPorId($id_detalle_contacto)
{
    try {
        $sql = "SELECT 
                    dc.id_detalle_contacto,
                    dc.descripcion_contacto,
                    tc.id_tipo_contacto,
                    tc.nombre_tipo_contacto
                FROM detalle_contacto dc
                LEFT JOIN tipo_contacto tc 
                    ON dc.id_tipo_contacto = tc.id_tipo_contacto
                WHERE dc.id_detalle_contacto = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_detalle_contacto, PDO::PARAM_INT);
        $stmt->execute();
        $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contacto) return null;

        return [
            "id_detalle_contacto" => $contacto["id_detalle_contacto"],
            "descripcion_contacto" => $contacto["descripcion_contacto"],
            "tipo_contacto" => [
                "id_tipo_contacto" => $contacto["id_tipo_contacto"],
                "nombre_tipo_contacto" => $contacto["nombre_tipo_contacto"]
            ]
        ];
    } catch (PDOException $e) {
        error_log("Error en obtenerContactoPorId(): " . $e->getMessage());
        return null;
    }
}

/**
 * ➕ Inserta un nuevo contacto.
 */
public function insertarContacto($descripcion, $id_tipo_contacto)
{
    try {
        $sql = "INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
                VALUES (:desc, :tipo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':tipo', $id_tipo_contacto, PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error en insertarContacto(): " . $e->getMessage());
        return false;
    }
}

/**
 * ✏️ Actualiza un contacto existente.
 */
public function actualizarContacto($id_detalle_contacto, $descripcion, $id_tipo_contacto)
{
    try {
        $sql = "UPDATE detalle_contacto 
                SET descripcion_contacto = :desc,
                    id_tipo_contacto = :tipo
                WHERE id_detalle_contacto = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->bindParam(':tipo', $id_tipo_contacto, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_detalle_contacto, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en actualizarContacto(): " . $e->getMessage());
        return false;
    }
}

    /* ===================================================
SECCIÓN: GÉNERO
=================================================== */

    public function obtenerGeneros($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT id_genero, nombre_genero
            FROM genero
            WHERE nombre_genero LIKE :buscar
            ORDER BY nombre_genero $orden
            LIMIT :inicio, :porPagina";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%");
        $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM genero WHERE nombre_genero LIKE '%$buscar%'")->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => ceil($total / $porPagina)
        ];
    }

    public function existeGenero($nombre, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM genero WHERE nombre_genero = ?";
        $params = [$nombre];

        if ($excluirId) {
            $sql .= " AND id_genero != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarGenero($nombre)
    {
        $stmt = $this->db->prepare("INSERT INTO genero (nombre_genero) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function actualizarGenero($id, $nombre)
    {
        $stmt = $this->db->prepare("UPDATE genero SET nombre_genero = ? WHERE id_genero = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public function eliminarGenero($id)
    {
        $stmt = $this->db->prepare("DELETE FROM genero WHERE id_genero = ?");
        return $stmt->execute([$id]);
    }

    public function generoEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM persona WHERE id_genero = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerGeneroPorId($id_genero)
{
    try {
        $sql = "SELECT id_genero, nombre_genero
                FROM genero
                WHERE id_genero = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_genero, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerGeneroPorId(): " . $e->getMessage());
        return null;
    }
}

    /* ===================================================
    SECCIÓN: PERFIL
    =================================================== */

    public function obtenerPerfiles($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        // 🔹 Validaciones
        $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';
        $pagina = max(1, (int)$pagina);
        $porPagina = max(1, (int)$porPagina);
        $inicio = ($pagina - 1) * $porPagina;
        $sql = "SELECT id_perfil, descripcion_perfil
            FROM perfil
            WHERE descripcion_perfil LIKE :buscar
            ORDER BY descripcion_perfil $orden
            LIMIT $inicio, $porPagina";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 Total registros
        $sqlTotal = "SELECT COUNT(*) FROM perfil WHERE descripcion_perfil LIKE :buscar";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
        $stmtTotal->execute();
        $total = (int)$stmtTotal->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => max(1, (int)ceil($total / $porPagina))
        ];
    }

    public function existePerfil($descripcion, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM perfil WHERE descripcion_perfil = ?";
        $params = [$descripcion];

        if ($excluirId) {
            $sql .= " AND id_perfil != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarPerfil($descripcion)
    {
        $stmt = $this->db->prepare("INSERT INTO perfil (descripcion_perfil) VALUES (?)");
        return $stmt->execute([$descripcion]);
    }

    public function actualizarPerfil($id, $descripcion)
    {
        $stmt = $this->db->prepare("UPDATE perfil SET descripcion_perfil = ? WHERE id_perfil = ?");
        return $stmt->execute([$descripcion, $id]);
    }

    public function eliminarPerfil($id)
    {
        $stmt = $this->db->prepare("DELETE FROM perfil WHERE id_perfil = ?");
        return $stmt->execute([$id]);
    }

    public function perfilEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM modulo_perfil WHERE relacion_perfil = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    /* ===================================================
    SECCIÓN: MÓDULO
    =================================================== */

    public function obtenerModulos($buscar = '', $orden = 'ASC', $pagina = 1, $porPagina = 10)
    {
        $orden = strtoupper($orden) === 'DESC' ? 'DESC' : 'ASC';
        $pagina = max(1, (int)$pagina);
        $porPagina = max(1, (int)$porPagina);
        $inicio = ($pagina - 1) * $porPagina;

        $sql = "SELECT id_modulo, descripcion_modulo
            FROM modulo
            WHERE descripcion_modulo LIKE :buscar
            ORDER BY descripcion_modulo $orden
            LIMIT $inicio, $porPagina";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlTotal = "SELECT COUNT(*) FROM modulo WHERE descripcion_modulo LIKE :buscar";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
        $stmtTotal->execute();
        $total = (int)$stmtTotal->fetchColumn();

        return [
            'datos' => $datos,
            'total_paginas' => max(1, (int)ceil($total / $porPagina))
        ];
    }

    public function existeModulo($descripcion, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM modulo WHERE descripcion_modulo = ?";
        $params = [$descripcion];

        if ($excluirId) {
            $sql .= " AND id_modulo != ?";
            $params[] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function insertarModulo($descripcion)
    {
        $stmt = $this->db->prepare("INSERT INTO modulo (descripcion_modulo) VALUES (?)");
        return $stmt->execute([$descripcion]);
    }

    public function actualizarModulo($id, $descripcion)
    {
        $stmt = $this->db->prepare("UPDATE modulo SET descripcion_modulo = ? WHERE id_modulo = ?");
        return $stmt->execute([$descripcion, $id]);
    }

    public function eliminarModulo($id)
    {
        $stmt = $this->db->prepare("DELETE FROM modulo WHERE id_modulo = ?");
        return $stmt->execute([$id]);
    }

    public function moduloEnUso($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM modulo_perfil WHERE relacion_modulo = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    /* ===================================================
    SECCIÓN: ACCESOS
    =================================================== */

    /**
     * Obtiene todas las relaciones entre módulos y perfiles
     * Devuelve un array de objetos con las columnas:
     * [relacion_modulo, relacion_perfil]
     */
    public function obtenerAccesos()
    {
        $sql = "SELECT relacion_modulo, relacion_perfil FROM modulo_perfil";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asigna un acceso (relación módulo ↔ perfil)
     */
    public function asignarAcceso($idModulo, $idPerfil)
    {
        $sql = "INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil)
                VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int)$idModulo, (int)$idPerfil]);
    }

    /**
     * Elimina un acceso existente
     */
    public function eliminarAcceso($idModulo, $idPerfil)
    {
        $sql = "DELETE FROM modulo_perfil
                WHERE relacion_modulo = ? AND relacion_perfil = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int)$idModulo, (int)$idPerfil]);
    }

    /**
     * Verifica si un acceso ya existe
     */
    public function accesoExiste($idModulo, $idPerfil)
    {
        $sql = "SELECT COUNT(*) FROM modulo_perfil
                WHERE relacion_modulo = ? AND relacion_perfil = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$idModulo, (int)$idPerfil]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene el nombre del perfil (para restringir al 'Invitado')
     */
    public function obtenerNombrePerfil($idPerfil)
    {
        $sql = "SELECT descripcion_perfil FROM perfil WHERE id_perfil = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$idPerfil]);
        return (string)$stmt->fetchColumn();
    }

    /* ===================================================
    SECCIÓN: CATEGORÍAS
    =================================================== */

    /**
     * 🔍 Obtiene todas las categorías registradas.
     * Por defecto, solo lista las activas (id_estado_logico = 1).
     * Si se desea incluir inactivas, se puede pasar $incluirInactivas = true.
     */
    public function obtenerCategorias(bool $incluirInactivas = false): array
    {
        $sql = "SELECT 
                id_categoria, 
                nombre_categoria, 
                imagen_categoria, 
                id_estado_logico,
                DATE_FORMAT(alta_categoria, '%d/%m/%Y %H:%i') AS fecha_alta,
                DATE_FORMAT(actualizacion_categoria, '%d/%m/%Y %H:%i') AS fecha_actualizacion
            FROM categoria";

        if (!$incluirInactivas) {
            $sql .= " WHERE id_estado_logico = 1"; // Solo activas
        } else {
            $sql .= " WHERE id_estado_logico IN (1, 2)";
        }

        $sql .= " ORDER BY nombre_categoria ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔍 Obtiene una categoría específica por su ID.
     */
    public function obtenerCategoriaPorId(int $idCategoria): ?array
    {
        $sql = "SELECT 
                id_categoria, 
                nombre_categoria, 
                imagen_categoria, 
                id_estado_logico 
            FROM categoria 
            WHERE id_categoria = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idCategoria]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /**
     * 🧩 Verifica si una categoría con el mismo nombre ya existe.
     * Si se está editando, excluye su propio ID.
     */
    public function categoriaExiste(string $nombre, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) 
            FROM categoria 
            WHERE nombre_categoria = ?
              AND id_estado_logico = 1"; // Solo nombres activos
        $params = [$nombre];

        if (!empty($idActual)) {
            $sql .= " AND id_categoria <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Inserta una nueva categoría.
     */
    public function crearCategoria(array $data): bool
    {
        $sql = "INSERT INTO categoria 
                (nombre_categoria, imagen_categoria, id_estado_logico)
            VALUES (:nombre, :imagen, :estado)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre' => $data['nombre_categoria'],
            ':imagen' => !empty($data['imagen_categoria']) ? $data['imagen_categoria'] : null,
            ':estado' => $data['id_estado_logico'] ?? 1
        ]);
    }

    /**
     * Actualiza una categoría existente.
     */
    public function actualizarCategoria(array $data): bool
    {
        $sql = "UPDATE categoria
            SET nombre_categoria = :nombre,
                imagen_categoria = :imagen,
                id_estado_logico = :estado,
                actualizacion_categoria = NOW()
            WHERE id_categoria = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre' => $data['nombre_categoria'],
            ':imagen' => !empty($data['imagen_categoria']) ? $data['imagen_categoria'] : null,
            ':estado' => $data['id_estado_logico'] ?? 1,
            ':id'     => $data['id_categoria']
        ]);
    }

    /**
     * Elimina lógicamente una categoría (cambia a estado inactivo).
     */
    public function eliminarCategoria(int $idCategoria): bool
    {
        $sql = "UPDATE categoria 
            SET id_estado_logico = 2, actualizacion_categoria = NOW()
            WHERE id_categoria = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idCategoria]);
    }

    /**
     * Guarda una categoría (crea o actualiza según corresponda).
     */
    public function guardarCategoria(array $data): bool
    {
        if (!empty($data['id_categoria'])) {
            return $this->actualizarCategoria($data);
        }
        return $this->crearCategoria($data);
    }

    /**
     * Devuelve la cantidad total de categorías registradas.
     */
    public function contarCategorias(bool $soloActivas = false): int
    {
        $sql = "SELECT COUNT(*) FROM categoria";
        $sql .= $soloActivas ? " WHERE id_estado_logico = 1" : " WHERE id_estado_logico IN (1, 2)";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtiene las categorías activas (para selects u otros listados).
     */
    public function obtenerCategoriasActivas(): array
    {
        $sql = "SELECT 
                id_categoria, 
                nombre_categoria 
            FROM categoria 
            WHERE id_estado_logico = 1
            ORDER BY nombre_categoria ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================================================
    SECCIÓN: SUBCATEGORÍAS
    =================================================== */

    /**
     * 🔍 Obtiene todas las subcategorías registradas.
     * Si se pasa $soloActivas = true, devuelve solo las activas.
     * Incluye el nombre de la categoría asociada.
     */
    public function obtenerSubCategorias(bool $soloActivas = true): array
    {
        $sql = "SELECT 
                s.id_sub_categoria,
                s.nombre_sub_categoria,
                s.cant_sub_categoria,
                s.id_estado_logico,
                s.id_categoria,
                c.nombre_categoria AS nombre_categoria,
                DATE_FORMAT(s.id_sub_categoria, '%d/%m/%Y') AS fecha_creacion
            FROM sub_categoria s
            LEFT JOIN categoria c ON s.id_categoria = c.id_categoria";

        if ($soloActivas) {
            $sql .= " WHERE s.id_estado_logico = 1";
        } else {
            $sql .= " WHERE s.id_estado_logico IN (1, 2)";
        }

        $sql .= " ORDER BY c.nombre_categoria ASC, s.nombre_sub_categoria ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔍 Obtiene las subcategorías de una categoría específica.
     * Usado para combos anidados.
     */
    public function obtenerSubCategoriasPorCategoria(int $idCategoria, bool $soloActivas = true): array
    {
        $sql = "SELECT 
                id_sub_categoria,
                nombre_sub_categoria,
                cant_sub_categoria
            FROM sub_categoria
            WHERE id_categoria = :idCat";

        if ($soloActivas) {
            $sql .= " AND id_estado_logico = 1";
        }

        $sql .= " ORDER BY nombre_sub_categoria ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idCat', $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔍 Obtiene una subcategoría por su ID.
     */
    public function obtenerSubCategoriaPorId(int $idSubCategoria): ?array
    {
        $sql = "SELECT 
                s.id_sub_categoria,
                s.nombre_sub_categoria,
                s.cant_sub_categoria,
                s.id_estado_logico,
                s.id_categoria,
                c.nombre_categoria
            FROM sub_categoria s
            LEFT JOIN categoria c ON s.id_categoria = c.id_categoria
            WHERE s.id_sub_categoria = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idSubCategoria]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /**
     * 🧩 Verifica si ya existe una subcategoría con el mismo nombre en la misma categoría.
     */
    public function subCategoriaExiste(string $nombre, int $idCategoria, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) 
            FROM sub_categoria 
            WHERE nombre_sub_categoria = ? 
              AND id_categoria = ? 
              AND id_estado_logico = 1";
        $params = [$nombre, $idCategoria];

        if (!empty($idActual)) {
            $sql .= " AND id_sub_categoria <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * ➕ Crea una nueva subcategoría.
     */
    public function crearSubCategoria(array $data): bool
    {
        $sql = "INSERT INTO sub_categoria 
                (nombre_sub_categoria, cant_sub_categoria, id_estado_logico, id_categoria)
            VALUES (:nombre, :cantidad, :estado, :categoria)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'   => $data['nombre_sub_categoria'],
            ':cantidad' => $data['cant_sub_categoria'],
            ':estado'   => $data['id_estado_logico'] ?? 1,
            ':categoria' => $data['id_categoria']
        ]);
    }

    /**
     * ✏️ Actualiza una subcategoría existente.
     */
    public function actualizarSubCategoria(array $data): bool
    {
        $sql = "UPDATE sub_categoria
            SET nombre_sub_categoria = :nombre,
                cant_sub_categoria   = :cantidad,
                id_estado_logico     = :estado,
                id_categoria         = :categoria
            WHERE id_sub_categoria = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre'   => $data['nombre_sub_categoria'],
            ':cantidad' => $data['cant_sub_categoria'],
            ':estado'   => $data['id_estado_logico'] ?? 1,
            ':categoria' => $data['id_categoria'],
            ':id'       => $data['id_sub_categoria']
        ]);
    }

    /**
     * ❌ Elimina lógicamente una subcategoría (marca como inactiva).
     */
    public function eliminarSubCategoria(int $idSubCategoria): bool
    {
        $sql = "UPDATE sub_categoria
            SET id_estado_logico = 2
            WHERE id_sub_categoria = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idSubCategoria]);
    }

    /**
     * 💾 Guarda una subcategoría (crea o actualiza según corresponda).
     */
    public function guardarSubCategoria(array $data): bool
    {
        if (!empty($data['id_sub_categoria'])) {
            return $this->actualizarSubCategoria($data);
        }
        return $this->crearSubCategoria($data);
    }

    /**
     * 🔢 Devuelve la cantidad total de subcategorías registradas.
     */
    public function contarSubCategorias(bool $soloActivas = false): int
    {
        $sql = "SELECT COUNT(*) FROM sub_categoria";
        $sql .= $soloActivas ? " WHERE id_estado_logico = 1" : " WHERE id_estado_logico IN (1, 2)";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /* ===================================================
    SECCIÓN: MARCAS
    =================================================== */

    /**
     * 🔍 Obtener todas las marcas.
     */
    public function obtenerMarcas(): array
    {
        $sql = "SELECT id_marca, nombre_marca FROM marca ORDER BY nombre_marca ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener marca por ID.
     */
    public function obtenerMarcaPorId(int $idMarca): ?array
    {
        $sql = "SELECT id_marca, nombre_marca FROM marca WHERE id_marca = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idMarca]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /**
     * Verificar si existe marca con el mismo nombre.
     */
    public function marcaExiste(string $nombre, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) FROM marca WHERE nombre_marca = ?";
        $params = [$nombre];

        if (!empty($idActual)) {
            $sql .= " AND id_marca <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nueva marca.
     */
    public function crearMarca(array $data): bool
    {
        $sql = "INSERT INTO marca (nombre_marca) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['nombre_marca']]);
    }

    /**
     * Actualizar marca existente.
     */
    public function actualizarMarca(array $data): bool
    {
        $sql = "UPDATE marca SET nombre_marca = ? WHERE id_marca = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['nombre_marca'], $data['id_marca']]);
    }

    /**
     * Eliminar marca.
     */
    public function eliminarMarca(int $idMarca): bool
    {
        $sql = "DELETE FROM marca WHERE id_marca = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idMarca]);
    }

    /* ===================================================
    SECCIÓN: UNIDADES DE MEDIDA
    =================================================== */

    public function obtenerUnidadesMedida()
    {
        $sql = "SELECT * FROM unidad_medida ORDER BY nombre_unidad_medida ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unidadExiste($nombre, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM unidad_medida WHERE nombre_unidad_medida = :nombre";
        $params = [':nombre' => $nombre];

        if ($excluirId) {
            $sql .= " AND id_unidad_medida != :id";
            $params[':id'] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function crearUnidad($data)
    {
        $sql = "INSERT INTO unidad_medida (nombre_unidad_medida) VALUES (:nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nombre' => $data['nombre_unidad_medida']]);
    }

    public function actualizarUnidad($data)
    {
        $sql = "UPDATE unidad_medida SET nombre_unidad_medida = :nombre WHERE id_unidad_medida = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre_unidad_medida'],
            ':id' => $data['id_unidad_medida']
        ]);
    }

    public function eliminarUnidad($id)
    {
        $sql = "DELETE FROM unidad_medida WHERE id_unidad_medida = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /* ===================================================
    SECCIÓN: MÉTODOS DE PAGO
    =================================================== */

    /**
     * 🔍 Obtiene todos los métodos de pago.
     */
    public function obtenerMetodosPago(): array
    {
        $sql = "SELECT id_metodo_pago, nombre_metodo_pago FROM metodo_pago ORDER BY nombre_metodo_pago ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un método de pago ya existe.
     */
    public function metodoPagoExiste(string $nombre, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) FROM metodo_pago WHERE nombre_metodo_pago = ?";
        $params = [$nombre];

        if (!empty($idActual)) {
            $sql .= " AND id_metodo_pago <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un nuevo método de pago.
     */
    public function crearMetodoPago(array $data): bool
    {
        $sql = "INSERT INTO metodo_pago (nombre_metodo_pago) VALUES (:nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nombre' => $data['nombre_metodo_pago']]);
    }

    /**
     * Actualiza un método de pago existente.
     */
    public function actualizarMetodoPago(array $data): bool
    {
        $sql = "UPDATE metodo_pago SET nombre_metodo_pago = :nombre WHERE id_metodo_pago = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre_metodo_pago'],
            ':id'     => $data['id_metodo_pago']
        ]);
    }

    /**
     * Elimina un método de pago.
     */
    public function eliminarMetodoPago(int $id): bool
    {
        $sql = "DELETE FROM metodo_pago WHERE id_metodo_pago = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /* ===================================================
    SECCIÓN: TIPOS DE NOTA
    =================================================== */

    /**
     * Obtiene todos los tipos de nota.
     */
    public function obtenerTiposNota(): array
    {
        $sql = "SELECT id_tipo_nota, nombre_tipo_nota FROM tipo_nota ORDER BY nombre_tipo_nota ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un tipo de nota ya existe.
     */
    public function tipoNotaExiste(string $nombre, ?int $idActual = null): bool
    {
        $sql = "SELECT COUNT(*) FROM tipo_nota WHERE nombre_tipo_nota = ?";
        $params = [$nombre];

        if (!empty($idActual)) {
            $sql .= " AND id_tipo_nota <> ?";
            $params[] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un nuevo tipo de nota.
     */
    public function crearTipoNota(array $data): bool
    {
        $sql = "INSERT INTO tipo_nota (nombre_tipo_nota) VALUES (:nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nombre' => $data['nombre_tipo_nota']]);
    }

    /**
     * Actualiza un tipo de nota existente.
     */
    public function actualizarTipoNota(array $data): bool
    {
        $sql = "UPDATE tipo_nota SET nombre_tipo_nota = :nombre WHERE id_tipo_nota = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre_tipo_nota'],
            ':id'     => $data['id_tipo_nota']
        ]);
    }

    /**
     * Elimina un tipo de nota.
     */
    public function eliminarTipoNota(int $id): bool
    {
        $sql = "DELETE FROM tipo_nota WHERE id_tipo_nota = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }


    /* ===================================================
    Corchete } de cierre de la clase MasterModel
    =================================================== */
}
