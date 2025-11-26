<head>
    <title>Sobre Mizza Store</title>
    <style>
        /* Configuración base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #fff;
            overflow-x: hidden;
            background-color: #000;
        }

        /* Contenedor del video */
        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .background-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            filter: brightness(65%) saturate(110%);
        }

        /* Overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(233, 30, 99, 0.2) 0%, rgba(0, 0, 0, 0.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 25px;
            backdrop-filter: blur(3px);
        }

        .overlay h1 {
            font-size: 3rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            margin-bottom: 20px;
            position: relative;
        }

        .overlay h1::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            margin: 10px auto 0;
            background-color: #ff80ab;
            border-radius: 2px;
        }

        .overlay p {
            max-width: 700px;
            line-height: 1.7;
            font-size: 1.1rem;
            font-weight: 300;
            color: #fce4ec;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            animation: fadeIn 2s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Botón CTA (opcional) */
        .overlay a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background-color: #e91e63;
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 1px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .overlay a:hover {
            background-color: #ad1457;
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .overlay h1 {
                font-size: 2.2rem;
            }

            .overlay p {
                font-size: 1rem;
                padding: 0 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Sección principal con video -->
    <div class="video-container">
        <iframe
            class="background-video"
            src="https://www.youtube.com/embed/3VwWcfCUCWs?autoplay=1&mute=1&controls=0&rel=0&loop=1&playlist=3VwWcfCUCWs"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            title="Korean Idol Makeup Tutorial Soft, Glam & Aesthetic">
        </iframe>

        <div class="overlay">
            <h1>Sobre Mizza</h1>
            <p>
                Creemos que la belleza prospera en la diversidad y el descubrimiento.
                Nuestro propósito es ampliar la forma en que el mundo ve la belleza,
                potenciando lo extraordinario que hay en cada uno de nosotros.
            </p>
            <a href="index.php?controller=Home&action=cosmeticos">Conocé nuestros productos</a>
        </div>
    </div>
</body>