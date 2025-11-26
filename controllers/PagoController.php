<?php
// Sesión del sistema
require_once 'core/Sesion.php';

// Conexión a base de datos (PDO)
require_once 'models/Conexion.php';

// Modelos usados en el flujo de pago
require_once 'models/PedidoModel.php';
require_once 'models/EnvioModel.php';
require_once 'models/PagoModel.php';

// SDK Mercado Pago (via Composer)
require_once __DIR__ . '/../views/libs/vendor/autoload.php';
// Librerías PHPMailer y Dompdf si están en views/libs
require_once 'views/libs/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MercadoPago\MercadoPagoConfig;
use Dompdf\Dompdf;


class PagoController
{
    private $db;
    private $pedidoModel;
    private $envioModel;
    private $pagoModel;

    public function __construct()
    {
        Sesion::iniciar();

        $conexion = new Conexion();
        $this->db = $conexion->Conectar();

        $this->pedidoModel = new PedidoModel($this->db);
        $this->envioModel  = new EnvioModel($this->db);
        $this->pagoModel   = new PagoModel($this->db);
    }

    // ==========================================================
    // 1) Procesar pago desde checkout API (token MP)
    // ==========================================================
    public function procesarPagoAPI()
    {
        header('Content-Type: application/json; charset=utf-8');
        Sesion::iniciar();

        try {
            $raw  = file_get_contents("php://input");
            $data = json_decode($raw, true);

            file_put_contents(
                "logs_pago_api.txt",
                "\n[" . date("Y-m-d H:i:s") . "] RAW RECIBIDO: " . $raw . "\n",
                FILE_APPEND
            );

            if (!$data) {
                echo json_encode([
                    'success' => false,
                    'message' => "No se recibieron datos del Brick."
                ]);
                return;
            }

            $token        = $data['token'] ?? null;
            $methodId     = $data['payment_method_id'] ?? null;
            $issuerId     = $data['issuer_id'] ?? null;
            $installments = $data['installments'] ?? 1;
            $amount       = $data['transaction_amount'] ?? $data['amount'] ?? 0;
            $idPedido     = $data['id_pedido'] ?? null;

            if (!$token || !$idPedido || !$amount) {
                echo json_encode([
                    'success' => false,
                    'message' => "Faltan datos obligatorios del pago."
                ]);
                return;
            }

            MercadoPagoConfig::setAccessToken("TEST-6187758628847843-112518-2b28778f119e91b6b4250df796e75264-3016728834");
            $client = new \MercadoPago\Client\Payment\PaymentClient();

            $payment = $client->create([
                "transaction_amount" => (float)$amount,
                "token"              => $token,
                "description"        => "Pago del pedido #{$idPedido}",
                "installments"       => (int)$installments,
                "payment_method_id"  => $methodId,
                "issuer_id"          => $issuerId,
                "payer" => [
                    "email" => $_SESSION['usuario']['email_usuario'] ?? "cliente@mizzastore.com"
                ]
            ]);

            $estadoMP  = $payment->status;
            $paymentId = $payment->id;

            // Registro inicial y actualización del estado
            $this->pagoModel->crearPagoPendiente($idPedido, $amount);
            $this->pagoModel->actualizarPaymentId($idPedido, (string)$paymentId);
            $this->pagoModel->actualizarEstadoPago((string)$paymentId, (string)$estadoMP);

            if ($estadoMP === "approved") {
                $this->actualizarEstadoPedido($idPedido, 12); // Confirmado
            }

            echo json_encode([
                'success'     => true,
                'estado'      => $estadoMP,
                'payment_id'  => $paymentId
            ]);
            return;

        } catch (\Exception $e) {

            file_put_contents(
                "logs_pago_api.txt",
                "\n[" . date("Y-m-d H:i:s") . "] ERROR: " . $e->getMessage() . "\n",
                FILE_APPEND
            );

            echo json_encode([
                'success' => false,
                'message' => "Error interno al procesar el pago."
            ]);
            return;
        }
    }

    // ==========================================================
    // 2) Webhook de MercadoPago (confirmaciones externas)
    // ==========================================================
    public function webhookMP()
    {
        $raw  = file_get_contents("php://input");
        $data = json_decode($raw, true);

        file_put_contents(
            "logs_mp_webhook.txt",
            "[" . date("Y-m-d H:i:s") . "] → " . $raw . "\n",
            FILE_APPEND
        );

        $paymentId = $data['data']['id'] ?? ($_GET['id'] ?? null);
        $type      = $data['type'] ?? ($_GET['type'] ?? null);

        if (!$paymentId || $type !== "payment") {
            http_response_code(200);
            echo "IGNORED";
            return;
        }

        try {
            MercadoPagoConfig::setAccessToken("TEST-6187758628847843-112518-2b28778f119e91b6b4250df796e75264-3016728834");

            $client  = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            $estadoMP = $payment->status;

            // Actualizar estado de pago en BD
            $this->pagoModel->actualizarEstadoPago((string)$paymentId, (string)$estadoMP);

            // Obtener pedido asociado
            $stmt = $this->db->prepare("SELECT id_pedido FROM pago WHERE payment_mp_id = :pid ORDER BY id_pago DESC LIMIT 1");
            $stmt->bindParam(':pid', $paymentId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $idPedido = (int)$row['id_pedido'];

                if ($estadoMP === "approved") {
                    $this->actualizarEstadoPedido($idPedido, 12);
                } elseif (in_array($estadoMP, ["rejected", "cancelled", "charged_back"])) {
                    $this->actualizarEstadoPedido($idPedido, 11);
                }
            }

            http_response_code(200);
            echo "OK";
            return;
        } catch (\Exception $e) {
            error_log("WEBHOOK ERROR → " . $e->getMessage());
            http_response_code(500);
            echo "ERROR";
            return;
        }
    }

    // ==========================================================
    // 3) Actualizar estado del pedido desde controlador
    // ==========================================================
    private function actualizarEstadoPedido($idPedido, $estado)
    {
        $sql = "UPDATE pedido 
                SET id_estado_logico = :estado 
                WHERE id_pedido = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
    }

    // ==========================================================
    // 4) Confirmación visual de pago + generación PDF + email
    // ==========================================================
    public function confirmacionPago()
    {
        Sesion::iniciar();

        if (empty($_GET['id_pedido'])) {
            echo "<div class='alert alert-danger text-center mt-5'>Pedido no válido.</div>";
            return;
        }

        $idPedido = (int)$_GET['id_pedido'];

        $pedido = $this->pedidoModel->obtenerPedidoCompleto($idPedido);
        $pago   = $this->pagoModel->obtenerPagoCompletoPorPedido($idPedido);

        if (!$pedido || !$pago) {
            echo "<div class='alert alert-danger text-center mt-5'>No se pudo cargar la información del pago.</div>";
            return;
        }

        if ($pago['estado_pago'] === 'completado') {
            $this->generarComprobantePDF($pedido, $pago);
            $this->enviarMailConfirmacion($pedido, $pago);
        }

        $data = [
            'pedido' => $pedido,
            'pago'   => $pago
        ];

        $vista = 'views/pago/confirmacion_pago.php';
        require_once 'views/layouts/main.php';
    }

    // ==========================================================
    // 5) Generación del PDF de comprobante (Dompdf)
    // ==========================================================
    private function generarComprobantePDF(array $pedido, array $pago)
    {
        $dompdf = new Dompdf();

        ob_start();
        include 'views/pago/comprobante_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $carpeta = "views/pago/pdf_generados/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $filePath = $carpeta . "comprobante_{$pedido['id_pedido']}.pdf";
        file_put_contents($filePath, $dompdf->output());
    }

    // ==========================================================
    // 6) Enviar email con comprobante al cliente (PHPMailer)
    // ==========================================================
    private function enviarMailConfirmacion(array $pedido, array $pago)
    {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'milovargasb@gmail.com';
            $mail->Password   = 'xcbu fdze xqfl ieik';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@mizzastore.com', 'MizzaStore');
            $mail->addAddress($pedido['email_usuario']);

            $mail->isHTML(true);
            $mail->Subject = "Confirmación de pago - Pedido #{$pedido['id_pedido']}";

            $productosHtml = '';
            foreach ($pedido['detalles'] as $item) {
                $productosHtml .= "<tr>
                    <td>{$item['nombre_producto']}</td>
                    <td>{$item['cantidad_producto']}</td>
                    <td>$ {$item['precio_unitario']}</td>
                    <td>$ " . number_format($item['precio_unitario'] * $item['cantidad_producto'], 2) . "</td>
                </tr>";
            }

            $mail->Body = "
                <h2>Gracias por tu compra</h2>
                <p>Hola <strong>{$pedido['nombre_persona']} {$pedido['apellido_persona']}</strong>, tu pago fue aprobado.</p>
                <p>Pedido N° {$pedido['id_pedido']} | Fecha: {$pedido['fecha_pedido']}</p>
                <table border='1' cellpadding='5' cellspacing='0' width='100%'>{$productosHtml}</table>
            ";

            $rutaPDF = "views/pago/pdf_generados/comprobante_{$pedido['id_pedido']}.pdf";
            if (file_exists($rutaPDF)) {
                $mail->addAttachment($rutaPDF);
            }

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error enviando mail: " . $e->getMessage());
            return false;
        }
    }

    // ==========================================================
    // 7) Descargar comprobante PDF
    // ==========================================================
    public function descargarComprobante()
    {
        Sesion::iniciar();

        if (empty($_GET['id_pedido'])) {
            echo "<div class='alert alert-danger text-center mt-5'>Pedido no válido.</div>";
            return;
        }

        $idPedido = (int)$_GET['id_pedido'];
        $rutaPDF = "views/pago/pdf_generados/comprobante_{$idPedido}.pdf";

        if (!file_exists($rutaPDF)) {
            echo "<div class='alert alert-warning text-center mt-5'>
                No se encontró el comprobante del pedido #{$idPedido}.<br>
                Asegúrate de que el pago esté confirmado y se haya generado el PDF.
            </div>";
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Comprobante_Pedido_' . $idPedido . '.pdf"');
        readfile($rutaPDF);
        exit;
    }
}
