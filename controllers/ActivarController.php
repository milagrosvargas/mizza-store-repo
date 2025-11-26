<?php
require_once 'models/Conexion.php';

class ActivarController
{
    /**
     * Ruta: ?controller=activar&action=cuenta&token=xxx
     */
    public function cuenta()
    {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            $mensaje = [
                'tipo' => 'error',
                'titulo' => 'Token no válido',
                'texto' => 'No se ha proporcionado un token válido para la activación.'
            ];
            require 'views/login/resultado_activacion.php';
            return;
        }

        try {
            $db = (new Conexion())->Conectar();

            // 1. Buscar token válido
            $sql = "SELECT relacion_usuario, expiracion, usado
                    FROM tokens_usuario
                    WHERE token = :token AND tipo = 'activacion'";
            $stmt = $db->prepare($sql);
            $stmt->execute([':token' => $token]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tokenData) {
                $mensaje = [
                    'tipo' => 'error',
                    'titulo' => 'Token inválido',
                    'texto' => 'Este enlace no es válido o no corresponde a una activación.'
                ];
                require 'views/login/resultado_activacion.php';
                return;
            }

            // 2. Verificar expiración o uso
            if ($tokenData['usado'] == 1) {
                $mensaje = [
                    'tipo' => 'info',
                    'titulo' => 'Token ya utilizado',
                    'texto' => 'Este enlace ya fue usado anteriormente. Tu cuenta ya está activada.'
                ];
                require 'views/login/resultado_activacion.php';
                return;
            }

            if (strtotime($tokenData['expiracion']) < time()) {
                $mensaje = [
                    'tipo' => 'error',
                    'titulo' => 'Token expirado',
                    'texto' => 'El enlace de activación ha expirado. Debes solicitar uno nuevo.'
                ];
                require 'views/login/resultado_activacion.php';
                return;
            }

            $idUsuario = $tokenData['relacion_usuario'];

            // 3. Activar cuenta
            $sqlUpdate = "UPDATE usuario 
                          SET cuenta_activada = 1 
                          WHERE id_usuario = :id";
            $stmtUp = $db->prepare($sqlUpdate);
            $stmtUp->execute([':id' => $idUsuario]);

            // 4. Marcar token como usado
            $sqlTok = "UPDATE tokens_usuario SET usado = 1 WHERE token = :token";
            $stmtTok = $db->prepare($sqlTok);
            $stmtTok->execute([':token' => $token]);

            $mensaje = [
                'tipo' => 'success',
                'titulo' => 'Cuenta activada con éxito',
                'texto' => 'Tu cuenta ha sido activada correctamente. Ya puedes iniciar sesión.'
            ];
        } catch (Exception $e) {
            error_log("Error activando cuenta: " . $e->getMessage());
            $mensaje = [
                'tipo' => 'error',
                'titulo' => 'Error interno',
                'texto' => 'Ocurrió un problema al activar tu cuenta. Inténtalo nuevamente.'
            ];
        }

        require 'views/login/resultado_activacion.php';
    }
}
