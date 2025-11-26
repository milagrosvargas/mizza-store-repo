<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación | MizzaStore</title>
    <link rel="icon" href="assets/images/logo2.png" type="image/png">
    <style>
        body {
            background: #f8b8cc;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(122, 28, 75, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(122, 28, 75, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #d94b8c, #7a1c4b);
            color: #fff;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            padding: 25px 15px;
            letter-spacing: 0.5px;
        }

        .card-body {
            text-align: center;
            padding: 35px 40px 45px;
        }

        .icono {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: #7a1c4b;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-warning {
            color: #ffc107;
        }

        .texto {
            font-size: 1rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #d94b8c, #7a1c4b);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(122, 28, 75, 0.25);
        }

        .btn-volver:hover {
            opacity: 0.95;
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(122, 28, 75, 0.35);
        }
    </style>

<body>
    <div class="card">
        <div class="card-header"><?= htmlspecialchars($mensaje['titulo']) ?></div>
        <div class="card-body">
            <?php if ($mensaje['tipo'] === 'success'): ?>
                <div class="icono text-success"><i class="bi bi-check-circle-fill"></i></div>
            <?php elseif ($mensaje['tipo'] === 'error'): ?>
                <div class="icono text-danger"><i class="bi bi-x-circle-fill"></i></div>
            <?php else: ?>
                <div class="icono text-warning"><i class="bi bi-exclamation-circle-fill"></i></div>
            <?php endif; ?>

            <p class="texto"><?= $mensaje['texto'] ?></p>

            <?php if ($mensaje['tipo'] === 'success'): ?>
                <a href="index.php?controller=Login&action=login" class="btn-volver">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                </a>
            <?php else: ?>
                <a href="index.php?controller=Cliente&action=verFrmCliente" class="btn-volver">
                    <i class="bi bi-person-plus-fill"></i> Volver al registro
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>