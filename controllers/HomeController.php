<?php
require_once 'core/Sesion.php';

/**
 * Controlador principal de la web externa
 * 
 * HomeController se encarga de gestionar la vista inicial del cliente
 * (la página de inicio o “home”) y controlar el acceso según el estado de sesión.
 */
class HomeController
{
    /**
     * Acción por defecto del controlador
     * 
     * Método responsable de mostrar la página de inicio del cliente (home),
     * pero antes verifica si el usuario ya tiene una sesión activa
     */
    public function index()
    {
        // Iniciar o reanudar la sesión del usuario
        Sesion::iniciar();

        // Si ya está autenticado, redirigir al panel interno
        if (Sesion::usuarioAutenticado()) {
            header('Location: index.php?controller=panel&action=dashboard');
            exit;
        }

        // Indicar la vista de contenido que cargará dentro del layout principal
        $vista = 'views/public/home.php';

        // Incluir layout principal (que carga el navbar, estructura HTML, etc.)
        require_once 'views/layouts/main.php';
    }

    /**
     * Sección - Cosméticos
     * Muestra la vista pública con los productos cosméticos destacados.
     */
    public function cosmeticos()
    {
        Sesion::iniciar();
        $vista = 'views/public/cosmeticos.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Sección - Blog
     * Muestra la vista pública del blog con artículos de maquillaje y belleza.
     */
    public function blog()
    {
        Sesion::iniciar();
        $vista = 'views/public/blog.php';
        require_once 'views/layouts/main.php';
    }

    /**
     * Sección - Sobre Nosotros
     * Muestra la vista institucional de la empresa.
     */
    public function sobreNosotros()
    {
        Sesion::iniciar();
        $vista = 'views/public/sobre_nosotros.php';
        require_once 'views/layouts/main.php';
    }
}
