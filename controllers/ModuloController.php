<?php
// controllers/ModuloController.php

require_once 'models/ModuloModel.php';

class ModuloController
{
/**
 * Devuelve los módulos activos autorizados para el perfil del usuario actual como JSON
 */
public function listar()
{
    header('Content-Type: application/json');

    try {
        // Verificar si hay perfil en sesión
        if (!isset($_SESSION['id_perfil'])) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró el perfil del usuario en la sesión.'
            ]);
            return;
        }

        $idPerfil = (int) $_SESSION['id_perfil'];
        $modelo = new ModuloModel();

        // Obtener los módulos según el perfil
        $modulos = $modelo->obtenerModulosPorPerfil($idPerfil);

        echo json_encode([
            'success' => true,
            'data' => $modulos
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los módulos: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Crea un módulo si no existe
     */
    public function crear()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $nombre = trim($data['nombre'] ?? '');

        if ($nombre === '') {
            echo json_encode(['success' => false, 'message' => 'El nombre del módulo es requerido.']);
            return;
        }

        $modelo = new ModuloModel();

        if ($modelo->existeModulo($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El módulo ya existe.']);
            return;
        }

        $creado = $modelo->crear($nombre);
        echo json_encode([
            'success' => $creado,
            'message' => $creado ? 'Módulo creado correctamente.' : 'Error al crear el módulo.'
        ]);
    }

    /**
     * Elimina lógicamente un módulo
     */
    public function eliminar()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = (int) ($_DELETE['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            return;
        }

        $modelo = new ModuloModel();
        $eliminado = $modelo->desactivar($id);

        echo json_encode([
            'success' => $eliminado,
            'message' => $eliminado ? 'Módulo eliminado correctamente.' : 'No se pudo eliminar el módulo.'
        ]);
    }

    /**
     * Muestra la vista para gestión de módulos
     */
    public function verFormulario()
    {
        $titulo = 'Gestión de módulo';
        $contenido = 'views/masters/modulo.php';
        require 'views/layouts/main.php';
    }
}
