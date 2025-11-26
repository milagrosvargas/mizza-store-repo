<?php
// Variables esperadas: $titulo, $contenido (ruta de vista), $data
$titulo = $titulo ?? 'Dashboard | MizzaStore';

// Verifico que la vista existe
if (!isset($contenido) || !file_exists($contenido)) {
    $contenidoRenderizado = "<p class='text-danger'>Contenido no disponible.</p>";
} else {
    // 📌 Renderizo la vista real y capturo su contenido
    ob_start();
    include $contenido;
    $contenidoRenderizado = ob_get_clean();
}

// Estilos exclusivos del dashboard (opcional)
$extraStyles = '<link rel="stylesheet" href="/MizzaStore/assets/css/global.css">';

// 🚀 Cargo layout principal SIN perder datos ni destruir variables
require 'views/layouts/main.php';
?>
