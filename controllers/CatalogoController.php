<?php
require_once 'models/ProductosModel.php';
require_once 'core/Sesion.php';

class CatalogoController
{
    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new ProductosModel();
    }

    // Vista principal del catálogo de cosméticos
    public function cosmeticos()
    {
        Sesion::iniciar();

        // Si se pasa un slug de categoría (por ejemplo: ?categoria=maquillaje)
        /*
        ** Un slug es una versión de un texto (normalmente un título) 
        que se convierte en una cadena amigable para usar en URLs.
        Se genera transformando ese texto a un formato simple, legible y sin caracteres especiales.
        */
        $categoriaSlug = $_GET['categoria'] ?? null;
        $nombreCategoria = 'Todos los productos';
        $idCategoria = null;

        if ($categoriaSlug) {
            $categorias = $this->productoModel->obtenerCategoriasActivas();
            foreach ($categorias as $cat) {
                $slug = strtolower(str_replace(' ', '-', $cat['nombre_categoria']));
                if ($slug === $categoriaSlug) {
                    $idCategoria = $cat['id_categoria'];
                    $nombreCategoria = $cat['nombre_categoria'];
                    break;
                }
            }
        }

        // Las categorías se cargarán por AJAX, pero enviamos datos base
        $categorias = $this->productoModel->obtenerCategoriasActivas();

        $vista = 'views/public/cosmeticos.php';
        require_once 'views/layouts/main.php';
    }

    // Listar productos con filtros (respuesta JSON)
    public function listarProductosAjax()
    {
        // Filtros recibidos por AJAX
        $buscar     = trim($_POST['buscar'] ?? '');
        $orden      = strtoupper($_POST['orden'] ?? 'ASC');
        $pagina     = max(1, (int)($_POST['pagina'] ?? 1));
        $porPagina  = max(1, (int)($_POST['porPagina'] ?? 12));
        $inicio     = ($pagina - 1) * $porPagina;

        $idCategoria    = isset($_POST['id_categoria']) && $_POST['id_categoria'] !== '' ? (int)$_POST['id_categoria'] : null;
        $idSubCategoria = isset($_POST['id_sub_categoria']) && $_POST['id_sub_categoria'] !== '' ? (int)$_POST['id_sub_categoria'] : null;
        $precioMin      = isset($_POST['precio_min']) && $_POST['precio_min'] !== '' ? (float)$_POST['precio_min'] : null;
        $precioMax      = isset($_POST['precio_max']) && $_POST['precio_max'] !== '' ? (float)$_POST['precio_max'] : null;

        try {
            // Obtener productos filtrados
            $productos = $this->productoModel->listarProductosFiltrado(
                $buscar,
                $idCategoria,
                $idSubCategoria,
                $orden,
                $inicio,
                $porPagina,
                $precioMin,
                $precioMax
            );

            // Contar total de productos (para la paginación)
            $total = $this->productoModel->contarProductosFiltrado(
                $buscar,
                $idCategoria,
                $idSubCategoria,
                $precioMin,
                $precioMax
            );

            // Validar rutas de imágenes
            foreach ($productos as &$p) {
                $ruta = $p['imagen_producto'];
                if (empty($ruta) || !file_exists($ruta)) {
                    $p['imagen_producto'] = 'views/public/uploads/products/default.png';
                }
            }

            // Enviar respuesta en formato JSON
            echo json_encode([
                'success'   => true,
                'data'      => $productos,
                'total'     => (int)$total,
                'porPagina' => (int)$porPagina,
                'pagina'    => (int)$pagina
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar productos: ' . $e->getMessage()
            ]);
        }
    }

    // Listar categorías activas (respuesta JSON)
    public function listarCategoriasAjax()
    {
        try {
            $categorias = $this->productoModel->obtenerCategoriasActivas();

            echo json_encode([
                'success' => true,
                'data'    => $categorias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ]);
        }
    }

    // Listar subcategorías según la categoría (respuesta JSON)
    public function listarSubCategoriasAjax()
    {
        $idCategoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 0;

        try {
            if ($idCategoria <= 0) {
                throw new Exception('Categoría inválida');
            }

            $subcategorias = $this->productoModel->obtenerSubCategoriasPorCategoria($idCategoria);

            echo json_encode([
                'success' => true,
                'data'    => $subcategorias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener subcategorías: ' . $e->getMessage()
            ]);
        }
    }
}