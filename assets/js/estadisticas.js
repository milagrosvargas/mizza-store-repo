let chartInstance = null;

/* =======================================================
   1) LISTAR PERÍODOS (HTML dentro de #resultados-periodo)
   ======================================================= */
function listarPeriodos() {
    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo períodos registrados',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('index.php?controller=Periodo&action=listar')
        .then(response => response.text())
        .then(html => {
            Swal.close();
            document.getElementById('resultados-periodo').innerHTML = html;
        })
        .catch(error => {
            Swal.fire('Error', 'No se pudieron cargar los períodos', 'error');
            console.error(error);
        });
}

/* =======================================================
   2) CREAR NUEVO PERÍODO (Modal + SweetAlert)
   ======================================================= */
function guardarPeriodo() {
    const form = document.getElementById('formNuevoPeriodo');
    const formData = new FormData(form);

    const nombre = formData.get('nombre_periodo').trim();
    const inicio = formData.get('fecha_inicio');
    const fin = formData.get('fecha_fin');

    if (!nombre || !inicio || !fin) {
        Swal.fire('Atención', 'Debe completar todos los campos.', 'warning');
        return;
    }

    if (new Date(inicio) > new Date(fin)) {
        Swal.fire('Error', 'La fecha de inicio no puede ser mayor que la fecha fin.', 'error');
        return;
    }

    Swal.fire({
        title: 'Guardando...',
        text: 'Registrando nuevo período',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('index.php?controller=Periodo&action=crear', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire('Éxito', 'Período registrado correctamente.', 'success');
                form.reset();
                listarPeriodos();
            } else {
                Swal.fire('Error', data.message || 'No se pudo guardar el período', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Ocurrió un problema al crear el período.', 'error');
            console.error(err);
        });
}

/* =========================================================================
   3) CONSULTAR ESTADÍSTICAS GENERALES (MÁS VENDIDOS)
   ========================================================================= */
function consultarEstadisticas() {
    const idPeriodo = document.getElementById('selectPeriodo').value;

    if (!idPeriodo) {
        Swal.fire('Atención', 'Seleccione un período válido.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Generando estadísticas...',
        text: 'Obteniendo productos más vendidos',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`index.php?controller=Periodo&action=estadisticas&id_periodo=${idPeriodo}`)
        .then(res => res.json())
        .then(data => {
            Swal.close();

            if (!data || data.length === 0) {
                Swal.fire('Sin datos', 'No hay estadísticas disponibles para este período.', 'info');
                document.getElementById('graficoVentas').style.display = 'none';
                document.getElementById('resultados-periodo').innerHTML =
                    '<div class="alert alert-warning mt-3">No hay datos para este período.</div>';
                return;
            }

            mostrarTablaEstadisticas(data);
            generarGrafico(data);
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudieron obtener las estadísticas.', 'error');
            console.error(err);
        });
}

/* =======================================================
   4) TABLA PARA MÁS VENDIDOS
   ======================================================= */
function mostrarTablaEstadisticas(data) {
    let html = `
        <h5 class="mt-4 text-success">Productos más vendidos</h5>
        <div class="table-responsive mt-2">
            <table class="table table-hover tabla-periodos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Total vendido</th>
                        <th>Total recaudado</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.nombre_producto}</td>
                <td>${item.total_vendido}</td>
                <td>$${parseFloat(item.total_recaudado).toFixed(2)}</td>
            </tr>
        `;
    });

    html += `</tbody></table></div>`;
    document.getElementById('resultados-periodo').innerHTML = html;
}

/* =======================================================
   5) GRÁFICO BARRAS - PRODUCTOS MÁS VENDIDOS
   ======================================================= */
function generarGrafico(data) {
    const nombres = data.map(item => item.nombre_producto);
    const cantidades = data.map(item => item.total_vendido);

    const ctx = document.getElementById('graficoVentas').getContext('2d');
    document.getElementById('graficoVentas').style.display = 'block';

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Unidades Vendidas',
                data: cantidades,
                backgroundColor: 'rgba(182,70,95,0.3)',
                borderColor: 'rgba(182,70,95,1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                title: { display: true, text: 'Productos más vendidos por período' }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}

/* =========================================================================
   6) CONSULTAR PRODUCTOS MENOS VENDIDOS (Pie/Doughnut)
   ========================================================================= */
function consultarMenosVendidos() {
    const idPeriodo = document.getElementById('selectPeriodo').value;
    if (!idPeriodo) {
        Swal.fire('Atención', 'Seleccione un período válido.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Analizando...',
        text: 'Buscando productos menos vendidos',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`index.php?controller=Periodo&action=estadisticas&id_periodo=${idPeriodo}`)
        .then(res => res.json())
        .then(data => {
            Swal.close();

            if (!data || data.length === 0) {
                Swal.fire('Sin datos', 'Este período no tiene ventas registradas.', 'info');
                document.getElementById('graficoVentas').style.display = 'none';
                return;
            }

            data.sort((a, b) => a.total_vendido - b.total_vendido);

            mostrarTablaMenosVendidos(data);
            generarGraficoMenosVendidos(data);
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudieron obtener los datos.', 'error');
            console.error(err);
        });
}

/* =======================================================
   7) TABLA PARA MENOS VENDIDOS
   ======================================================= */
function mostrarTablaMenosVendidos(data) {
    let html = `
        <h5 class="mt-4 text-danger">Productos menos vendidos</h5>
        <div class="table-responsive mt-2">
            <table class="table table-hover tabla-periodos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Total vendido</th>
                        <th>Total recaudado</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.slice(0, 5).forEach(item => {
        html += `
            <tr>
                <td>${item.nombre_producto}</td>
                <td>${item.total_vendido}</td>
                <td>$${parseFloat(item.total_recaudado).toFixed(2)}</td>
            </tr>
        `;
    });

    html += `</tbody></table></div>`;
    document.getElementById('resultados-periodo').innerHTML = html;
}

/* =======================================================
   8) GRÁFICO DONUT / PIE - MENOS VENDIDOS
   ======================================================= */
function generarGraficoMenosVendidos(data) {
    const nombres = data.slice(0, 5).map(item => item.nombre_producto);
    const cantidades = data.slice(0, 5).map(item => item.total_vendido);

    const ctx = document.getElementById('graficoVentas').getContext('2d');
    document.getElementById('graficoVentas').style.display = 'block';

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Menos Vendidos',
                data: cantidades,
                backgroundColor: [
                    'rgba(182,70,95,0.6)',
                    'rgba(255,159,64,0.6)',
                    'rgba(255,205,86,0.6)',
                    'rgba(75,192,192,0.6)',
                    'rgba(153,102,255,0.6)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Productos menos vendidos (Doughnut)' }
            }
        }
    });
}
/* =========================================================================
   CONSULTAR CATEGORÍAS MÁS VENDIDAS
   ========================================================================= */
function consultarCategorias() {
    const idPeriodo = document.getElementById('selectPeriodo').value;
    if (!idPeriodo) {
        Swal.fire('Atención', 'Seleccione un período primero.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Cargando...',
        text: 'Analizando categorías más vendidas',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`index.php?controller=Periodo&action=estadisticasCategorias&id_periodo=${idPeriodo}`)
        .then(res => res.json())
        .then(data => {
            Swal.close();

            if (!data || data.length === 0) {
                Swal.fire('Sin datos', 'No hay ventas para este período.', 'info');
                return;
            }

            mostrarTablaCategorias(data);
            generarGraficoCategorias(data);
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudieron obtener las categorías.', 'error');
            console.error(err);
        });
}

/* ====== Mostrar tabla de categorías ====== */
function mostrarTablaCategorias(data) {
    let html = `
        <h5 class="mt-4 text-info">Categorías más vendidas</h5>
        <div class="table-responsive mt-2">
            <table class="table table-hover tabla-periodos">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Total vendido</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.nombre_categoria}</td>
                <td>${item.total_vendido}</td>
            </tr>
        `;
    });

    html += `</tbody></table></div>`;
    document.getElementById('resultados-periodo').innerHTML = html;
}

/* ====== Generar gráfico de categorías ====== */
function generarGraficoCategorias(data) {
    const nombres = data.map(item => item.nombre_categoria);
    const cantidades = data.map(item => item.total_vendido);

    const ctx = document.getElementById('graficoVentas').getContext('2d');
    document.getElementById('graficoVentas').style.display = 'block';

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Categorías más vendidas',
                data: cantidades,
                backgroundColor: [
                    'rgba(255,99,132,0.6)',
                    'rgba(54,162,235,0.6)',
                    'rgba(255,206,86,0.6)',
                    'rgba(75,192,192,0.6)',
                    'rgba(153,102,255,0.6)',
                    'rgba(255,159,64,0.6)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Categorías más vendidas'
                }
            }
        }
    });
}
/* =========================================================================
   CONSULTAR SUBCATEGORÍAS MÁS VENDIDAS
   ========================================================================= */
function consultarSubcategorias() {
    const idPeriodo = document.getElementById('selectPeriodo').value;
    if (!idPeriodo) {
        Swal.fire('Atención', 'Seleccione un período válido.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo subcategorías más vendidas',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`index.php?controller=Periodo&action=estadisticasSubcategorias&id_periodo=${idPeriodo}`)
        .then(res => res.json())
        .then(data => {
            Swal.close();

            if (!data || data.length === 0) {
                Swal.fire('Sin datos', 'No hay ventas por subcategoría en este período.', 'info');
                document.getElementById('graficoVentas').style.display = 'none';
                return;
            }

            mostrarTablaSubcategorias(data);
            generarGraficoSubcategorias(data);
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudieron obtener los datos.', 'error');
            console.error(err);
        });
}

/* ====== Tabla de subcategorías ====== */
function mostrarTablaSubcategorias(data) {
    let html = `
        <h5 class="mt-4 text-primary">Subcategorías más vendidas</h5>
        <div class="table-responsive mt-2">
            <table class="table table-hover tabla-periodos">
                <thead>
                    <tr>
                        <th>Subcategoría</th>
                        <th>Total vendido</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.nombre_sub_categoria}</td>
                <td>${item.total_vendido}</td>
            </tr>
        `;
    });

    html += `</tbody></table></div>`;
    document.getElementById('resultados-periodo').innerHTML = html;
}

/* ====== Gráfico (Pie Chart) ====== */
function generarGraficoSubcategorias(data) {
    const nombres = data.map(item => item.nombre_sub_categoria);
    const cantidades = data.map(item => item.total_vendido);

    const ctx = document.getElementById('graficoVentas').getContext('2d');
    document.getElementById('graficoVentas').style.display = 'block';

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Subcategorías más vendidas',
                data: cantidades,
                backgroundColor: [
                    'rgba(153,102,255,0.6)',
                    'rgba(255,159,64,0.6)',
                    'rgba(54,162,235,0.6)',
                    'rgba(255,99,132,0.6)',
                    'rgba(75,192,192,0.6)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Subcategorías más vendidas'
                }
            }
        }
    });
}