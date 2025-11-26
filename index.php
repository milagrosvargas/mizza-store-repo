<?php
// 🧩 Inicia la sesión centralizada
require_once 'core/Sesion.php';
Sesion::iniciar();

// 🧠 Carga el autoload de Composer (PhpSpreadsheet u otras librerías externas)
require_once __DIR__ . '/views/libs/vendor/autoload.php';

// 🔁 Autocarga de clases: controladores y modelos de tu arquitectura MVC
spl_autoload_register(function ($class) {
    $controllerPath = "controllers/$class.php";
    $modelPath = "models/$class.php";

    if (file_exists($controllerPath)) {
        require_once $controllerPath;  // Si es un controlador
    } elseif (file_exists($modelPath)) {
        require_once $modelPath;       // Si es un modelo
    }
});

// 📦 Obtiene el controlador y la acción desde la URL
$controllerName = isset($_GET['controller']) ? $_GET['controller'] . 'Controller' : 'HomeController';
$actionName = $_GET['action'] ?? 'index';

// 🧹 Sanitiza el nombre de la acción para evitar inyecciones o errores
$actionName = preg_replace('/[^a-zA-Z0-9_]/', '', $actionName);

// ✅ Verifica que el controlador exista
$controllerPath = "controllers/$controllerName.php";
if (!file_exists($controllerPath)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => "Controlador no encontrado: $controllerName"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 📥 Carga el controlador y crea una instancia dinámica
require_once $controllerPath;
$controller = new $controllerName();

// ❓ Verifica que la acción exista dentro del controlador
if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => "Acción no encontrada: $actionName"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 🚀 Ejecuta la acción del controlador
call_user_func([$controller, $actionName]);
