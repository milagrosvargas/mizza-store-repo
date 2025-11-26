<?php
require_once 'models/ClienteModel.php';
require_once 'models/HistorialModel.php';
require_once 'views/libs/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ClienteController
{
    private $clienteModel;
    private $historialModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->historialModel = new HistorialModel();
        Sesion::establecerClienteDesdeBD();
    }

    // Formulario para la creación de clientes
    public function verFrmCliente()
    {
        $generos    = $this->clienteModel->obtenerGeneros();
        $paises     = $this->clienteModel->obtenerPaises();
        $provincias = $this->clienteModel->obtenerProvincias();
        $localidades = $this->clienteModel->obtenerLocalidades();
        $barrios    = $this->clienteModel->obtenerBarrios();
        $tiposDocumento = $this->clienteModel->obtenerTiposDocumento();

        $vista = 'views/client/frm_cliente.php';
        require_once 'views/layouts/main.php';
    }

    // Endpoints de validación (AJAX)

    public function validarEmail()
    {
        $email = $_GET['email'] ?? '';
        $exists = $this->clienteModel->existeEmail($email);
        echo json_encode(['success' => true, 'exists' => $exists]);
    }

    public function validarTelefono()
    {
        $telefono = $_GET['telefono'] ?? '';
        $exists = $this->clienteModel->existeTelefono($telefono);
        echo json_encode(['success' => true, 'exists' => $exists]);
    }

    public function validarUsuario()
    {
        $usuario = $_GET['usuario'] ?? '';
        $exists = $this->clienteModel->existeUsuario($usuario);
        echo json_encode(['success' => true, 'exists' => $exists]);
    }

    // REGISTRO COMPLETO DE CLIENTE
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Sanitización de datos
        $data = [
            'nombre'           => trim($_POST['nombre'] ?? ''),
            'apellido'         => trim($_POST['apellido'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'genero'           => intval($_POST['genero'] ?? 0),
            'tipo_documento'   => intval($_POST['tipo_documento'] ?? 0),
            'numero_documento' => trim($_POST['numero_documento'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'telefono'         => trim($_POST['telefono'] ?? ''),
            'pais'             => intval($_POST['pais'] ?? 0),
            'provincia'        => intval($_POST['provincia'] ?? 0),
            'ciudad'           => intval($_POST['ciudad'] ?? 0),
            'barrio'           => intval($_POST['barrio'] ?? 0),
            'direccion'        => trim($_POST['direccion'] ?? ''),
            'numero'           => trim($_POST['numero'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password2'        => $_POST['password2'] ?? '',
            'usuario'          => trim($_POST['usuario'] ?? '')
        ];

        // Validaciones backend
        $errores = [];

        if (empty($data['nombre'])) $errores[] = 'El nombre es obligatorio.';
        if (empty($data['apellido'])) $errores[] = 'El apellido es obligatorio.';

        // Fecha de nacimiento y mayoría de edad
        if (empty($data['fecha_nacimiento'])) {
            $errores[] = 'La fecha de nacimiento es obligatoria.';
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_nacimiento']);
            $hoy = new DateTime();
            if (!$fecha || $fecha > $hoy) {
                $errores[] = 'Fecha de nacimiento inválida.';
            } else {
                $edad = $hoy->diff($fecha)->y;
                if ($edad < 18) {
                    $errores[] = 'Debes tener al menos 18 años.';
                }
            }
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'Email no válido.';
        if (empty($data['telefono'])) $errores[] = 'El teléfono es obligatorio.';
        if (empty($data['genero'])) $errores[] = 'Debe seleccionar un género.';
        if (empty($data['tipo_documento']) || empty($data['numero_documento'])) $errores[] = 'Debe indicar tipo y número de documento.';
        if (empty($data['pais']) || empty($data['provincia']) || empty($data['ciudad']) || empty($data['barrio'])) $errores[] = 'Complete todos los campos de ubicación.';
        if (empty($data['direccion'])) $errores[] = 'La dirección es obligatoria.';
        if ($data['password'] !== $data['password2']) $errores[] = 'Las contraseñas no coinciden.';
        if (strlen($data['password']) < 6) $errores[] = 'La contraseña es demasiado corta.';

        // Duplicados
        if ($this->clienteModel->existeEmail($data['email'])) $errores[] = 'El email ya está registrado.';
        if ($this->clienteModel->existeTelefono($data['telefono'])) $errores[] = 'El teléfono ya está registrado.';
        if ($this->clienteModel->existeUsuario($data['usuario'])) $errores[] = 'El usuario ya existe.';

        if (!empty($errores)) {
            echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
            return;
        }


        // Guardar cliente
        $resultado = $this->clienteModel->insertarCliente($data);

        if (!$resultado['success']) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar cliente.']);
            return;
        }

        // Enviar correo de activación
        $envio = $this->enviarCorreoActivacion($resultado['email'], $resultado['usuario'], $resultado['token']);

        if ($envio) {
            echo json_encode([
                'success' => true,
                'message' => 'Cliente registrado. Se envió un correo para activar la cuenta.'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Cliente registrado, pero no se pudo enviar el correo de activación.'
            ]);
        }
    }

    // Envío de correo de activación
    private function enviarCorreoActivacion($email, $usuario, $token)
    {
        try {
            // Instancia PHPMailer y activa el modo de excepciones
            $mail = new PHPMailer(true);

            // Configuración del servidor SMTP (Gmail en este caso)
            $mail->isSMTP();                              // Indica que se usará SMTP
            $mail->Host       = 'smtp.gmail.com';         // Servidor SMTP de Gmail
            $mail->SMTPAuth   = true;                     // Habilita la autenticación SMTP
            $mail->Username   = 'ladardrgz@gmail.com';    // Tu correo completo de Gmail
            $mail->Password   = 'xcbu fdze xqfl ieik';    // Contraseña de aplicación generada en Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Cifrado TLS (puerto 587)
            $mail->Port       = 587;                      // Puerto estándar para TLS

            // Configuración del remitente y destinatario
            $mail->setFrom('no-reply@mizzastore.com', 'MizzaStore'); // Remitente visible en el correo
            $mail->addAddress($email);                     // Destinatario (correo del usuario)

            // Contenido del correo
            $mail->isHTML(true);                           // Permite HTML en el cuerpo
            $mail->Subject = 'Activa tu cuenta en MizzaStore'; // Asunto del correo

            // Enlace de activación con el token
            $link = "http://localhost/MizzaStore/index.php?controller=Activar&action=cuenta&token={$token}";

            // Cuerpo HTML del mensaje
            $mail->Body = "
            <h2>¡Hola, {$usuario}!</h2>
            <p>Gracias por registrarte en <strong>MizzaStore</strong>.</p>
            <p>Para activar tu cuenta, haz clic en el siguiente enlace:</p>
            <p><a href='{$link}' style='background:#d94b8c;color:#fff;padding:10px 15px;border-radius:5px;text-decoration:none;'>Activar cuenta</a></p>
            <p>Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>
        ";

            // Envía el correo
            $mail->send();
            return true; // Éxito
        } catch (Exception $e) {
            // Si ocurre un error, lo registra y devuelve false
            error_log("Error enviando correo: " . $e->getMessage());
            return false;
        }
    }
// Mostrar la vista del historial de compras
// Utilizado para renderizar la interfaz con filtros, tabla y paginador
public function verSeccionHistorial()
{
    Sesion::iniciar();
    // Solo carga la vista que contiene el frontend del historial
    $vista = 'views/client/historial.php';
    require_once 'views/layouts/main.php';
}


// Endpoint AJAX: retorna historial paginado y filtrado
// Usado por la vista historial.php para cargar datos dinámicos
public function obtenerHistorial()
{
    $idCliente = $_SESSION['id_cliente'] ?? null;

    if (!$idCliente) {
        echo json_encode(['success' => false, 'message' => 'Cliente no autenticado']);
        return;
    }

    // Parámetros de paginación
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $registrosPorPagina = isset($_GET['registrosPorPagina']) ? (int)$_GET['registrosPorPagina'] : 10;
    $inicio = ($pagina - 1) * $registrosPorPagina;

    // Filtros recibidos desde la vista (sin warnings)
    $filtros = [
        'fechaInicio'   => $_GET['fechaInicio']   ?? null,
        'fechaFin'      => $_GET['fechaFin']      ?? null,
        'estadoPedido'  => $_GET['estadoPedido']  ?? null,
        'estadoPago'    => $_GET['estadoPago']    ?? null,
        'metodoPago'    => $_GET['metodoPago']    ?? null,
        'montoMin'      => $_GET['montoMin']      ?? null,
        'montoMax'      => $_GET['montoMax']      ?? null,
        'buscarPedido'  => $_GET['buscarPedido']  ?? null,
    ];

    // Obtener registros filtrados y paginados
    $registros = $this->historialModel->obtenerHistorialFiltrado($idCliente, $filtros, $inicio, $registrosPorPagina);
    $totalRegistros = $this->historialModel->contarHistorialFiltrado($idCliente, $filtros);
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    echo json_encode([
        'success'        => true,
        'data'           => $registros,
        'paginaActual'   => $pagina,
        'totalPaginas'   => $totalPaginas,
        'totalRegistros' => $totalRegistros
    ]);
}



// Mostrar detalle de un pedido específico del cliente
// Renderiza vista individual con toda la información del pedido
public function verDetallePedido()

{
    if (!isset($_GET['id'])) {
        echo "Pedido no especificado.";
        return;
    }

    $idPedido = (int)$_GET['id'];
    $idCliente = $_SESSION['id_cliente'];

    // El modelo controla la seguridad del pedido (pertenencia al cliente)
    $detalle = $this->historialModel->obtenerDetallePedido($idPedido, $idCliente);

    if (!$detalle) {
        echo "No se encontró el pedido o no pertenece a tu cuenta.";
        return;
    }

    // Datos disponibles en la vista
    $pedido    = $detalle['pedido'];
    $productos = $detalle['productos'];
    $envio     = $detalle['envio'];

    $vista = 'views/client/detalle_pedido.php';
    require_once 'views/layouts/main.php';
}

} // <- Fin del controller
