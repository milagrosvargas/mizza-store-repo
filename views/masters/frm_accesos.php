<style>
/* 🎨 PALETA DE COLORES
   Bordó: #7a1c4b
   Rosa medio: #d94b8c
   Rosa claro: #f9e2ec
   Texto oscuro: #2b1a1f
*/

/* === CONTENEDOR GENERAL === */
.contenedor-accesos {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2b1a1f;
    background: #fff;
    padding: 30px 20px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* === Encabezado === */
.contenedor-accesos h2 {
    text-align: left;
    margin-bottom: 10px;
    font-weight: 600;
    color: #7a1c4b;
    letter-spacing: 0.3px;
    font-size: 1.6rem;
}

.contenedor-accesos .subtitulo {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 25px;
    line-height: 1.4;
}

/* === Tabla de accesos (matriz) === */
.tabla-accesos {
    width: 100%;
    overflow-x: auto;
    margin-bottom: 20px;
}

#tablaAccesos {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-size: 0.95rem;
    min-width: 700px; /* asegurar scroll cuando muchas columnas */
}

#tablaAccesos th,
#tablaAccesos td {
    padding: 10px 14px;
    border-bottom: 1px solid #eee;
}

#tablaAccesos th {
    background-color: #f9e2ec;
    color: #7a1c4b;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 1;
}

#tablaAccesos td:first-child {
    text-align: left;
    font-weight: 500;
    color: #2b1a1f;
    background: #fafafa;
}

#tablaAccesos tbody tr:hover td {
    background-color: #f9e2ec;
}

/* === Toggle Switch Estilo Unificado === */
.switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0;
    right: 0; bottom: 0;
    background-color: #ccc;
    transition: .3s;
    border-radius: 34px;
    box-shadow: inset 0 0 4px rgba(0,0,0,0.1);
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

input:checked + .slider {
    background-color: #d94b8c;
}

input:checked + .slider:before {
    transform: translateX(22px);
}

input:disabled + .slider {
    background-color: #e0e0e0;
    cursor: not-allowed;
}

.btn-primario {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #d94b8c, #7a1c4b);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 9px 16px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: all 0.25s ease-in-out;
    text-decoration: none;
}

.btn-primario:hover {
    background: linear-gradient(135deg, #7a1c4b, #d94b8c);
    box-shadow: 0 0 6px rgba(122,28,75,0.3);
}

@keyframes aparecer {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<div class="contenedor-accesos">
    <h2>Autorización de permisos a perfiles</h2>
    <p class="subtitulo">
        Asigna o revoca accesos de módulos a los distintos perfiles del sistema.
    </p>

    <div class="tabla-accesos">
        <table id="tablaAccesos">
            <thead>
                <tr id="theadModulos">
                    <th>Perfil</th>
                    <!-- Las columnas de módulos se generarán dinámicamente vía JS -->
                </tr>
            </thead>
            <tbody id="tbodyAccesos">
                <!-- Las filas (perfiles) y toggles se generarán dinámicamente vía JS -->
            </tbody>
        </table>
    </div>
</div>

<script type="module" src="assets/js/accesos.js"></script>
