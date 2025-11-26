<?php
// Controlador responsable de la autenticación y recuperación:
// Login con credenciales (usuario o email)
// Envío de correo de recuperación de contraseña
// Cambio de contraseña mediante token

require_once 'core/Sesion.php';
require_once 'models/AuthModel.php';
require_once 'core/DashboardHelper.php';
require_once 'views/libs/vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LoginController
{
    private AuthModel $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    // Autenticar usuario
    public function autenticar(string $usuarioOEmail, string $contrasena): array
    {
        try {
            $usuario = $this->authModel->verificarCredenciales($usuarioOEmail);
            
            if (!$usuario) {
                return ['success' => false, 'message' => 'Usuario o correo electrónico no encontrado.'];
            }

            if ((int)$usuario['estado_usuario'] !== 1) {
                return ['success' => false, 'message' => 'El usuario está inactivo. Contacta al administrador.'];
            }

            if ((int)$usuario['cuenta_activada'] !== 1) {
                return [
                    'success' => false,
                    'message' => 'Tu cuenta aún no ha sido activada. Revisa tu correo electrónico o solicita un nuevo enlace de activación.',
                    'reenviar_activacion' => true,
                    'id_usuario' => $usuario['id_usuario']
                ];
            }

            require_once 'models/SesionModel.php';
            $sesionModel = new SesionModel();

            if ($sesionModel->sesionActiva($usuario['id_usuario'])) {
                return ['success' => false, 'message' => 'Este usuario ya tiene una sesión activa en otro dispositivo.'];
            }

            if (!password_verify($contrasena, $usuario['password_usuario'])) {
                return ['success' => false, 'message' => 'Credenciales inválidas. Verifica tus datos.'];
            }

            Sesion::establecerUsuario([
                'id_usuario'         => $usuario['id_usuario'],
                'nombre_usuario'     => $usuario['nombre_usuario'],
                'relacion_persona'   => $usuario['relacion_persona'],
                'relacion_perfil'    => $usuario['relacion_perfil'],
                'descripcion_perfil' => $usuario['descripcion_perfil'] ?? 'Invitado'
            ]);

            return [
                'success'  => true,
                'redirect' => 'index.php?controller=Login&action=redirigirPorPerfil',
                'message'  => '¡Inicio de sesión exitoso!'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error en autenticación: ' . $e->getMessage()];
        }
    }

    // API Login (AJAX)
    public function loginApi(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || empty($data['usuario']) || empty($data['contrasena'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Petición inválida. Faltan datos.']);
            return;
        }

        $usuario    = trim($data['usuario']);
        $contrasena = trim($data['contrasena']);

        $resultado = $this->autenticar($usuario, $contrasena);

        $status = 200;
        if (!$resultado['success']) {
            if (str_contains($resultado['message'], 'no encontrado')) {
                $status = 404;
            } elseif (str_contains($resultado['message'], 'inactivo')) {
                $status = 403;
            } elseif (str_contains($resultado['message'], 'activada')) {
                $status = 423;
            } else {
                $status = 401;
            }
        }

        echo json_encode(array_merge($resultado, ['status_code' => $status]), JSON_UNESCAPED_UNICODE);
    }

    // Redirección por perfil
    public function redirigirPorPerfil()
    {
        Sesion::requerirLogin();
        $usuario = Sesion::obtenerUsuario();

        if (!$usuario || empty($usuario['descripcion_perfil'])) {
            echo "Error: el perfil del usuario no está definido.";
            exit;
        }

        header('Location: index.php?controller=Panel&action=dashboard');
        exit;
    }

    // Vista del inicio de sesión
    public function login()
    {
        if (Sesion::usuarioAutenticado()) {
            header('Location: index.php?controller=Panel&action=dashboard');
            exit;
        }

        Sesion::inicializarInvitado();
        require_once 'views/login/account.php';
    }

    // Cierre de sesión
    public function logout()
    {
        Sesion::destruir();
        header('Location: index.php?controller=Home&action=index');
        exit;
    }

    // Restablecimiento de contraseña
    public function restablecerContrasena()
    {
        // Muestra la vista del formulario para ingresar correo
        Sesion::iniciar();
        $vista = 'views/login/restablecer_contrasena.php';
        require_once 'views/layouts/main.php';
    }

    // Solicitar recuperación a través de token
    public function solicitarRecuperacion()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $correo = trim($data['correo'] ?? '');

        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido.']);
            return;
        }

        $usuario = $this->authModel->obtenerUsuarioPorCorreo($correo);
        if (!$usuario) {
            echo json_encode(['success' => false, 'message' => 'No existe ninguna cuenta asociada a este correo.']);
            return;
        }

        // Token y expiración
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->authModel->guardarTokenRecuperacion($usuario['id_usuario'], $token, $expira);

        // Enviar correo
        $enviado = $this->enviarCorreoRecuperacion($correo, $usuario['nombre_usuario'], $token);

        echo json_encode([
            'success' => $enviado,
            'message' => $enviado
                ? 'Se ha enviado un enlace de recuperación a tu correo.'
                : 'No se pudo enviar el correo. Intenta más tarde.'
        ]);
    }

    // Validación token y mostrar vista de nueva contraseña
    public function nuevaContrasena()
    {
        Sesion::iniciar();
        $token = $_GET['token'] ?? '';

        // Validar el token antes de mostrar la vista
        $resultado = $this->authModel->validarTokenRecuperacion($token);
        if (!$resultado['valido']) {
            $mensaje = $resultado['motivo'];
            require_once 'views/login/token_invalido.php';
            return;
        }

        $vista = 'views/login/nueva_contrasena.php';
        require_once 'views/layouts/main.php';
    }

// Actualización de contraseña (desde AJAX)
public function actualizarContrasena()
{
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $token = $data['token'] ?? '';
    $pass1 = $data['password'] ?? '';
    $pass2 = $data['password2'] ?? '';

    if (empty($pass1) || empty($pass2)) {
        echo json_encode(['success' => false, 'message' => 'Debes ingresar y confirmar la contraseña.']);
        return;
    }

    if ($pass1 !== $pass2) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        return;
    }

    if (strlen($pass1) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
        return;
    }

    $validacion = $this->authModel->validarTokenRecuperacion($token);
    if (!$validacion['valido']) {
        echo json_encode(['success' => false, 'message' => $validacion['motivo']]);
        return;
    }

    $this->authModel->actualizarContrasena($validacion['id_usuario'], $pass1);
    $this->authModel->marcarTokenUsado($token);

    echo json_encode(['success' => true, 'message' => 'Tu contraseña ha sido actualizada correctamente.']);
}


    // Envío de correo para recuperación 
    private function enviarCorreoRecuperacion(string $correo, string $usuario, string $token): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'milovargasbz@gmail.com';
            $mail->Password   = 'xcbu fdze xqfl ieik';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@mizzastore.com', 'MizzaStore');
            $mail->addAddress($correo);
            $mail->isHTML(true);
            $mail->Subject = 'Recupera tu contraseña - MizzaStore';

            $link = "http://localhost/MizzaStore/index.php?controller=Login&action=nuevaContrasena&token={$token}";
            $mail->Body = "
                <h2>Hola, {$usuario}</h2>
                <p>Has solicitado restablecer tu contraseña en <strong>MizzaStore</strong>.</p>
                <p>Para continuar, haz clic en el siguiente enlace:</p>
                <p><a href='{$link}' style='background:#d94b8c;color:#fff;padding:10px 15px;border-radius:5px;text-decoration:none;'>Restablecer contraseña</a></p>
                <p>Si no hiciste esta solicitud, puedes ignorar este mensaje.</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error enviando correo de recuperación: " . $e->getMessage());
            return false;
        }
    }
}
