<nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <!-- Logo -->
            <a class="navbar-brand me-3" href="index.php?controller=Panel&action=dashboard" title="Ir al panel principal">
                <img src="/MizzaStore/assets/images/w-logo.png"
                    alt="MizzaStore"
                    style="height: 45px; width: auto; border-radius: 8px;">
            </a>

            <!-- Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">

                    <!-- Módulos internos -->

                    <?php if (in_array('Catálogo', $modulos)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=Home&action=cosmeticos">Cósmeticos</a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Clientes', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Clientes -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Clientes</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=cliente&action=listar">Ir a sección</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=cliente&action=historial">Historial de compras</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=cliente&action=exportar">Exportar datos</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Cliente para clientes', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Clientes par historial de clientes -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Mis compras</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=cliente&action=verSeccionHistorial">Ir a sección</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Ventas', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Ventas -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Ventas</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=venta&action=listar">Ir a sección</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=venta&action=nueva">Exportar listado</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Inventario', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Inventario -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Inventario</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=Productos&action=verFrmProducto">Ir a sección</a></li>
                                <li><a class="dropdown-item" href="index.php?controller=Productos&action=exportarListadoProductos">Exportar listado de productos .xls</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Pedidos', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Pedidos -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pedidos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=Repartidor&action=ver">Estado de pedidos</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Blog interno', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <!-- Blog -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=blog&action=listar">Artículos públicados</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Reportes -->
                    <?php if (in_array('Reportes', $modulos)) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Reportes</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?controller=Periodo&action=ver">Gráficos de estadísticas</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Configuración', $modulos)) : ?>
                        <li class="nav-item dropdown" data-bs-auto-close="outside">
                            <!-- Configuración -->
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-display="static">Configuración</a>
                            <ul class="dropdown-menu p-0">
                                <div class="dropdown-scroll px-2 py-2">
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmEstado">Estados lógicos</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmPais">Países</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmProvincia">Provincias</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmLocalidad">Localidades</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmBarrio">Barrios</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmTipoDoc">Tipos de documento</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmTipoCon">Tipos de contacto</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmPerfiles">Administrar roles</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmModulos">Accesos a módulos</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmAccesos">Autorización de permisos</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmGenero">Identidad de género</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmCategoria">Categorías</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmSubCategoria">Sub-categorías</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmMarca">Marcas</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmUnidadMedida">Unidad de medida</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmMetodoPago">Métodos de pagos</a></li>
                                    <li><a class="dropdown-item" href="index.php?controller=Master&action=verFrmTipoNota">Tipos de notas</a></li>
                                </div>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Acceso externo -->

                    <?php if (in_array('Home', $modulos)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=Home&action=index">Home</a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Cósmeticos', $modulos)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=Home&action=cosmeticos">Cósmeticos</a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Blog externo', $modulos)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=Home&action=blog">Mizza Blog</a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('Sobre nosotros', $modulos)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=Home&action=sobreNosotros">Sobre nosotros</a>
                        </li>
                    <?php endif; ?>


                    <!-- Buscador -->
                    <li class="nav-item">
                        <form class="search-container" method="get" action="index.php">
                            <input type="hidden" name="controller" value="catalogo">
                            <input type="hidden" name="action" value="buscar">
                            <input class="search-input" type="search" name="q" placeholder="Buscar productos..." aria-label="Buscar">
                            <button class="search-btn" type="submit" aria-label="Buscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </li>
                    
                    <!-- Carrito -->
                    <a href="index.php?controller=Carrito&action=ver"
                        class="carrito-link d-flex align-items-center justify-content-center"
                        title="Ver carrito">
                        <img src="/MizzaStore/assets/images/cart.png"
                            alt="Carrito"
                            class="carrito-icono">
                    </a>

                    <!-- Perfil del usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <img src="/MizzaStore/assets/images/perfil.png" alt="Perfil" class="perfil-icono">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php
                            require_once 'core/Sesion.php';
                            $usuario = Sesion::obtenerUsuario();
                            $perfil = $usuario['perfil'] ?? 'Invitado';
                            $autenticado = Sesion::usuarioAutenticado();
                            ?>
                            <?php if ($autenticado): ?>
                                <?php if ($perfil === 'Cliente'): ?>
                                    <li><a class="dropdown-item" href="index.php?controller=usuario&action=misPedidos">Mis pedidos</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="index.php?controller=MiPerfil&action=verFrmPerfil">Mi perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="index.php?controller=Login&action=logout">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item text-success" href="index.php?controller=Login&action=login">
                                        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>