<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --pink: #e06388;
            --pink-light: #f8b8cc;
            --rose-soft: #fde4ec;
            --rose-hover: #ec7fa2;
            --dark: #2d2d2d;
            --light: #ffffff;
        }

        body {
            background-color: var(--rose-soft);
            color: var(--dark);
        }

        .perfil-container {
            max-width: 850px;
            margin: 50px auto;
            background: var(--light);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .perfil-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .perfil-header h2 {
            color: var(--pink);
            font-weight: 700;
        }

        .perfil-header p {
            color: var(--dark);
            font-size: 0.95rem;
        }

        .perfil-section {
            margin-bottom: 25px;
        }

        .perfil-section h5 {
            color: var(--pink);
            border-bottom: 2px solid var(--pink-light);
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .perfil-section i {
            font-size: 1.3rem;
            color: var(--pink);
        }

        .perfil-dato {
            margin-bottom: 10px;
        }

        .perfil-label {
            font-weight: 600;
            color: var(--dark);
        }

        .divider {
            height: 2px;
            background: var(--pink-light);
            margin: 25px 0;
            border-radius: 2px;
        }

        /* ===============================
           BOTÓN ANIMADO (Hover Fill)
           =============================== */
        .btn-editar {
            background-color: transparent;
            color: var(--pink);
            border: 2px solid var(--pink);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            position: relative;
            overflow: hidden;
            transition: color 0.4s ease;
            z-index: 1;
        }

        .btn-editar::before {
            content: "";
            position: absolute;
            background: var(--pink);
            border-radius: 50px;
            height: 100%;
            width: 0;
            top: 0;
            left: 0;
            z-index: -1;
            transition: width 0.4s ease;
        }

        .btn-editar:hover::before {
            width: 100%;
        }

        .btn-editar:hover {
            color: var(--light);
        }

        .btn-editar i {
            margin-right: 6px;
        }

        @media (max-width: 768px) {
            .perfil-container {
                padding: 25px;
            }
        }

        /* ===============================
           BOTÓN ANIMADO (Hover Fill)
           =============================== */
        .btn-seguridad {
            background-color: transparent;
            color: #2954d4ff;
            border: 2px solid #2954d4ff;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            position: relative;
            overflow: hidden;
            transition: color 0.4s ease;
            z-index: 1;
        }

        .btn-seguridad::before {
            content: "";
            position: absolute;
            background: #2954d4ff;
            border-radius: 50px;
            height: 100%;
            width: 0;
            top: 0;
            left: 0;
            z-index: -1;
            transition: width 0.4s ease;
        }

        .btn-seguridad:hover::before {
            width: 100%;
        }

        .btn-seguridad:hover {
            color: var(--light);
        }

        .btn-seguridad i {
            margin-right: 6px;
        }

        /* Mensajes de error de validación */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            color: #dc3545;
            margin-top: 3px;
        }
    </style>
</head>

<body>

    <div class="perfil-container">
        <div class="perfil-header">
            <h2><i class="bi bi-person-heart"></i> Ver mi información</h2>
            <p>Se unió el <?= htmlspecialchars($datosPerfil["fecha_registro"]) ?></p>
        </div>

        <!-- Usuario -->
        <div class="perfil-section">
            <h5><i class="bi bi-person-badge"></i> Información de usuario</h5>
            <div class="perfil-dato">
                <span class="perfil-label">Nombre de usuario</span>
                <?= htmlspecialchars($datosPerfil["nombre_usuario"]) ?>
            </div>
            <div class="perfil-dato">
                <span class="perfil-label">Perfil:</span>
                <?= htmlspecialchars($datosPerfil["descripcion_perfil"]) ?>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Persona -->
        <div class="perfil-section">
            <h5><i class="bi bi-person-vcard"></i> Datos personales</h5>
            <div class="perfil-dato">
                <span class="perfil-label">Nombre completo</span>
                <?= htmlspecialchars($datosPerfil["nombre_completo"]) ?>
            </div>
            <div class="perfil-dato">
                <span class="perfil-label">Fecha de nacimiento</span>
                <?= htmlspecialchars($datosPerfil["fecha_nacimiento"] ?? '-') ?>
            </div>
            <div class="perfil-dato">
                <span class="perfil-label">Género </span>
                <?= htmlspecialchars($datosPerfil["genero"] ?? '-') ?>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Contacto -->
        <div class="perfil-section">
            <h5><i class="bi bi-envelope-at"></i> Información de contacto</h5>
            <div class="perfil-dato">
                <span class="perfil-label">Tipo</span>
                <?= htmlspecialchars($datosPerfil["tipo_contacto"] ?? '-') ?>
            </div>
            <div class="perfil-dato">
                <span class="perfil-label">Contacto </span>
                <?= htmlspecialchars($datosPerfil["contacto"] ?? '-') ?>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Documento -->
        <div class="perfil-section">
            <h5><i class="bi bi-card-text"></i> Documento</h5>
            <div class="perfil-dato">
                <span class="perfil-label">Documento </span>
                <?= htmlspecialchars($datosPerfil["documento"] ?? '-') ?>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Domicilio -->
        <div class="perfil-section">
            <h5><i class="bi bi-geo-alt"></i> Domicilio</h5>
            <div class="perfil-dato">
                <span class="perfil-label">Dirección </span>
                <?= htmlspecialchars($datosPerfil["direccion_completa"] ?? '-') ?>
            </div>
        </div>

        <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <!-- Botón Modificar -->
            <button type="button" class="btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                <i class="bi bi-pencil-square"></i> Modificar
            </button>

            <!-- Botón Seguridad de la cuenta -->
            <a href="index.php?controller=MiPerfil&action=verFrmSeguridad" class="btn-seguridad">
                <i class="bi bi-shield-lock"></i> Seguridad de la cuenta
            </a>


        </div>

        <!-- ============================================================
     MODAL: EDITAR PERFIL DE USUARIO
     ============================================================ -->
        <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header" style="background-color: var(--pink); color: var(--light); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        <h5 class="modal-title" id="modalEditarPerfilLabel">
                            <i class="bi bi-pencil-square"></i> Editar información de perfil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body p-4">
                        <form id="formEditarPerfil" novalidate>
                            <div class="row g-3">
                                <!-- ====================================================
                 DATOS PERSONALES
                 ==================================================== -->
                                <h6 class="text-uppercase text-secondary fw-bold"><i class="bi bi-person-vcard"></i> Datos personales</h6>

                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" placeholder="Ingrese su nombre" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" placeholder="Ingrese su apellido" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Fecha de nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nac" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Género</label>
                                    <select class="form-select" name="genero" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">Masculino</option>
                                        <option value="2">Femenino</option>
                                        <option value="3">Otro</option>
                                    </select>
                                </div>

                                <hr class="mt-4 mb-2">

                                <!-- ====================================================
                 DOMICILIO
                 ==================================================== -->
                                <h6 class="text-uppercase text-secondary fw-bold"><i class="bi bi-geo-alt"></i> Domicilio</h6>

                                <div class="col-md-6">
                                    <label class="form-label">País</label>
                                    <select id="pais" class="form-select" name="pais" required></select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Provincia</label>
                                    <select id="provincia" class="form-select" name="provincia" disabled required></select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Localidad</label>
                                    <select id="localidad" class="form-select" name="localidad" disabled required></select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Barrio</label>
                                    <select id="barrio" class="form-select" name="barrio" disabled required></select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Calle</label>
                                    <input type="text" class="form-control" name="calle_direccion" placeholder="Ej: Av. Siempre Viva" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Número</label>
                                    <input type="text" class="form-control" name="numero_direccion" placeholder="Ej: 742" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Piso (opcional)</label>
                                    <input type="text" class="form-control" name="piso_direccion" placeholder="Ej: 2°A">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Información adicional</label>
                                    <input type="text" class="form-control" name="info_extra_direccion" placeholder="Entre calles o referencia">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-editar" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button class="btn-editar" type="submit" form="formEditarPerfil">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="module" src="/MizzaStore/assets/js/mi_perfil.js"></script>
</body>

</html>