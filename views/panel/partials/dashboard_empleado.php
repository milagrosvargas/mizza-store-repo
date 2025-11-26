
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Empleado - MizzaStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --rosa-claro: #fce4ec;
            --rosa-principal: #f48fb1;
            --rosa-oscuro: #ec407a;
            --gris-texto: #4a4a4a;
            --blanco: #ffffff;
        }

        body {
            background: var(--rosa-claro);
            font-family: 'Segoe UI', sans-serif;
            color: var(--gris-texto);
        }

        h2 {
            font-weight: bold;
            color: var(--rosa-oscuro);
            text-align: center;
            margin-bottom: 2rem;
        }

        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            border: none;
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(90deg, var(--rosa-principal), var(--rosa-oscuro));
            color: var(--blanco);
            font-weight: 600;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .container-custom {
            padding: 40px 20px;
        }

        @media (max-width: 768px) {
            .container-custom {
                padding: 20px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="row">

        <!-- Estadísticas de pedidos con Chart.js -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">Estado actual de pedidos</div>
                <div class="card-body">
                    <canvas id="graficoEstadosPedidos" width="400" height="200"></canvas>
                    <p class="mt-3 text-muted small">
                        Visualización en tiempo real de pedidos según su estado: pendiente, en camino o entregado.
                    </p>
                </div>
            </div>
        </div>

        <!-- Acceso a gestión de envíos -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Gestión de pedidos</div>
                <div class="card-body">
                    <p class="card-text">Desde aquí podrás ver y actualizar el estado de los pedidos asociados a envíos.</p>
                    <a href="index.php?controller=Envio&action=listarPedidosView" class="btn btn-primary w-100">
                        Ir a gestión de envíos
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('graficoEstadosPedidos').getContext('2d');

    // Inicializamos el gráfico vacío
    const chartPedidos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pendientes', 'En Camino', 'Entregados'],
            datasets: [{
                label: 'Cantidad de pedidos',
                data: [0, 0, 0],
                backgroundColor: ['#f48fb1', '#ec407a', '#ad1457'],
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Función para actualizar datos desde el backend
    function cargarEstadisticas() {
        fetch('index.php?controller=Envio&action=obtenerEstadisticasEnvio')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    chartPedidos.data.datasets[0].data = [
                        data.data.pendiente,
                        data.data['en camino'],
                        data.data.entregado
                    ];
                    chartPedidos.update();
                }
            })
            .catch(error => console.error('Error al cargar estadísticas:', error));
    }

    // Llamada inicial
    cargarEstadisticas();

    // Actualiza cada 30 segundos
    setInterval(cargarEstadisticas, 30000);
});
</script>


</body>
</html>
