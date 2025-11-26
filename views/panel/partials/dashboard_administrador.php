<head>
    <style>
        :root {
            --rosado-claro: #fdf2f5;
            --rosado-medio: #f5b5c7;
            --rosado-profundo: #b6465f;
            --rosado-oscuro: #7d2e3e;
            --gris-texto: #555;
            --blanco: #ffffff;
        }

        body {
            background: var(--rosado-claro);
            font-family: 'Segoe UI', sans-serif;
            color: var(--gris-texto);
        }

        h2 {
            font-weight: 600;
            color: var(--rosado-profundo);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .card {
            border-radius: 14px;
            border: none;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(182, 70, 95, 0.07);
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(182, 70, 95, 0.12);
        }

        .card-header {
            background: linear-gradient(90deg, var(--rosado-medio), var(--rosado-profundo));
            color: var(--blanco);
            font-weight: 500;
            font-size: 1rem;
            padding: 10px 15px;
        }

        .card-body {
            padding: 20px;
        }

        .small-note {
            font-size: 0.85rem;
            color: #888;
            margin-top: 8px;
        }

        .btn-primary {
            background-color: var(--rosado-profundo);
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--rosado-oscuro);
        }

        /* Ajuste del gráfico */
        #graficoEstadosPedidos {
            max-width: 500px;
            height: 260px !important;
            display: block;
            margin: auto;
        }

        /* Imagen equilibrada dentro de la tarjeta */
        .envio-img {
            max-width: 85px;
            margin: 1rem auto;
            display: block;
            opacity: 0.93;
        }

        .card-body p {
            flex-grow: 1;
            display: flex;
            align-items: center;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="row justify-content-center">

            <!-- Estadísticas de pedidos -->
            <div class="col-md-7 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-bar-chart-fill me-2"></i>Estado de pedidos
                    </div>
                    <div class="card-body text-center">
                        <canvas id="graficoEstadosPedidos"></canvas>
                        <p class="small-note">Pedidos según su estado actual pendientes, en camino y entregados.</p>
                    </div>
                </div>
            </div>

            <!-- Gestión de envíos -->
            <div class="col-md-5 mb-4">
                <div class="card h-100 text-center d-flex flex-column">
                    <div class="card-header">
                        <i class="bi bi-truck me-2"></i>Envíos
                    </div>
                    <div class="card-body d-flex flex-column align-items-center">

                        <img src="assets/images/camion.png"
                            alt="Gestión de Envíos"
                            class="envio-img">

                        <p>Visualiza, gestiona y actualiza los pedidos listos para envío.</p>

                        <a href="index.php?controller=Envio&action=listarPedidosView"
                            class="btn btn-primary w-100 mt-auto">
                            Ir a gestión de envíos
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('graficoEstadosPedidos').getContext('2d');

            const chartPedidos = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pendientes', 'En camino', 'Entregados'],
                    datasets: [{
                        label: 'Cantidad',
                        data: [0, 0, 0],
                        backgroundColor: ['#f3a5b8', '#b6465f', '#7d2e3e'],
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function cargarEstadisticas() {
                fetch('index.php?controller=Envio&action=obtenerEstadisticasEnvio')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chartPedidos.data.datasets[0].data = [
                                data.data.pendiente || 0,
                                data.data['en camino'] || 0,
                                data.data.entregado || 0
                            ];
                            chartPedidos.update();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            cargarEstadisticas();
            setInterval(cargarEstadisticas, 30000);
        });
    </script>
</body>