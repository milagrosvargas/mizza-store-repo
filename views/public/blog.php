<style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    /* Sección de artículos del blog */
    .articulos-blog {
        padding: 20px 0;
        background: #fff7f8;
        text-align: center;
    }

    /* Título */
    .articulos-blog .title {
        font-size: 2.4rem;
        font-weight: 600;
        color: #2C0703;
        margin-bottom: 50px;
        position: relative;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }

    .articulos-blog .title::after {
        content: "";
        display: block;
        width: 90px;
        height: 3px;
        background: linear-gradient(90deg, #B6465F, #EBA1A6);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* Tarjetas de artículos */
    .articulos-blog .col-3 {
        background: #fff;
        border-radius: 18px;
        padding: 40px 25px;
        margin: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        text-align: center;
        cursor: pointer;
        position: relative;
    }

    .articulos-blog .col-3:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 30px rgba(182, 70, 95, 0.25);
    }

    /* Icono de cita */
    .fa.fa-quote-left {
        font-size: 34px;
        color: #B6465F;
        margin-bottom: 15px;
        display: block;
    }

    /* Texto del artículo */
    .articulos-blog .col-3 p {
        font-size: 14px;
        color: #555;
        margin: 12px 0 20px;
        line-height: 1.6;
        min-height: 80px;
    }

    /* Imagen de autor */
    .articulos-blog .col-3 img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 3px solid #EBD4CB;
        box-shadow: 0 0 0 3px rgba(182, 70, 95, 0.15);
        transition: all 0.3s ease;
    }

    .articulos-blog .col-3:hover img {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(182, 70, 95, 0.4);
    }

    /* Nombre del autor */
    .articulos-blog .col-3 h3 {
        font-weight: 600;
        color: #2C0703;
        font-size: 16px;
        margin-top: 5px;
    }

    /* Cita */
    .articulos-blog .col-3 i {
        font-style: normal;
        font-weight: 600;
        color: #B6465F;
        display: block;
        margin-bottom: 10px;
        font-size: 15px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .articulos-blog .title {
            font-size: 1.8rem;
        }

        .articulos-blog .col-3 {
            margin: 10px auto;
            width: 90%;
        }
    }
</style>

<div class="articulos-blog">
    <div class="small-container">
        <h2 class="title">¡Bienvenidx al blog de Mizza!</h2>
        <div class="row justify-content-center">

            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <i>¿El fin del Clean Girl look?<br>¿Qué se viene?</i>
                <p>El minimalismo se reinventa con toques metálicos y pieles luminosas. Te contamos qué estilos dominan las pasarelas del 2025.</p>
                <img src="assets/images/user-1.png" alt="Autora del artículo">
                <h3>Karen Pérez</h3>
            </div>

            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <i>Productos que marcaron la era de las E-Girl<br>(2020-2022)</i>
                <p>Brillos, delineadores gráficos y labios glossy: un repaso por la estética que marcó una generación digital y creativa.</p>
                <img src="assets/images/user-2.png" alt="Autora del artículo">
                <h3>Andrea Gutiérrez</h3>
            </div>

            <div class="col-3">
                <i class="fa fa-quote-left"></i>
                <i>Panteras y Vampiros:<br>El furor de Halloween</i>
                <p>Entre sombras y labiales oscuros, analizamos cómo el maquillaje gótico vuelve con fuerza y glamour en las noches de octubre.</p>
                <img src="assets/images/user-3.png" alt="Autora del artículo">
                <h3>Micaela Germano</h3>
            </div>
        </div>
    </div>
</div>