<style>
    :root {
        --bordo: #7a1c4b;
        --rosa: #d94b8c;
        --rosa-claro: #f9e2ec;
        --texto: #2b1a1f;
        --gris: #ece7ea;
        --fondo: #fafafa;
        --error: #d9534f;
    }

    .contenedor-cliente {
        width: 100%;
        max-width: 720px;
        margin: 40px auto;
        background: #fff;
        padding: 50px 60px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        box-sizing: border-box;
    }

    h2 {
        text-align: center;
        font-weight: 600;
        color: var(--bordo);
        margin-bottom: 35px;
        letter-spacing: 0.3px;
    }

    h2 i {
        font-size: 1.4rem;
        color: var(--rosa);
        margin-right: 8px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .fila {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    @media (max-width: 720px) {
        .fila {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .contenedor-cliente {
            padding: 40px 25px;
        }
    }

    .campo {
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .campo input,
    .campo select {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px 12px 12px 40px;
        font-size: 15px;
        background: #fff;
        outline: none;
        transition: all 0.25s ease;
    }

    .campo input:focus,
    .campo select:focus {
        border-color: var(--rosa);
        box-shadow: 0 0 0 3px rgba(217, 75, 140, 0.1);
    }

    .campo label {
        position: absolute;
        left: 40px;
        top: 12px;
        color: #777;
        font-size: 15px;
        pointer-events: none;
        transition: all 0.2s ease;
        background: #fff;
    }

    .campo input:focus+label,
    .campo input:not(:placeholder-shown)+label,
    .campo select:focus+label,
    .campo select:not([value=""])+label {
        top: -10px;
        left: 35px;
        font-size: 13px;
        color: var(--bordo);
        background: #fff;
        padding: 0 4px;
    }

    .campo i {
        position: absolute;
        top: 13px;
        left: 12px;
        color: var(--rosa);
        font-size: 1.1rem;
    }

    .campo.error input,
    .campo.error select {
        border-color: var(--error);
    }

    .campo.error label {
        color: var(--error);
    }

    .documento-grupo {
        position: relative;
    }

    .documento-input {
        display: flex;
        gap: 8px;
        align-items: center;
        position: relative;
    }

    .documento-input select,
    .documento-input input {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px 12px 12px 38px;
        font-size: 15px;
        transition: all 0.25s ease;
        width: 100%;
    }

    .documento-input select:focus,
    .documento-input input:focus {
        border-color: var(--rosa);
        box-shadow: 0 0 0 3px rgba(217, 75, 140, 0.1);
    }

    .documento-input select {
        flex: 1.2;
    }

    .documento-input input {
        flex: 2;
    }

    .documento-grupo label {
        position: absolute;
        left: 38px;
        top: -10px;
        font-size: 13px;
        color: var(--bordo);
        background: #fff;
        padding: 0 4px;
        z-index: 1;
    }

    .documento-grupo i {
        position: absolute;
        top: 13px;
        left: 12px;
        color: var(--rosa);
        font-size: 1.1rem;
        z-index: 2;
    }

    .mensaje-error {
        display: block;
        margin-top: 3px;
        color: var(--error);
        font-size: 13px;
    }

    .btn-primario {
        align-self: center;
        border: none;
        padding: 14px 32px;
        background: linear-gradient(135deg, var(--rosa), var(--bordo));
        color: #fff;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 0.4px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(122, 28, 75, 0.25);
        transition: all 0.3s ease;
    }

    .btn-primario:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(122, 28, 75, 0.35);
    }
</style>

<div class="contenedor-cliente">
    <h2><i class="bi bi-person-plus-fill"></i>Nueva cuenta</h2>

    <form id="formCliente" method="POST" action="index.php?controller=Cliente&action=guardar" novalidate>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-person"></i>
                <input type="text" id="nombre" name="nombre" required maxlength="50" autocomplete="given-name" placeholder=" ">
                <label for="nombre">Nombre</label>
            </div>

            <div class="campo">
                <i class="bi bi-person"></i>
                <input type="text" id="apellido" name="apellido" required maxlength="50" autocomplete="family-name" placeholder=" ">
                <label for="apellido">Apellido</label>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-calendar-event"></i>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required placeholder=" " max="2007-11-02">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
            </div>

            <div class="campo">
                <i class="bi bi-gender-ambiguous"></i>
                <select id="genero" name="genero" required>
                    <option value="" selected disabled hidden>Seleccione género</option>
                    <?php if (!empty($generos)): ?>
                        <?php foreach ($generos as $g): ?>
                            <option value="<?= htmlspecialchars($g['id_genero']) ?>">
                                <?= htmlspecialchars($g['nombre_genero']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="genero">Género</label>
            </div>
        </div>

        <div class="campo documento-grupo">
            <i class="bi bi-credit-card-2-front"></i>
            <div class="documento-input">
                <select id="tipo_documento" name="tipo_documento" required>
                    <option value="" selected disabled hidden>Seleccionar</option>
                    <?php if (!empty($tiposDocumento)): ?>
                        <?php foreach ($tiposDocumento as $td): ?>
                            <option value="<?= htmlspecialchars($td['id_tipo_documento']) ?>"
                                data-codigo="<?= htmlspecialchars($td['nombre_tipo_documento']) ?>">
                                <?= htmlspecialchars($td['nombre_tipo_documento']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <input type="text" id="numero_documento" name="numero_documento" placeholder="Ingrese su documento" required maxlength="20" autocomplete="off">
            </div>
            <label for="tipo_documento">Documento</label>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-telephone"></i>
                <input type="tel" id="telefono" name="telefono" required maxlength="15" autocomplete="tel" placeholder=" ">
                <label for="telefono">Teléfono</label>
            </div>

            <div class="campo">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email" required maxlength="100" autocomplete="email" placeholder=" ">
                <label for="email">Correo electrónico</label>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-globe"></i>
                <select id="pais" name="pais" required>
                    <option value="" selected disabled hidden>Seleccione país</option>
                    <?php if (!empty($paises)): ?>
                        <?php foreach ($paises as $p): ?>
                            <option value="<?= htmlspecialchars($p['id_pais']) ?>">
                                <?= htmlspecialchars($p['nombre_pais']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="pais">País</label>
            </div>

            <div class="campo">
                <i class="bi bi-map"></i>
                <select id="provincia" name="provincia" required>
                    <option value="" selected disabled hidden>Seleccione provincia</option>
                </select>
                <label for="provincia">Provincia</label>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-geo-alt"></i>
                <select id="ciudad" name="ciudad" required>
                    <option value="" selected disabled hidden>Seleccione localidad</option>
                </select>
                <label for="ciudad">Localidad</label>
            </div>

            <div class="campo">
                <i class="bi bi-house"></i>
                <select id="barrio" name="barrio" required>
                    <option value="" selected disabled hidden>Seleccione barrio</option>
                </select>
                <label for="barrio">Barrio</label>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-signpost"></i>
                <input type="text" id="direccion" name="direccion" required maxlength="100" autocomplete="address-line1" placeholder=" ">
                <label for="direccion">Calle / Dirección</label>
            </div>

            <div class="campo">
                <i class="bi bi-building"></i>
                <input type="text" id="numero" name="numero" maxlength="10" autocomplete="address-line2" placeholder=" ">
                <label for="numero">Número</label>
            </div>
        </div>

        <div class="fila">
            <div class="campo">
                <i class="bi bi-lock"></i>
                <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder=" ">
                <label for="password">Contraseña</label>
            </div>

            <div class="campo">
                <i class="bi bi-lock-fill"></i>
                <input type="password" id="password2" name="password2" required minlength="6" autocomplete="new-password" placeholder=" ">
                <label for="password2">Confirmar contraseña</label>
            </div>
        </div>

        <small id="ayudaPassword" class="text-muted" style="display:block;margin-top:-10px;margin-bottom:10px;">
            Debe cumplir <strong>al menos una</strong> de estas reglas:
            (1) 8 dígitos numéricos, ó (2) 6+ caracteres con <em>una mayúscula</em> y <em>un símbolo</em> (!@#$%&*).
        </small>

        <input type="hidden" id="usuario" name="usuario" value="">

        <div class="acciones-form">
            <button type="submit" class="btn-primario">
                <i class="bi bi-check-circle"></i> Registrarme
            </button>
        </div>
    </form>
</div>

<script type="module" src="assets/js/cliente.js"></script>
<script type="module" src="assets/js/ubicaciones.js"></script>