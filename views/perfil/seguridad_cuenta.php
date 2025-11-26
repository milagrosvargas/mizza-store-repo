<!-- ==============================================
     VISTA: Seguridad de la cuenta
     ============================================== -->
<div class="container py-5">
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 600px; border-radius: 15px;">
        <div class="card-header text-white text-center py-3" style="background-color: #e06388; border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Seguridad de la cuenta</h4>
        </div>

        <div class="card-body px-4 py-4">
            <p class="text-muted text-center mb-4">
                Cambiá tu contraseña para mantener tu cuenta protegida.
            </p>

            <form id="formSeguridad" autocomplete="off">
                <div class="mb-3">
                    <label for="actual" class="form-label fw-semibold">Contraseña actual</label>
                    <input type="password" class="form-control" id="actual" name="actual" placeholder="Ingrese su contraseña actual">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="nueva" class="form-label fw-semibold">Nueva contraseña</label>
                    <input type="password" class="form-control" id="nueva" name="nueva" placeholder="Ingrese la nueva contraseña">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="confirmar" class="form-label fw-semibold">Confirmar nueva contraseña</label>
                    <input type="password" class="form-control" id="confirmar" name="confirmar" placeholder="Repita la nueva contraseña">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap">
                    <button type="submit" class="btn-actualizar">
                        <i class="bi bi-check-circle"></i> Actualizar contraseña
                    </button>
                    <a href="index.php?controller=MiPerfil&action=verFrmPerfil" class="btn-volver">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/MizzaStore/assets/js/seguridad_cuenta.js"></script>
<script src="/MizzaStore/assets/js/toast_notificaciones.js"></script>

<!-- ==============================================
     ESTILOS PERSONALIZADOS
     ============================================== -->
<style>
    .btn-actualizar {
        background-color: #e06388;
        color: #fff;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 50px;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.4s ease;
        z-index: 1;
    }

    .btn-actualizar::before {
        content: "";
        position: absolute;
        background: #c04d70;
        border-radius: 50px;
        height: 100%;
        width: 0;
        top: 0;
        left: 0;
        z-index: -1;
        transition: width 0.4s ease;
    }

    .btn-actualizar:hover::before {
        width: 100%;
    }

    .btn-actualizar:hover {
        color: #fff;
    }

    .btn-volver {
        background-color: transparent;
        border: 2px solid #e06388;
        color: #e06388;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 50px;
        transition: all 0.4s ease;
    }

    .btn-volver:hover {
        background-color: #e06388;
        color: #fff;
    }
</style>
