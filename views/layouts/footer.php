<style>
    .footer {
        background: linear-gradient(180deg, #2C0703 0%, #1B0402 100%);
        color: #EBD4CB;
        padding: 55px 0 25px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        letter-spacing: 0.3px;
        border-top: 1px solid rgba(235, 212, 203, 0.15);
        position: relative;
    }

    /* Efecto decorativo superior */
    .footer::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, rgba(233, 204, 194, 0.3), rgba(235, 212, 203, 0.1), rgba(233, 204, 194, 0.3));
    }

    /* --- Columnas --- */
    .footer-col {
        text-align: center;
        margin-bottom: 30px;
    }

    @media (min-width: 768px) {
        .footer-col {
            text-align: left;
        }
    }

    /* --- Logo --- */
    .footer-logo {
        width: 65px;
        /* más pequeño y sutil */
        filter: drop-shadow(0 0 3px rgba(235, 212, 203, 0.25));
        opacity: 0.9;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .footer-logo:hover {
        transform: scale(1.08);
        opacity: 1;
    }

    /* --- Descripción --- */
    .footer-desc {
        color: #EBD4CB;
        line-height: 1.7;
        margin-top: 15px;
        font-size: 13px;
        opacity: 0.9;
        max-width: 300px;
    }

    /* --- Títulos --- */
    .footer-title {
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 14px;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    /* --- Enlaces --- */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 7px;
    }

    .footer-links a {
        text-decoration: none;
        color: #EBD4CB;
        font-weight: 300;
        transition: color 0.3s ease, transform 0.2s ease;
        display: inline-flex;
        align-items: center;
    }

    .footer-links a:hover {
        color: #fff;
        transform: translateX(3px);
    }

    /* --- Íconos redes sociales --- */
    .footer-links i {
        margin-right: 8px;
        color: #EBD4CB;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
    }

    .footer-links a:hover i {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }

    /* --- Separador --- */
    .footer-separator {
        border: none;
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
        margin: 30px 0;
    }

    /* --- Copyright --- */
    .footer .copyright {
        text-align: center;
        color: #EBD4CB;
        font-size: 12.5px;
        opacity: 0.8;
        letter-spacing: 0.4px;
    }

    /* --- Responsividad --- */
    @media (max-width: 768px) {
        .footer {
            text-align: center;
        }

        .footer-logo {
            margin: 0 auto 10px;
        }

        .footer-desc {
            margin: 0 auto 20px;
            max-width: 85%;
        }

        .footer-title {
            margin-top: 20px;
        }
    }
</style>

<footer class="footer">
    <div class="container">
        <div class="row align-items-start text-center text-md-start">

            <!-- Columna 1: Logo y descripción -->
            <div class="col-md-4 footer-col d-flex flex-column align-items-center align-items-md-start">
                <img src="/MizzaStore/assets/images/logo2.png" alt="Mizza Logo" class="footer-logo mb-3">
                <p class="footer-desc">
                    Que la comodidad y los beneficios de la belleza, inspirados en las últimas tendencias globales, sean accesibles para todos en Argentina.
                </p>
            </div>

            <!-- Columna 2: Información -->
            <div class="col-md-4 footer-col">
                <h3 class="footer-title">¿Querés saber más?</h3>
                <ul class="footer-links">
                    <li><a href="#">Cupones de descuento</a></li>
                    <li><a href="#">Mizza Blog</a></li>
                    <li><a href="#">Influencers Partners</a></li>
                    <li><a href="#">Cosméticos</a></li>
                </ul>
            </div>

            <!-- Columna 3: Redes sociales -->
            <div class="col-md-4 footer-col">
                <h3 class="footer-title">¡Seguinos en nuestras redes sociales!</h3>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-facebook"></i>Facebook</a></li>
                    <li><a href="#"><i class="bi bi-twitter"></i>Twitter</a></li>
                    <li><a href="#"><i class="bi bi-instagram"></i>Instagram</a></li>
                    <li><a href="#"><i class="bi bi-youtube"></i>YouTube</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-separator">
        <p class="copyright mb-0">Mizza Store 2025 – Formosa, Argentina.</p>
    </div>
</footer>