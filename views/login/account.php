<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MizzaStore | Iniciar sesión</title>
    <link rel="icon" href="/MizzaStore/assets/images/logo2.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --pink: #e06388;
            --pink-light: #f8b8cc;
            --rose-soft: #fde4ec;
            --rose-hover: #f39ab5;
            --dark: #2d2d2d;
            --light: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(120deg, var(--pink-light), var(--rose-soft));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            background: var(--light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 950px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 🌸 Panel izquierdo (formulario) */
        .login-form {
            flex: 1;
            padding: 3rem 3rem 2rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--pink);
            text-align: center;
            margin-bottom: 0.2rem;
        }

        .login-form h2 {
            text-align: center;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 2rem;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid var(--pink-light);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 0.2rem rgba(224, 99, 136, 0.15);
        }

        .btn-login {
            width: 100%;
            background: var(--pink);
            border: none;
            color: var(--light);
            border-radius: 30px;
            padding: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--rose-hover);
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(224, 99, 136, 0.25);
        }

        /* 🔗 Enlaces de acción */
        .extra-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 1.8rem;
            font-size: 0.9rem;
        }

        .extra-links a {
            color: var(--pink);
            text-decoration: none;
            margin: 0.25rem 0;
            transition: color 0.3s ease;
        }

        .extra-links a:hover {
            color: var(--rose-hover);
            text-decoration: underline;
        }

        /* 🌺 Panel derecho (decorativo) */
        .login-banner {
            flex: 1;
            background: linear-gradient(150deg, var(--pink), var(--pink-light));
            color: var(--light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .login-banner img {
            width: 160px;
            margin-bottom: 1.5rem;
        }

        .login-banner h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .login-banner p {
            font-size: 0.95rem;
            max-width: 320px;
            margin-bottom: 1.5rem;
        }

        /* 🎀 Botón Volver al inicio (dentro del banner) */
        .btn-volver-inicio {
            display: inline-block;
            background: var(--light);
            color: var(--pink);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-volver-inicio:hover {
            background: var(--rose-hover);
            color: var(--light);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column-reverse;
                max-width: 420px;
            }

            .login-banner {
                padding: 1.5rem;
            }

            .login-banner img {
                width: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">

        <!-- Izquierda: formulario -->
        <div class="login-form">
            <h1>MizzaStore</h1>
            <h2>Iniciar sesión</h2>

            <form id="loginForm" autocomplete="off" novalidate>
                <div class="mb-3">
                    <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario o correo electrónico" required>
                </div>
                <div class="mb-3">
                    <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="Contraseña" required>
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>

            <div class="extra-links">
                <a href="index.php?controller=Cliente&action=verFrmCliente">¿No tienes cuenta? Regístrate</a>
                <a href="index.php?controller=Login&action=restablecerContrasena">¿Olvidaste tu contraseña?</a>
            </div>
        </div>

        <!-- Derecha: imagen / branding -->
        <div class="login-banner">
            <img src="/MizzaStore/assets/images/logo.png" alt="Logo MizzaStore">
            <h3>Bienvenidxs a MizzaStore</h3>
            <p>Descubre el universo de la belleza.</p>

            <a href="index.php?controller=Home&action=index" class="btn-volver-inicio">← Volver al inicio</a>
        </div>
    </div>

    <!-- ✅ Scripts -->
    <script type="module" src="/MizzaStore/assets/js/login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>