<?php
require_once 'models/SesionModel.php';
require_once 'models/ProductosModel.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductosController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProductosModel();
    }

    # Vista de administrar inventario / ver sección productos  

    public function verFrmProducto()
    {
        Sesion::iniciar();
        $vista = 'views/product/frm_productos.php';
        require_once 'views/layouts/main.php';
    }

    # Listado de productos con filtros y paginado
    public function listarProductos()
    {
        $buscar     = trim($_POST['buscar'] ?? '');
        $orden      = strtoupper($_POST['orden'] ?? 'ASC');
        $pagina     = max(1, (int)($_POST['pagina'] ?? 1));
        $porPagina  = max(1, (int)($_POST['porPagina'] ?? 10));
        $inicio     = ($pagina - 1) * $porPagina;
        $idCategoria    = isset($_POST['id_categoria']) && $_POST['id_categoria'] !== '' ? (int)$_POST['id_categoria'] : null;
        $idSubCategoria = isset($_POST['id_sub_categoria']) && $_POST['id_sub_categoria'] !== '' ? (int)$_POST['id_sub_categoria'] : null;
        $precioMin = isset($_POST['precio_min']) && $_POST['precio_min'] !== '' ? (float)$_POST['precio_min'] : null;
        $precioMax = isset($_POST['precio_max']) && $_POST['precio_max'] !== '' ? (float)$_POST['precio_max'] : null;
        try {
            $data  = $this->model->listarProductosFiltrado(
                $buscar,
                $idCategoria,
                $idSubCategoria,
                $orden,
                $inicio,
                $porPagina,
                $precioMin,
                $precioMax
            );

            $total = $this->model->contarProductosFiltrado(
                $buscar,
                $idCategoria,
                $idSubCategoria,
                $precioMin,
                $precioMax
            );

            echo json_encode([
                'success'   => true,
                'data'      => $data,
                'total'     => (int)$total,
                'porPagina' => $porPagina
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar productos: ' . $e->getMessage()
            ]);
        }
    }

    # Crear nuevo producto con modal
    public function crearProducto()
    {
        # Librería para manejo de imágenes
        require_once 'views/libs/class.upload/src/class.upload.php';

        $codigo       = trim($_POST['codigo_barras'] ?? '');
        $nombre       = trim($_POST['nombre_producto'] ?? '');
        $descripcion  = trim($_POST['descripcion_producto'] ?? '');
        $precioCompra = floatval($_POST['precio_compra'] ?? 0);
        $precioVenta  = floatval($_POST['precio_venta'] ?? 0);
        $stockMinimo  = intval($_POST['stock_minimo'] ?? 0);
        $stockActual  = intval($_POST['stock_actual'] ?? 0);
        $categoria    = intval($_POST['id_categoria'] ?? 0);
        $subcategoria = intval($_POST['id_sub_categoria'] ?? 0);
        $marca        = intval($_POST['id_marca'] ?? 0);
        $unidad       = intval($_POST['id_unidad_medida'] ?? 0);
        $estado       = 8; // 8 <- Estado lógico "Disponible"
        $rutaImagen   = null;

        # Validaciones 

        if ($nombre === '' || strlen($nombre) < 3) {
            echo json_encode(['success' => false, 'message' => 'El nombre debe tener al menos 3 caracteres.']);
            return;
        }

        if ($precioCompra <= 0 || $precioVenta <= 0) {
            echo json_encode(['success' => false, 'message' => 'Los precios deben ser mayores que 0.']);
            return;
        }

        if ($precioVenta < $precioCompra) {
            echo json_encode(['success' => false, 'message' => 'El precio de venta no puede ser menor al de compra.']);
            return;
        }

        if ($stockMinimo < 0 || $stockActual < 0) {
            echo json_encode(['success' => false, 'message' => 'Los valores de stock no pueden ser negativos.']);
            return;
        }

        if ($this->model->productoExiste($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un producto con ese nombre.']);
            return;
        }

        if (!empty($codigo) && $this->model->codigoBarrasExiste($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Ese código de barras ya está registrado.']);
            return;
        }

        if (!empty($_FILES['imagen_producto']['name'])) {
            # Librería para manejo de imágenes
            $handle = new \Verot\Upload\Upload($_FILES['imagen_producto']);
            if ($handle->uploaded) {
                $handle->file_new_name_body = 'prod_' . uniqid();
                $handle->image_resize = true;
                $handle->image_x = 800;
                $handle->image_ratio_y = true;
                $handle->allowed = ['image/*'];
                $handle->process('views/public/uploads/products/images/');

                if ($handle->processed) {
                    $rutaImagen = 'views/public/uploads/products/images/' . $handle->file_dst_name;
                    $handle->clean();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen: ' . $handle->error]);
                    return;
                }
            }
        }

        $ok = $this->model->crearProducto([
            'codigo_barras'      => $codigo ?: null,
            'nombre_producto'    => $nombre,
            'descripcion_producto' => $descripcion ?: null,
            'precio_compra'      => $precioCompra,
            'precio_venta'       => $precioVenta,
            'stock_minimo'       => $stockMinimo,
            'stock_actual'       => $stockActual,
            'imagen_producto'    => $rutaImagen,
            'id_categoria'       => $categoria ?: null,
            'id_sub_categoria'   => $subcategoria ?: null,
            'id_marca'           => $marca ?: null,
            'id_unidad_medida'   => $unidad ?: null,
            'id_estado_logico'   => $estado
        ]);

        # Mensajes de respuesta

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Producto creado correctamente.' : 'Error al guardar el producto.'
        ]);
    }

    # Editar producto con modal

    public function editarProducto()
    {
        # Librería para manejo de imágenes
        require_once 'views/libs/class.upload/src/class.upload.php';

        $idProducto   = intval($_POST['editar_id_producto'] ?? 0);
        $codigo       = trim($_POST['editar_codigo_barras'] ?? '');
        $nombre       = trim($_POST['editar_nombre_producto'] ?? '');
        $descripcion  = trim($_POST['editar_descripcion_producto'] ?? '');
        $precioCompra = floatval($_POST['editar_precio_compra'] ?? 0);
        $precioVenta  = floatval($_POST['editar_precio_venta'] ?? 0);
        $stockMinimo  = intval($_POST['editar_stock_minimo'] ?? 0);
        $stockActual  = intval($_POST['editar_stock_actual'] ?? 0);
        $categoria    = intval($_POST['editar_id_categoria'] ?? 0);
        $subcategoria = intval($_POST['editar_id_sub_categoria'] ?? 0);
        $marca        = intval($_POST['editar_id_marca'] ?? 0);
        $unidad       = intval($_POST['editar_id_unidad_medida'] ?? 0);
        $estado       = 8; // 8 <- Estado lógico "Disponible"

        # Validaciones

        if ($idProducto <= 0) {
            echo json_encode(['success' => false, 'message' => 'Código de producto inválido.']);
            return;
        }

        if ($nombre === '' || strlen($nombre) < 3) {
            echo json_encode(['success' => false, 'message' => 'El nombre debe tener al menos 3 caracteres.']);
            return;
        }

        if ($precioCompra <= 0 || $precioVenta <= 0) {
            echo json_encode(['success' => false, 'message' => 'Los precios deben ser mayores que 0.']);
            return;
        }

        if ($precioVenta < $precioCompra) {
            echo json_encode(['success' => false, 'message' => 'El precio de venta no puede ser menor al de compra.']);
            return;
        }

        if ($stockMinimo < 0 || $stockActual < 0) {
            echo json_encode(['success' => false, 'message' => 'Los valores de stock no pueden ser negativos.']);
            return;
        }

        if ($this->model->productoExiste($nombre, $idProducto)) {
            echo json_encode(['success' => false, 'message' => 'Ya existe otro producto con ese nombre.']);
            return;
        }

        if (!empty($codigo) && $this->model->codigoBarrasExiste($codigo, $idProducto)) {
            echo json_encode(['success' => false, 'message' => 'Ese código de barras ya está registrado.']);
            return;
        }

        $productoActual = $this->model->obtenerProductoPorId($idProducto);
        if (!$productoActual) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado.']);
            return;
        }

        $rutaImagen = $productoActual['imagen_producto'] ?? null;

        if (!empty($_FILES['editar_imagen_producto']['name'])) {
            $handle = new \Verot\Upload\Upload($_FILES['editar_imagen_producto']);
            if ($handle->uploaded) {
                $handle->file_new_name_body = 'prod_' . uniqid();
                $handle->image_resize = true;
                $handle->image_x = 800;
                $handle->image_ratio_y = true;
                $handle->allowed = ['image/*'];
                $handle->process('views/public/uploads/products/images/');

                if ($handle->processed) {
                    if (!empty($rutaImagen) && file_exists($rutaImagen)) {
                        @unlink($rutaImagen);
                    }
                    # Alojamiento de la nueva imagen
                    $rutaImagen = 'views/public/uploads/products/images/' . $handle->file_dst_name;
                    $handle->clean();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen: ' . $handle->error]);
                    return;
                }
            }
        }

        $ok = $this->model->actualizarProducto([
            'id_producto'        => $idProducto,
            'codigo_barras'      => $codigo ?: null,
            'nombre_producto'    => $nombre,
            'descripcion_producto' => $descripcion ?: null,
            'precio_compra'      => $precioCompra,
            'precio_venta'       => $precioVenta,
            'stock_minimo'       => $stockMinimo,
            'stock_actual'       => $stockActual,
            'imagen_producto'    => $rutaImagen,
            'id_categoria'       => $categoria ?: null,
            'id_sub_categoria'   => $subcategoria ?: null,
            'id_marca'           => $marca ?: null,
            'id_unidad_medida'   => $unidad ?: null,
            'id_estado_logico'   => $estado
        ]);

        # Mensajes de respuesta

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Producto actualizado correctamente.' : 'Error al actualizar el producto.'
        ]);
    }

    # Eliminar producto con modal (cambio de estado lógico de "disponible" a "no disponible")
    public function eliminarProducto()
    {
        $id = intval($_POST['id_producto'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Código de producto inválido.']);
            return;
        }

        # Mensaje de respuesta

        $ok = $this->model->eliminarProducto($id);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Producto eliminado correctamente.' : 'Error al eliminar el producto.'
        ]);
    }

    # Listados para el relleno de combos
    public function listarCategorias()
    {
        $data = $this->model->obtenerCategoriasActivas();
        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function listarSubCategoriasPorCategoria()
    {
        $idCategoria = intval($_POST['id_categoria'] ?? 0);
        $data = $this->model->obtenerSubCategoriasPorCategoria($idCategoria);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function listarMarcas()
    {
        $data = $this->model->obtenerMarcas();
        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function listarUnidadesMedida()
    {
        $data = $this->model->obtenerUnidadesMedida();
        echo json_encode(['success' => true, 'data' => $data]);
    }
    public function listarEstadosLogicos()
    {
        $data = $this->model->obtenerEstadosLogicos();
        echo json_encode(['success' => true, 'data' => $data]);
    }

    # Exportar listado de productos a Excel (.xls)

    public function exportarListadoProductos()
    {
        try {
            $model = new ProductosModel();
            $productos = $model->obtenerProductos();

            if (empty($productos)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => false,
                    'message' => 'No hay productos disponibles para exportar.'
                ]);
                return;
            }

            // Crear el libro de Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Informe de inventario');

            // Encabezados
            $headers = [
                'A1' => 'Nombre del producto',
                'B1' => 'Descripción',
                'C1' => 'Categoría',
                'D1' => 'Subcategoría',
                'E1' => 'Marca',
                'F1' => 'Unidad de medida',
                'G1' => 'Precio de compra',
                'H1' => 'Precio de venta',
                'I1' => 'Stock actual',
                'J1' => 'Stock mínimo',
                'K1' => 'Stock'
            ];

            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }

            // Estilo de encabezado
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DDDDDD']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'AAAAAA']
                    ]
                ],
            ];
            $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Cargar datos
            $row = 2;
            foreach ($productos as $p) {
                $sheet->setCellValue("A{$row}", $p['nombre_producto']);
                $sheet->setCellValue("B{$row}", $p['descripcion_producto']);
                $sheet->setCellValue("C{$row}", $p['nombre_categoria']);
                $sheet->setCellValue("D{$row}", $p['nombre_sub_categoria']);
                $sheet->setCellValue("E{$row}", $p['nombre_marca']);
                $sheet->setCellValue("F{$row}", $p['nombre_unidad_medida']);
                $sheet->setCellValue("G{$row}", (float)$p['precio_compra']);
                $sheet->setCellValue("H{$row}", (float)$p['precio_venta']);
                $sheet->setCellValue("I{$row}", (float)$p['stock_actual']);
                $sheet->setCellValue("J{$row}", (float)$p['stock_minimo']);
                $sheet->setCellValue("K{$row}", $p['nombre_estado_logico']);
                $row++;
            }

            // Formatos numéricos
            $sheet->getStyle("G2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("I2:J{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

            // Ajustar columnas
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Borde para todos los datos
            $sheet->getStyle("A1:L" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);

            // Nombre del archivo
            date_default_timezone_set('America/Argentina/San_Luis');
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            $d = (int)date('d');
            $m = (int)date('n');
            $y = (int)date('Y');
            $nombreArchivo = sprintf('inventario_productos_%d_%s_%d.xlsx', $d, $meses[$m], $y);

            // Enviar encabezados HTTP
            if (ob_get_length()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            header('Expires: 0');

            // Exportar
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'message' => 'Error al generar el Excel.',
                'error' => $e->getMessage()
            ]);
        }
    }
}