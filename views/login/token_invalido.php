<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación | MizzaStore</title>
    <link rel="icon" href="assets/images/logo2.png" type="image/png">
</head>
<div class="login-container">
    <div class="login-form">
        <h1 class="brand-title">MizzaStore</h1>
        <div class="icon-error">❌</div>
        <h2>Enlace inválido o expirado</h2>

        <p class="message">
            El enlace de recuperación de contraseña que intentaste usar no es válido, ya fue utilizado o ha expirado.
        </p>

        <div class="extra-links">
            <a href="index.php?controller=Login&action=restablecerContrasena" class="btn-login">
                Solicitar un nuevo enlace
            </a>
            <br>
            <a href="index.php?controller=Login&action=login" class="link-secondary">
                Volver al inicio de sesión
            </a>
        </div>
    </div>

    <div class="login-banner">
        <img src="/MizzaStore/assets/images/logo2.png" alt="Logo MizzaStore" class="banner-logo">
        <h3>Token inválido</h3>
        <p>Por seguridad, los enlaces de recuperación expiran después de una hora o si ya se utilizaron.</p>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 🩶 Mostrar alerta elegante con SweetAlert
        Swal.fire({
            icon: 'error',
            title: 'Enlace inválido o expirado',
            text: 'Por seguridad, los enlaces de recuperación tienen una duración limitada o pueden haber sido utilizados previamente.',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#d94b8c',
            background: '#fff',
            color: '#333',
            allowOutsideClick: false,
        });
    });
</script>

<style>
    /* ====== ESTILO GENERAL ====== */
    body {
        background: linear-gradient(135deg, #fbe3ec 0%, #fff 100%);
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    /* ====== CONTENEDOR PRINCIPAL ====== */
    .login-container {
        display: flex;
        flex-wrap: wrap;
        width: 900px;
        background: #ffffff;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        overflow: hidden;
        animation: fadeIn 0.8s ease-in-out;
    }

    /* ====== FORMULARIO ====== */
    .login-form {
        flex: 1;
        padding: 50px;
        text-align: center;
        background: #fff;
    }

    .login-form h1 {
        font-size: 28px;
        font-weight: 700;
        color: #d94b8c;
        margin-bottom: 10px;
    }

    .icon-error {
        font-size: 50px;
        color: #ff4b4b;
        margin: 10px 0;
        animation: pulse 1.5s infinite;
    }

    .login-form h2 {
        font-size: 22px;
        color: #333;
        margin-bottom: 15px;
    }

    .login-form .message {
        font-size: 15px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    /* ====== BOTONES ====== */
    .btn-login {
        display: inline-block;
        background: #d94b8c;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background: #b83c76;
    }

    .link-secondary {
        display: inline-block;
        margin-top: 10px;
        font-size: 14px;
        color: #777;
        text-decoration: none;
    }

    .link-secondary:hover {
        color: #d94b8c;
    }

    /* ====== BANNER ====== */
    .login-banner {
        flex: 1;
        background: linear-gradient(135deg, #d94b8c, #f097b5);
        color: #fff;
        text-align: center;
        padding: 50px 20px;
    }

    .login-banner img.banner-logo {
        width: 120px;
        height: auto;
        margin-bottom: 20px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .login-banner h3 {
        font-size: 22px;
        font-weight: 600;
    }

    .login-banner p {
        font-size: 15px;
        margin: 15px 0 25px;
        line-height: 1.6;
    }

    .btn-volver-inicio {
        display: inline-block;
        background: #fff;
        color: #d94b8c;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-volver-inicio:hover {
        background: #fbe3ec;
    }

    /* ====== ANIMACIONES ====== */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.9;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0.9;
        }
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            width: 90%;
        }

        .login-banner {
            order: -1;
            border-radius: 0 0 20px 20px;
        }
    }
</style>