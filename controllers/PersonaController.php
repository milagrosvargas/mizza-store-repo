<?php
// ============================================================
// CONTROLADOR: PersonaController
// ------------------------------------------------------------
// Controla las operaciones relacionadas con la persona del
// usuario logueado. Gestiona la lectura y actualización de los
// datos personales, domicilio y contacto.
// ============================================================

require_once "models/PersonaModel.php";
require_once "models/MasterModel.php";
require_once "core/Sesion.php";

class PersonaController
{
    private $personaModel;
    private $masterModel;

    // ============================================================
    // CONSTRUCTOR
    // ============================================================
    public function __construct()
    {
        $this->personaModel = new PersonaModel();
        $this->masterModel  = new MasterModel();

        // Requerir que haya un usuario activo antes de continuar
        Sesion::iniciar();
        if (!Sesion::usuarioAutenticado()) {
            // Si no hay sesión, forzamos modo invitado
            Sesion::inicializarInvitado();
        }
    }

    // ============================================================
    // MÉTODO AUXILIAR PARA RESPONDER EN JSON
    // ============================================================
    private function jsonResponse($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ============================================================
    // OBTENER PERFIL COMPLETO DE LA PERSONA LOGUEADA
    // ============================================================
    public function obtenerPerfil()
    {
        try {
            $usuario = Sesion::obtenerUsuario();

            if (!$usuario || empty($usuario['relacion_persona'])) {
                $this->jsonResponse([
                    "error" => "No se encontró una persona asociada a la sesión activa."
                ]);
            }

            $idPersona = $usuario['relacion_persona'];
            $persona = $this->personaModel->obtenerPorId($idPersona);

            if (!$persona) {
                $this->jsonResponse(["error" => "No se encontró la información de la persona."]);
            }

            // Devuelve los datos personales completos
            $this->jsonResponse([
                "success" => true,
                "persona" => $persona
            ]);
        } catch (Exception $e) {
            error_log("Error en PersonaController::obtenerPerfil -> " . $e->getMessage());
            $this->jsonResponse(["error" => "Error interno al obtener el perfil."]);
        }
    }

    // ============================================================
    // ACTUALIZAR DATOS PERSONALES
    // ============================================================
    public function actualizarDatosPersonales()
    {
        try {
            $usuario = Sesion::obtenerUsuario();
            $idPersona = $usuario['relacion_persona'] ?? null;

            $nombre   = trim($_POST["nombre_persona"] ?? "");
            $apellido = trim($_POST["apellido_persona"] ?? "");
            $fecha    = $_POST["fecha_nac_persona"] ?? null;
            $idGenero = $_POST["id_genero"] ?? null;

            // Validaciones
            if (!$idPersona || empty($nombre) || empty($apellido)) {
                $this->jsonResponse(["error" => "Datos personales incompletos o inválidos."]);
            }

            $resultado = $this->personaModel->actualizarDatos($idPersona, $nombre, $apellido, $fecha, $idGenero);

            $this->jsonResponse([
                "success" => $resultado,
                "mensaje" => $resultado
                    ? "✅ Datos personales actualizados correctamente."
                    : "❌ No se pudo actualizar los datos personales."
            ]);
        } catch (Exception $e) {
            error_log("Error en PersonaController::actualizarDatosPersonales -> " . $e->getMessage());
            $this->jsonResponse(["error" => "Error interno al actualizar los datos personales."]);
        }
    }

    // ============================================================
    // ACTUALIZAR DOMICILIO
    // ============================================================
    public function actualizarDomicilio()
    {
        try {
            $idDomicilio = $_POST["id_domicilio"] ?? null;
            $calle       = trim($_POST["calle_direccion"] ?? "");
            $numero      = trim($_POST["numero_direccion"] ?? "");
            $piso        = trim($_POST["piso_direccion"] ?? "");
            $info        = trim($_POST["info_extra_direccion"] ?? "");
            $idBarrio    = $_POST["id_barrio"] ?? null;

            if (!$idDomicilio || empty($calle) || empty($numero) || !$idBarrio) {
                $this->jsonResponse(["error" => "Datos de domicilio incompletos."]);
            }

            $resultado = $this->personaModel->actualizarDomicilio(
                $idDomicilio,
                $calle,
                $numero,
                $piso,
                $info,
                $idBarrio
            );

            $this->jsonResponse([
                "success" => $resultado,
                "mensaje" => $resultado
                    ? "✅ Domicilio actualizado correctamente."
                    : "❌ No se pudo actualizar el domicilio."
            ]);
        } catch (Exception $e) {
            error_log("Error en PersonaController::actualizarDomicilio -> " . $e->getMessage());
            $this->jsonResponse(["error" => "Error interno al actualizar el domicilio."]);
        }
    }

    // ============================================================
    // ACTUALIZAR CONTACTO
    // ============================================================
    public function actualizarContacto()
    {
        try {
            $idContacto     = $_POST["id_detalle_contacto"] ?? null;
            $descripcion    = trim($_POST["descripcion_contacto"] ?? "");
            $idTipoContacto = $_POST["id_tipo_contacto"] ?? null;

            if (!$idContacto || empty($descripcion) || !$idTipoContacto) {
                $this->jsonResponse(["error" => "Datos de contacto incompletos o inválidos."]);
            }

            $resultado = $this->personaModel->actualizarContacto(
                $idContacto,
                $descripcion,
                $idTipoContacto
            );

            $this->jsonResponse([
                "success" => $resultado,
                "mensaje" => $resultado
                    ? "✅ Contacto actualizado correctamente."
                    : "❌ No se pudo actualizar el contacto."
            ]);
        } catch (Exception $e) {
            error_log("Error en PersonaController::actualizarContacto -> " . $e->getMessage());
            $this->jsonResponse(["error" => "Error interno al actualizar el contacto."]);
        }
    }
}
?>
