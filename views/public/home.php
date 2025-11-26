<head>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fff8f9;
      color: #333;
    }

    h1,
    h2,
    h3,
    h4 {
      font-weight: 600;
      text-align: center;
    }

    a {
      text-decoration: none;
      color: #555;
    }

    p {
      color: #555;
    }

    .small-container {
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }

    .row {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      justify-content: space-around;
    }

    .hero {
      background: linear-gradient(to right, #fce4ec, #f8bbd0);
      padding: 80px 20px;
      text-align: center;
      color: #B6465F;
    }

    .hero h1 {
      font-size: 2.5rem;
      margin-bottom: 15px;
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 600px;
      margin: 0 auto 20px;
    }

    .hero .btn {
      display: inline-block;
      background: #e91e63;
      color: #fff;
      padding: 10px 25px;
      border-radius: 30px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .hero .btn:hover {
      background: #c2185b;
      color: #fff;
    }

    .col-2 {
      flex-basis: 50%;
      min-width: 300px;
    }

    .col-2 img {
      max-width: 100%;
      padding: 50px 0;
      border-radius: 10px;
    }

    .col-2 h1 {
      font-size: 2.5rem;
      line-height: 1.2;
      margin: 25px 0;
    }

    .btn {
      display: inline-block;
      background: #890620;
      color: #fff;
      padding: 10px 25px;
      margin: 20px 0;
      border-radius: 30px;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #5e0f07ff;
      color: #fff;
    }

    .categories {
      margin: 5px 0;
    }

    .col-3 {
      flex-basis: 30%;
      min-width: 250px;
      margin-bottom: 30px;
    }

    .col-3 img {
      width: 50%;
      border-radius: 10px;
      transition: transform 0.3s;
    }

    .col-3 img:hover {
      transform: scale(1.05);
    }

    .title {
      text-align: center;
      margin: 60px auto 30px;
      position: relative;
      font-size: 1.8rem;
      color: #e91e63;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .title::after {
      content: '';
      background: #B6465F;
      width: 80px;
      height: 5px;
      border-radius: 5px;
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
    }

    .col-4 {
      flex-basis: 23%;
      padding: 10px;
      margin-bottom: 50px;
      transition: transform 0.4s;
      text-align: center;
    }

    .col-4 img {
      width: 100%;
      border-radius: 10px;
    }

    .col-4 h4 {
      margin-top: 10px;
      font-weight: 500;
      color: #333;
    }

    .col-4:hover {
      transform: translateY(-5px);
    }

    .exclusivo {
      background: radial-gradient(#fff, #ffd6d6);
      margin-top: 80px;
      padding: 50px 0;
    }

    .exclusivo .col-2 img {
      padding: 30px;
      border-radius: 10px;
    }

    small {
      color: #555;
    }

    .articulos-blog {
      background: #fffafc;
      padding: 60px 0;
    }

    .articulos-blog .col-3 {
      text-align: center;
      padding: 20px;
    }

    .articulos-blog i {
      display: block;
      font-style: italic;
      color: #e91e63;
      margin-bottom: 10px;
    }

    .articulos-blog img {
      width: 80px;
      border-radius: 50%;
      margin-top: 15px;
    }

    .metodo-pago {
      margin: 100px auto;
      text-align: center;
    }

    .col-5 {
      width: 160px;
      margin: 10px;
    }

    .col-5 img {
      width: 100%;
      filter: grayscale(100%);
      transition: filter 0.3s;
    }

    .col-5 img:hover {
      filter: grayscale(0);
    }

    @media (max-width: 992px) {
      .col-4 {
        flex: 1 1 45%;
      }

      .col-3 {
        flex: 1 1 45%;
      }
    }

    @media (max-width: 600px) {

      .col-4,
      .col-3,
      .col-2 {
        flex: 1 1 100%;
        text-align: center;
      }

      .title {
        font-size: 1.4rem;
      }
    }

    .category-card {
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      cursor: pointer;
    }

    .category-card img {
      width: 100%;
      border-radius: 10px;
      transition: transform 0.5s ease;
    }

    .category-card:hover img {
      transform: scale(1.1);
      filter: brightness(70%);
    }

    .overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      font-size: 1.5rem;
      font-weight: 600;
      background: rgba(0, 0, 0, 0.4);
      opacity: 0;
      transition: opacity 0.4s ease;
      backdrop-filter: blur(3px);
      border-radius: 10px;
    }

    .category-card:hover .overlay {
      opacity: 1;
    }
  </style>
</head>

<body>
  <!-- Hero -->
  <section class="hero">
    <h1>Descubrí tu belleza natural</h1>
    <p>Productos de maquillaje y cuidado facial inspirados en vos. Probá lo mejor en cosmética internacional, ¡todo en un solo lugar!</p>
  </section>

  <!-- Novedades -->
  <section class="small-container">
    <div class="row">
      <div class="col-2">
        <h1>Novedades<br>Verano 2024</h1>
        <p>Encontrá los productos de belleza más virales en TikTok e Instagram, incluidos los esenciales para tu rutina diaria.</p>
        <a href="#" class="btn">Enterate de más &#8594;</a>
      </div>
      <div class="col-2">
        <img src="assets/images/image1.png" alt="Novedades verano 2024">
      </div>
    </div>
  </section>

  <!-- Categorías -->
  <section class="categories">
    <div class="small-container">
      <h2 class="title">Categorías</h2>
      <div class="row">

        <div class="col-3">
          <div class="category-card category-item" data-categoria="Skincare">
            <img src="assets/images/category-1.png" alt="Cuidado facial">
            <div class="overlay">Skincare</div>
          </div>
        </div>

        <div class="col-3">
          <div class="category-card category-item" data-categoria="Brochas y pinceles">
            <img src="assets/images/category-2.png" alt="Maquillaje">
            <div class="overlay">Brochas y pinceles</div>
          </div>
        </div>

        <div class="col-3">
          <div class="category-card category-item" data-categoria="Maquillaje">
            <img src="assets/images/category-3.png" alt="Accesorios">
            <div class="overlay">Maquillaje</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Productos -->
  <section class="small-container">
    <h2 class="title">Productos más buscados</h2>
    <div class="row">
      <div class="col-4"><img src="assets/images/producto1.png" alt="">
        <h4>The Ordinary - Niamicida</h4>
        <p>$27.500</p>
      </div>
      <div class="col-4"><img src="assets/images/producto2.jpg" alt="">
        <h4>Summer Friday - Bálsamos</h4>
        <p>$80.000</p>
      </div>
      <div class="col-4"><img src="assets/images/producto3.png" alt="">
        <h4>Makeup By Mario</h4>
        <p>$52.000</p>
      </div>
      <div class="col-4"><img src="assets/images/producto4.png" alt="">
        <h4>LANEIGE - Mascarilla</h4>
        <p>$37.500</p>
      </div>
    </div>
  </section>

  <h2 class="title">Últimas Novedades</h2>
  <div class="row">
    <div class="col-4">
      <img src="assets/images/producto5.png">
      <h4>PGlow Recipe - Mascarilla Hidratante</h4>
      <p>$10.300</p>
    </div>
    <div class="col-4">
      <img src="assets/images/producto6.png">
      <h4>PATRICK TA - Sombra de Ojos Duo</h4>
      <p>$62.000</p>
    </div>
    <div class="col-4">
      <img src="assets/images/producto7.png">
      <h4>Fenty Beauty - Lip Gloss</h4>
      <p>$30.000</p>
    </div>
    <div class="col-4">
      <img src="assets/images/producto8.png">
      <h4>DIOR - Óleo Labial</h4>
      <p>$63.000</p>
    </div>
  </div>

  </div>

  <!-- Exclusivo -->
  <section class="exclusivo">
    <div class="small-container">
      <div class="row">
        <div class="col-2"><img src="assets/images/rubor-dior.png" alt="Rubor Dior Rosy Glow"></div>
        <div class="col-2">
          <p>Exclusivo en Mizza Store</p>
          <h1>Dior Rosy Glow Blush</h1>
          <small>Rubor viral tono 001 Pink con acabado natural y modulable.</small><br>
          <a href="#" class="btn">Conseguilo ahora &#8594;</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Métodos de pago -->
  <section class="metodo-pago">
    <div class="small-container">
      <h2 class="title">Métodos de pago</h2>
      <div class="row">
        <div class="col-5"><img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa"></div>
        <div class="col-5"><img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard"></div>
        <div class="col-5"><img src="https://imgs.search.brave.com/olfW7NjPa9rh6duaMopaDcVu3OBFRGnaUIM1VTQ4Wx8/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9sb2dv/ZG93bmxvYWQub3Jn/L3dwLWNvbnRlbnQv/dXBsb2Fkcy8yMDE0/LzEwL3BheXBhbC1s/b2dvLTAucG5n" alt="PayPal"></div>
        <div class="col-5"><img src="https://imgs.search.brave.com/-tKACwDxY3MsGTJPuUS0vXiIMZh9GNBrAUC_QiNsEJg/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWFn/ZXMuc2Vla2xvZ28u/Y29tL2xvZ28tcG5n/LzM0LzIvbWVyY2Fk/by1wYWdvLWxvZ28t/cG5nX3NlZWtsb2dv/LTM0MjM0Ny5wbmc" alt="MercadoPago"></div>
        <div class="col-5"><img src="https://imgs.search.brave.com/pTlmZUhHiqbfN4MOD8m8nB40rNeAg2GzRHWuzvuMvs8/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4t/aWNvbnMtcG5nLmZy/ZWVwaWsuY29tLzI1/Ni81OTUwLzU5NTA0/NDcucG5nP3NlbXQ9/YWlzX3doaXRlX2xh/YmVs" alt="Transferencia"></div>
      </div>
    </div>
  </section>
    <script>
    // Redirección dinámica por categoría
    document.addEventListener('DOMContentLoaded', function() {
      const categorias = document.querySelectorAll('.category-item');

      categorias.forEach(cat => {
        cat.addEventListener('click', () => {
          const nombreCategoria = cat.dataset.categoria || '';
          if (!nombreCategoria) return;

          // Crear slug a partir del nombre
          const slug = nombreCategoria
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '');

          // Redirigir con slug
          const url = `index.php?controller=Catalogo&action=cosmeticos&categoria=${slug}`;
          window.location.href = url;
        });
      });
    });
  </script>
</body>