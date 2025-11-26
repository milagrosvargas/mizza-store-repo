CREATE DATABASE mizzastore;
USE mizzastore;
/* ╔══════════════════════════╗
   ║GESTIÓN DE ESTADOS LÓGICOS║
   ╚══════════════════════════╝ */
CREATE TABLE estado_logico (
    id_estado_logico INT PRIMARY KEY AUTO_INCREMENT,
    nombre_estado VARCHAR(50)
);
/* ╔══════════════════════╗
   ║GESTIÓN DE DIRECCIONES║
   ╚══════════════════════╝ */
CREATE TABLE pais (
    id_pais INT PRIMARY KEY AUTO_INCREMENT,
    nombre_pais VARCHAR(50)
);
CREATE TABLE provincia (
    id_provincia INT PRIMARY KEY AUTO_INCREMENT,
    nombre_provincia VARCHAR(50),
    id_pais INT,
    FOREIGN KEY (id_pais) REFERENCES pais(id_pais)
);
CREATE TABLE localidad (
    id_localidad INT PRIMARY KEY AUTO_INCREMENT,
    nombre_localidad VARCHAR(50),
    id_provincia INT,
    FOREIGN KEY (id_provincia) REFERENCES provincia(id_provincia)
);
CREATE TABLE barrio (
    id_barrio INT PRIMARY KEY AUTO_INCREMENT,
    nombre_barrio VARCHAR(50),
    id_localidad INT,
    FOREIGN KEY (id_localidad) REFERENCES localidad(id_localidad)
);
CREATE TABLE domicilio (
    id_domicilio INT PRIMARY KEY AUTO_INCREMENT,
    calle_direccion VARCHAR(100),
    numero_direccion VARCHAR(10),
    piso_direccion VARCHAR(10),
    info_extra_direccion VARCHAR(100),
    id_barrio INT,
    FOREIGN KEY (id_barrio) REFERENCES barrio(id_barrio)
);
/* ╔══════════════════════════════════╗
   ║GESTIÓN DE DOCUMENTOS DE IDENTIDAD║
   ╚══════════════════════════════════╝ */
CREATE TABLE tipo_documento (
    id_tipo_documento INT PRIMARY KEY AUTO_INCREMENT,
    nombre_tipo_documento VARCHAR(50)
);
CREATE TABLE detalle_documento (
    id_detalle_documento INT PRIMARY KEY AUTO_INCREMENT,
    id_tipo_documento INT,
    descripcion_documento VARCHAR(100),
    FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documento(id_tipo_documento)
);
/* ╔══════════════════════════════════╗
   ║GESTIÓN DE INFORMACIÓN DE CONTACTO║
   ╚══════════════════════════════════╝ */
CREATE TABLE tipo_contacto (
    id_tipo_contacto INT PRIMARY KEY AUTO_INCREMENT,
    nombre_tipo_contacto VARCHAR(50)
);
CREATE TABLE detalle_contacto (
    id_detalle_contacto INT PRIMARY KEY AUTO_INCREMENT,
    descripcion_contacto VARCHAR(100),
    id_tipo_contacto INT,
    FOREIGN KEY (id_tipo_contacto) REFERENCES tipo_contacto(id_tipo_contacto)
);
/* ╔═════════════════════════════════════════════╗
   ║GESTIÓN DE INFORMACIÓN DE IDENTIDAD DE GÉNERO║
   ╚═════════════════════════════════════════════╝ */
CREATE TABLE genero (
    id_genero INT PRIMARY KEY AUTO_INCREMENT,
    nombre_genero VARCHAR(50) NOT NULL UNIQUE
);
/* ╔══════════════════════════════════╗
   ║GESTIÓN DE INFORMACIÓN DE PERSONAL║
   ╚══════════════════════════════════╝ */
CREATE TABLE persona (
    id_persona INT PRIMARY KEY AUTO_INCREMENT,
    nombre_persona VARCHAR(50) NOT NULL,
    apellido_persona VARCHAR(50) NOT NULL,
    fecha_nac_persona DATE,
    id_genero INT,
    id_domicilio INT,
    id_detalle_documento INT,
    id_detalle_contacto INT,
    FOREIGN KEY (id_genero) REFERENCES genero(id_genero),
    FOREIGN KEY (id_domicilio) REFERENCES domicilio(id_domicilio),
    FOREIGN KEY (id_detalle_documento) REFERENCES detalle_documento(id_detalle_documento),
    FOREIGN KEY (id_detalle_contacto) REFERENCES detalle_contacto(id_detalle_contacto)
);
/* ╔═══════════════════╗
   ║GESTIÓN DE PERFILES║
   ╚═══════════════════╝ */
CREATE TABLE perfil (
    id_perfil INT PRIMARY KEY AUTO_INCREMENT,
    descripcion_perfil VARCHAR(50),
    activo_perfil TINYINT(1) DEFAULT 1
);
/* ╔══════════════════╗
   ║GESTIÓN DE MÓDULOS║
   ╚══════════════════╝ */
CREATE TABLE modulo (
    id_modulo INT PRIMARY KEY AUTO_INCREMENT,
    descripcion_modulo VARCHAR(100),
    activo_modulo TINYINT(1) DEFAULT 1
);
/* ╔═══════════════════╗
   ║GESTIÓN DE PERMISOS║
   ╚═══════════════════╝ */
CREATE TABLE modulo_perfil (
    relacion_modulo INT,
    relacion_perfil INT,
    PRIMARY KEY (relacion_modulo, relacion_perfil),
    FOREIGN KEY (relacion_modulo) REFERENCES modulo(id_modulo) ON DELETE CASCADE,
    FOREIGN KEY (relacion_perfil) REFERENCES perfil(id_perfil) ON DELETE CASCADE
);
/* ╔═══════════════════╗
   ║GESTIÓN DE USUARIOS║
   ╚═══════════════════╝ */
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    password_usuario VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_usuario TINYINT(1) DEFAULT 0, -- 0 = Inactivo, 1 = Activo
    cuenta_activada TINYINT(1) DEFAULT 0,
    relacion_persona INT NOT NULL,
    relacion_perfil INT NOT NULL,
    FOREIGN KEY (relacion_persona) REFERENCES persona(id_persona),
    FOREIGN KEY (relacion_perfil) REFERENCES perfil(id_perfil)
);
/* ╔═══════════════════════════════════════════════════════════════╗
   ║  AUDITORÍA DE CAMBIOS DE CONTRASEÑA                           ║
   ╚═══════════════════════════════════════════════════════════════╝ */
CREATE TABLE auditoria_contrasenas (
    id_auditoria INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_cambio VARCHAR(45) NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

/* ╔═════════════════════════════════╗
   ║GESTIÓN DE TOKENS DE RECUPERACIÓN║
   ╚═════════════════════════════════╝ */
CREATE TABLE tokens_usuario (
    id_token INT PRIMARY KEY AUTO_INCREMENT,
    relacion_usuario INT NOT NULL,
    token VARCHAR(100) NOT NULL,
    tipo ENUM('activacion', 'recuperacion') NOT NULL,
    expiracion DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (relacion_usuario) REFERENCES usuario(id_usuario)
);
/* ╔═══════════════════╗
   ║GESTIÓN DE SESIONES║
   ╚═══════════════════╝ */
CREATE TABLE sesion (
    id_sesion INT PRIMARY KEY AUTO_INCREMENT,
    relacion_usuario INT NOT NULL,
    activa_sesion TINYINT(1) NOT NULL DEFAULT 0,
    fecha_ultimo_login DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (relacion_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);
/* ╔═══════════════════╗
   ║GESTIÓN DE CLIENTES║
   ╚═══════════════════╝ */
CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    estado_cliente TINYINT DEFAULT 1,
    relacion_persona INT NOT NULL,
    FOREIGN KEY (relacion_persona) REFERENCES persona(id_persona)
);
/* ╔════════════════════╗
   ║GESTIÓN DE EMPLEADOS║
   ╚════════════════════╝ */
CREATE TABLE empleado (
    id_empleado INT PRIMARY KEY AUTO_INCREMENT,
    relacion_persona INT NOT NULL,
    fecha_alta_empleado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_empleado TINYINT DEFAULT 1,
    FOREIGN KEY (relacion_persona) REFERENCES persona(id_persona)
);

/* ╔═════════════════════╗
   ║GESTIÓN DE REPARTIDOR║
   ╚═════════════════════╝ */
CREATE TABLE repartidor (
    id_repartidor INT PRIMARY KEY AUTO_INCREMENT,
    relacion_persona INT NOT NULL,
    fecha_alta_repartidor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_repartidor TINYINT DEFAULT 1,
    FOREIGN KEY (relacion_persona) REFERENCES persona(id_persona)
);

/* ╔══════════════════════════════════╗
   ║GESTIÓN DE CATEGORÍAS DE PRODUCTOS║
   ╚══════════════════════════════════╝ */
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    imagen_categoria VARCHAR(255),
    alta_categoria TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizacion_categoria DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_estado_logico INT,
    FOREIGN KEY (id_estado_logico) REFERENCES estado_logico(id_estado_logico)
);
/* ╔════════════════════════════════════════════════════╗
   ║GESTIÓN DE SUB-CATEGORÍAS DE CATEGORÍAS DE PRODUCTOS║
   ╚════════════════════════════════════════════════════╝ */
CREATE TABLE sub_categoria (
    id_sub_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_sub_categoria VARCHAR(50) NOT NULL,
    cant_sub_categoria INT NOT NULL,
    id_estado_logico INT,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_estado_logico) REFERENCES estado_logico(id_estado_logico)
);
/* ╔══════════════════════════════╗
   ║GESTIÓN DE MARCAS DE PRODUCTOS║
   ╚══════════════════════════════╝ */
CREATE TABLE marca (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100)
);
/* ╔══════════════════════════════════════════╗
   ║GESTIÓN DE UNIDADES DE MEDIDA DE PRODUCTOS║
   ╚══════════════════════════════════════════╝ */
CREATE TABLE unidad_medida (
    id_unidad_medida INT AUTO_INCREMENT PRIMARY KEY,
    nombre_unidad_medida VARCHAR(100)
);
/* ╔════════════════════╗
   ║GESTIÓN DE PRODUCTOS║
   ╚════════════════════╝ */
CREATE TABLE producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(100) UNIQUE,
    nombre_producto VARCHAR(255) NOT NULL,
    descripcion_producto TEXT,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_minimo INT NOT NULL,
    stock_actual INT NOT NULL,
    imagen_producto VARCHAR(255),
    id_marca INT,
    id_categoria INT,
    id_sub_categoria INT,
    id_unidad_medida INT,
    id_estado_logico INT,
    alta_producto TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizacion_producto DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_marca) REFERENCES marca(id_marca),
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_sub_categoria) REFERENCES sub_categoria(id_sub_categoria) ON DELETE SET NULL,
    FOREIGN KEY (id_unidad_medida) REFERENCES unidad_medida(id_unidad_medida),
    FOREIGN KEY (id_estado_logico) REFERENCES estado_logico(id_estado_logico)
);
/* ╔══════════════════════════╗
   ║GESTIÓN DE MÉTODOS DE PAGO║
   ╚══════════════════════════╝ */
CREATE TABLE metodo_pago (
    id_metodo_pago INT AUTO_INCREMENT PRIMARY KEY,
    nombre_metodo_pago VARCHAR(50)
);
/* ╔═════════════════════════════════════════════╗
   ║GESTIÓN DE EMISIÓN DE NOTAS PARA DEVOLUCIONES║
   ╚═════════════════════════════════════════════╝ */
CREATE TABLE tipo_nota (
    id_tipo_nota INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipo_nota VARCHAR(50)
);
/* ╔═════════════════╗
   ║GESTIÓN DE BANNER║
   ╚═════════════════╝ */
CREATE TABLE banner (
    id_banner INT AUTO_INCREMENT PRIMARY KEY,
    titulo_banner TEXT NOT NULL,
    imagen_banner VARCHAR(255) NOT NULL,
    estado_banner TINYINT(1) DEFAULT 1
);
/* ╔══════════════════════════════╗
   ║GESTIÓN DE PEDIDOS DE CLIENTES║
   ╚══════════════════════════════╝ */
CREATE TABLE pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_estado_logico INT,
    id_repartidor INT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    monto_total DECIMAL(10,2),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_estado_logico) REFERENCES estado_logico(id_estado_logico),
    FOREIGN KEY (id_repartidor) REFERENCES repartidor(id_repartidor) ON DELETE SET NULL ON UPDATE CASCADE
);
/* ╔══════════════════════════════════════════╗
   ║GESTIÓN DE DETALLES DEL PEDIDO DE CLIENTES║
   ╚══════════════════════════════════════════╝ */
CREATE TABLE detalle_pedido (
    id_detalle_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_producto INT,
    cantidad_producto INT NOT NULL,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);
/* ╔════════════════════════════╗
   ║GESTIÓN DE PAGO DE PRODUCTOS║
   ╚════════════════════════════╝ */
CREATE TABLE pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_metodo_pago INT,
    estado_pago ENUM('pendiente', 'completado', 'fallido') NOT NULL,
    monto_pago DECIMAL(10,2),
    payment_mp_id VARCHAR(100),
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_metodo_pago) REFERENCES metodo_pago(id_metodo_pago)
);
/* ╔════════════════════════════════════════════╗
   ║GESTIÓN DE INFORMACIÓN DE ENVÍO DEL PRODUCTO║
   ╚════════════════════════════════════════════╝ */
CREATE TABLE envio (
    id_envio INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_domicilio INT NULL,
    estado ENUM('pendiente', 'en camino', 'entregado') NOT NULL,
    fecha_envio TIMESTAMP NULL,
    fecha_entrega TIMESTAMP NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_domicilio) REFERENCES domicilio(id_domicilio)
);
/* ╔════════════════════════════════════════════╗
   ║GESTIÓN DE LISTAS DE DESEOS DE LOS CLIENTES ║
   ╚════════════════════════════════════════════╝ */
CREATE TABLE wishlist (
    id_wishlist INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_producto INT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
);
/* ╔══════════════════════════════════════════════╗
   ║GESTIÓN DE RESEÑAS Y VALORACIONES DE PRODUCTOS║
   ╚══════════════════════════════════════════════╝ */
CREATE TABLE review (
    id_review INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_producto INT,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_review TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
);
/* ╔════════════════════════════════════════════════╗
   ║GESTIÓN DE PUBLICACIONES DEL BLOG ADMINISTRATIVO║
   ╚════════════════════════════════════════════════╝ */
CREATE TABLE blog_post (
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL, 
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);
/* ╔═════════════════════════════════════════════╗
   ║GESTIÓN DE COMENTARIOS DE CLIENTES EN EL BLOG║
   ╚═════════════════════════════════════════════╝ */
CREATE TABLE comentarios_blog (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT,
    id_cliente INT,
    comentario TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES blog_post(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE
);
/* ╔════════════════════════════════════════════╗
   ║GESTIÓN DE NOTIFICACIONES PARA LOS USUARIOS ║
   ╚════════════════════════════════════════════╝ */
CREATE TABLE notificaciones (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    mensaje TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_estado_logico INT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_estado_logico) REFERENCES estado_logico(id_estado_logico)
);
/* ╔═══════════════════════════════╗
   ║GESTIÓN DE COMPRAS DE CLIENTES ║
   ╚═══════════════════════════════╝ */
CREATE TABLE historial_compra (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_pedido INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE
);

/* ╔══════════════════════════════════╗
   ║GESTIÓN DE PERÍODOS PARA REPORTES ║
   ╚══════════════════════════════════╝ */
CREATE TABLE periodo (
    id_periodo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_periodo VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    descripcion TEXT NULL,
    cantidad_vendida INT NULL
);
/* ╔══════════════════════════════════════════════╗
   ║GESTIÓN DE PERÍODOS PARA REPORTES (RELACIÓN)  ║
   ╚══════════════════════════════════════════════╝ */
CREATE TABLE periodo_pedido (
    id_periodo_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_periodo INT NOT NULL,
    id_pedido INT NOT NULL,
    FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo),
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
);

