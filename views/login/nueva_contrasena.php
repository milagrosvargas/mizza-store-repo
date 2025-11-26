<style>
    body {
        background-color: #f5f5f5;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: stretch;
        max-width: 850px;
        margin: 35px auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        animation: fadeIn 0.6s ease-in-out;
    }

    .login-form {
        flex: 1;
        padding: 50px 40px;
        text-align: center;
        background: #fff;
        border-top-left-radius: 16px;
        border-bottom-left-radius: 16px;
    }

    .login-form h1.brand-title {
        font-size: 28px;
        font-weight: 700;
        color: #d94b8c;
        margin-bottom: 5px;
    }

    .login-form h2 {
        font-size: 22px;
        color: #333;
        margin-bottom: 25px;
    }

    .mb-3 {
        margin-bottom: 20px;
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1.5px solid #ccc;
        font-size: 15px;
        outline: none;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #d94b8c;
        box-shadow: 0 0 6px rgba(217, 75, 140, 0.3);
    }

    /* Botón del ojito */
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #888;
        padding: 0;
    }

    .toggle-password:hover {
        color: #d94b8c;
    }

    .btn-login {
        display: inline-block;
        width: 100%;
        background: linear-gradient(135deg, #d94b8c, #f097b5);
        color: white;
        padding: 12px 0;
        border-radius: 8px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(217, 75, 140, 0.3);
    }

    .extra-links {
        margin-top: 20px;
    }

    .link-secondary {
        display: inline-block;
        font-size: 14px;
        color: #777;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .link-secondary:hover {
        color: #d94b8c;
    }

    .login-banner {
        flex: 1;
        background: linear-gradient(135deg, #d94b8c, #f097b5);
        color: white;
        text-align: center;
        padding: 50px 25px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .banner-logo {
        width: 110px;
        margin-bottom: 20px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .login-banner h3 {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .login-banner p {
        font-size: 15px;
        line-height: 1.5;
        margin: 10px 0 25px;
    }

    .btn-volver-inicio {
        background: white;
        color: #d94b8c;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-volver-inicio:hover {
        background: #fbe3ec;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            width: 90%;
        }

        .login-banner {
            order: -1;
            padding: 40px 20px;
            border-radius: 0;
        }

        .login-form {
            padding: 40px 25px;
            border-radius: 0;
        }

        .btn-login {
            padding: 10px;
        }
    }
</style>

<div class="login-container">
    <div class="login-form">
        <h1 class="brand-title">MizzaStore</h1>
        <h2>Establecer nueva contraseña</h2>

        <form id="formNuevaContrasena" autocomplete="off" novalidate>
            <input type="hidden" id="token" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

            <div class="mb-3">
                <input type="password" id="password" name="password" class="form-control" placeholder="Nueva contraseña" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password', this)">👁</button>
            </div>

            <div class="mb-3">
                <input type="password" id="password2" name="password2" class="form-control" placeholder="Confirmar contraseña" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password2', this)">👁</button>
            </div>

            <button type="submit" class="btn-login">Actualizar contraseña</button>
        </form>

        <div class="extra-links">
            <a href="index.php?controller=Login&action=login" class="link-secondary">Volver al inicio de sesión</a>
        </div>
    </div>

    <div class="login-banner">
        <img src="/MizzaStore/assets/images/logo2.png" alt="Logo MizzaStore" class="banner-logo">
        <h3>Restablecé tu acceso</h3>
        <p>Ingresá una nueva contraseña segura para tu cuenta.</p>
        <a href="index.php?controller=Home&action=index" class="btn-volver-inicio">Volver al inicio</a>
    </div>
</div>

<!-- Scripts -->
<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            btn.textContent = "🚫";
        } else {
            input.type = "password";
            btn.textContent = "👁";
        }
    }
</script>

<script type="module" src="/MizzaStore/assets/js/nueva_contrasena.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
