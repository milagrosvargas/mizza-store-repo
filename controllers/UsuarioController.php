<?php
require_once 'models/PersonaModel.php';
require_once 'models/MasterModel.php';
require_once 'core/Sesion.php';

class UsuarioController
{
    private $model;
    private $masterModel;

    public function __construct()
    {
        $this->model = new PersonaModel();
        $this->masterModel = new MasterModel();
    }

    /* =========================================================
       SECCIÓN: VISTA PRINCIPAL
       ========================================================= */

    // 📄 Cargar la vista del formulario de perfil
    public function miPerfil()
    {
        Sesion::iniciar();
        $vista = 'views/user/mi_perfil.php';
        require_once 'views/layouts/main.php';
    }

    /* =========================================================
       SECCIÓN: DATOS PERSONALES
       ========================================================= */

    // 🔍 Obtener los datos completos de la persona logueada
    public function obtenerPerfil()
    {
        Sesion::iniciar();
        $idPersona = $_SESSION['id_persona'] ?? 0;

        if ($idPersona <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sesión inválida o expirada.']);
            return;
        }

        $data = $this->model->obtenerPorId($idPersona);

        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'No se encontraron datos de la persona.']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    // ✏️ Actualizar los datos personales
    public function actualizarDatosPersonales()
    {
        Sesion::iniciar();
        $id = $_SESSION['id_persona'] ?? 0;

        $nombre  = trim($_POST['nombre_persona'] ?? '');
        $apellido = trim($_POST['apellido_persona'] ?? '');
        $fecha   = trim($_POST['fecha_nac_persona'] ?? '');
        $genero  = intval($_POST['id_genero'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sesión inválida.']);
            return;
        }

        if ($nombre === '' || $apellido === '' || $genero <= 0) {
            echo json_encode(['success' => false, 'message' => 'Complete todos los campos obligatorios.']);
            return;
        }

        $ok = $this->model->actualizarDatos($id, $nombre, $apellido, $fecha, $genero);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Datos personales actualizados correctamente.' : 'Error al actualizar los datos personales.'
        ]);
    }

    /* =========================================================
       SECCIÓN: DOMICILIO
       ========================================================= */

    // ✏️ Actualizar domicilio completo
    public function actualizarDomicilio()
    {
        Sesion::iniciar();
        $idPersona = $_SESSION['id_persona'] ?? 0;

        if ($idPersona <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sesión inválida.']);
            return;
        }

        $persona = $this->model->obtenerPorId($idPersona);
        $id_domicilio = $persona['domicilio']['id_domicilio'] ?? 0;

        if ($id_domicilio <= 0) {
            echo json_encode(['success' => false, 'message' => 'No se encontró domicilio asociado.']);
            return;
        }

        $calle  = trim($_POST['calle_direccion'] ?? '');
        $numero = trim($_POST['numero_direccion'] ?? '');
        $piso   = trim($_POST['piso_direccion'] ?? '');
        $info   = trim($_POST['info_extra_direccion'] ?? '');
        $barrio = intval($_POST['id_barrio'] ?? 0);

        if ($calle === '' || $barrio <= 0) {
            echo json_encode(['success' => false, 'message' => 'Debe completar calle y barrio.']);
            return;
        }

        $ok = $this->model->actualizarDomicilio($id_domicilio, $calle, $numero, $piso, $info, $barrio);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Domicilio actualizado correctamente.' : 'Error al actualizar el domicilio.'
        ]);
    }

    /* =========================================================
       SECCIÓN: CONTACTO
       ========================================================= */

    // ✏️ Actualizar teléfono o correo electrónico
    public function actualizarContacto()
    {
        Sesion::iniciar();
        $idPersona = $_SESSION['id_persona'] ?? 0;

        if ($idPersona <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sesión inválida.']);
            return;
        }

        $persona = $this->model->obtenerPorId($idPersona);
        $id_contacto = $persona['contacto']['id_detalle_contacto'] ?? 0;

        if ($id_contacto <= 0) {
            echo json_encode(['success' => false, 'message' => 'No se encontró contacto asociado.']);
            return;
        }

        $descripcion = trim($_POST['descripcion_contacto'] ?? '');
        $tipo = intval($_POST['id_tipo_contacto'] ?? 0);

        if ($descripcion === '' || $tipo <= 0) {
            echo json_encode(['success' => false, 'message' => 'Complete los campos obligatorios.']);
            return;
        }

        $ok = $this->model->actualizarContacto($id_contacto, $descripcion, $tipo);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Contacto actualizado correctamente.' : 'Error al actualizar el contacto.'
        ]);
    }

    /* =========================================================
       SECCIÓN: COMBOS / SELECTS
       ========================================================= */

    public function obtenerGenerosSelect()
    {
        $data = $this->masterModel->obtenerGeneros()['datos'] ?? [];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function obtenerTiposContactoSelect()
    {
        $data = $this->masterModel->obtenerTiposContacto()['datos'] ?? [];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function obtenerBarriosSelect()
    {
        $data = $this->masterModel->obtenerBarrios()['datos'] ?? [];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /* =========================================================
       SECCIÓN: COMBOS ANIDADOS (Ubicación)
       ========================================================= */

    public function obtenerProvinciasPorPais()
    {
        $id_pais = intval($_POST['id_pais'] ?? 0);
        $data = $this->masterModel->obtenerProvinciasPorPais($id_pais);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function obtenerLocalidadesPorProvincia()
    {
        $id_provincia = intval($_POST['id_provincia'] ?? 0);
        $data = $this->masterModel->obtenerLocalidadesPorProvincia($id_provincia);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function obtenerBarriosPorLocalidad()
    {
        $id_localidad = intval($_POST['id_localidad'] ?? 0);
        $data = $this->masterModel->obtenerBarriosPorLocalidad($id_localidad);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
?>
