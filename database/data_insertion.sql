USE mizzastore;
/* ╔══════════════════════════════════════════╗
   ║INSERCIÓN DE DATOS BASE DE ESTADOS LÓGICOS║
   ╚══════════════════════════════════════════╝ */
INSERT INTO estado_logico (nombre_estado) VALUES 
-- Estados para bajas lógicas:
('Activo'), ('Inactivo'),
-- Estados para procesos de pago:
('Pagado'), ('Pendiente de pago'), 
-- Estados para el proceso de envío y recepción de un producto
('Enviado'), ('Entregado'), ('Cancelado'),
-- Estados para el stock: 
('Disponible'), ('No disponible'), ('Agotado');

/* ╔═════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE UBICACIONES║
   ╚═════════════════════════════════╝ */
INSERT INTO pais (nombre_pais) VALUES ('Argentina');

INSERT INTO provincia (nombre_provincia, id_pais) VALUES ('Formosa', 1);

INSERT INTO localidad (nombre_localidad, id_provincia) VALUES 
('Formosa', 1), ('Pirané', 1), ('Pozo del Tigre', 1), ('Laishí', 1), 
('San Martín II', 1), ('Villa Dos Trece', 1), ('Villafañe', 1), ('Ramón Lista', 1), 
('Río Muerto', 1), ('Pilcomayo', 1), ('Gral Belgrano', 1), ('Pilagás', 1), 
('Matacos', 1), ('Bermejo', 1), ('Las Lomitas', 1), ('Guemes', 1);

INSERT INTO barrio (nombre_barrio, id_localidad) VALUES 
('Barrio La Pilar', 1), ('Barrio 2 de Abril', 1), ('Barrio 7 de Mayo', 1),
('Barrio Antenor Gauna', 1), ('Barrio Bernardino Rivadavia', 1),
('Barrio Centenario', 1), ('Barrio Coluccio', 1), ('Barrio Curé Cuá', 1),
('Barrio Divino Niño Jesús', 1), ('Barrio El Amanecer', 1), ('Barrio El Palmar', 1),
('Barrio El Pucú', 1), ('Barrio Eva Perón', 1), ('Barrio Guadalupe', 1),
('Barrio Independencia', 1), ('Barrio Irigoyen', 1), ('Barrio Juan Domingo Perón', 1),
('Barrio Juan Manuel de Rosas', 1), ('Barrio La Colonia', 1), ('Barrio La Lomita', 1),
('Barrio La Nueva Formosa', 1), ('Barrio La Paz', 1), ('Barrio Laguna Siam', 1),
('Barrio Las Orquídeas', 1), ('Barrio Lote 4', 1), ('Barrio Lote 111', 1),
('Barrio Lote 67', 1), ('Barrio Lote Rural 3 Bis', 1), ('Barrio Los Inmigrantes', 1),
('Barrio Los Naranjos', 1), ('Barrio Los Pinos', 1), ('Barrio Mariano Moreno', 1),
('Barrio Medalla Milagrosa', 1), ('Barrio Nanqom', 1), ('Barrio Nuestra Señora de Luján', 1),
('Barrio Parque Urbano', 1), ('Barrio República Argentina', 1), ('Barrio Ricardo Balbín', 1),
('Barrio San Agustín', 1), ('Barrio San Antonio', 1), ('Barrio San Carlos', 1),
('Barrio San Cayetano', 1), ('Barrio San Fernando', 1), ('Barrio San Francisco de Asís', 1),
('Barrio San José Obrero', 1), ('Barrio San Juan Bautista', 1), ('Barrio San Lorenzo', 1),
('Barrio San Miguel', 1), ('Barrio San Pedro', 1), ('Barrio San Roque', 1),
('Barrio Santa Rosa', 1), ('Barrio Sagrado Corazón', 1), ('Barrio Sagrado Corazón de María', 1),
('Barrio Simón Bolívar', 1), ('Barrio Timbó', 1), ('Barrio Urunday', 1),
('Barrio Veinticinco de Mayo', 1), ('Barrio Venezuela', 1), ('Barrio Vial', 1),
('Barrio Villa Hermosa', 1), ('Barrio Villa Lourdes', 1), ('Barrio Villa Mabel', 1),
('Barrio Villa del Carmen', 1);

/* ╔═════════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE CONFIGURACIONES║
   ╚═════════════════════════════════════╝ */
INSERT INTO tipo_documento (nombre_tipo_documento) VALUES ('DNI'), ('CDI'), ('CUIT'), ('CUIL'), ('DNIe'), ('LC');

INSERT INTO tipo_contacto (nombre_tipo_contacto) VALUES ('Correo electrónico'), ('Número de teléfono');

INSERT INTO genero (nombre_genero) VALUES ('Masculino'), ('Femenino'), ('No binario'), ('Prefiero no decirlo');

/* ╔════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE CATEGORÍAS║
   ╚════════════════════════════════╝ */
INSERT INTO categoria (nombre_categoria, imagen_categoria, id_estado_logico) VALUES
('Skincare', NULL, 1),
('Maquillaje', NULL, 1),
('Brochas y pinceles', NULL, 1);

INSERT INTO sub_categoria (nombre_sub_categoria, cant_sub_categoria, id_estado_logico, id_categoria) VALUES
-- SKINCARE
('Limpieza Facial', 10, 1, 1),
('Tónicos', 8, 1, 1),
('Hidratantes', 12, 1, 1),
('Exfoliantes', 6, 1, 1),
('Mascarillas', 7, 1, 1),

-- MAQUILLAJE
('Base de Maquillaje', 15, 1, 2),
('Polvo Compacto', 10, 1, 2),
('Rubor', 9, 1, 2),
('Iluminador', 8, 1, 2),
('Rímel para Ojos', 12, 1, 2),
('Labial', 20, 1, 2),
('Lápiz para Cejas', 10, 1, 2),

-- BROCHAS Y PINCELES
('Brochas para Rostro', 10, 1, 3),
('Brochas para Ojos', 12, 1, 3),
('Pinceles para Labios', 8, 1, 3),
('Esponjas de Maquillaje', 6, 1, 3);

/* ╔════════════════════════════╗
   ║INSERCIÓN DE DATOS DE MARCAS║
   ╚════════════════════════════╝ */
INSERT INTO marca (nombre_marca) VALUES 
('MAC'), ('Maybelline'), ('NARS'), ('Fenty Beauty'), ('Urban Decay'), ('L’Oréal'),
('Dior'), ('The Ordinary'), ('La Roche-Posay'), ('CeraVe'), ('Neutrogena'), ('Clinique'), ('Estée Lauder');

/* ╔══════════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE UNIDADES Y PAGOS║
   ╚══════════════════════════════════════╝ */
INSERT INTO unidad_medida (nombre_unidad_medida) VALUES 
('ml'), ('oz'), ('gr'), ('lt'), ('unidades'), ('pieza'), ('pack'), ('kg'), ('sobre');

INSERT INTO metodo_pago (nombre_metodo_pago) VALUES ('Efectivo'), ('Tarjeta débito'), ('Tarjeta crédito'), ('Transferencia');

INSERT INTO tipo_nota (nombre_tipo_nota) VALUES ('Nota de crédito'), ('Nota de débito');

/* ╔═════════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE PERFILES DE USO║
   ╚═════════════════════════════════════╝ */
INSERT INTO perfil (descripcion_perfil) VALUES 
('Administrador'),   
('Empleado'),        
('Repartidor'),      
('Cliente'),          
('Invitado');         

/* ╔═════════════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE MÓDULOS DEL SISTEMA║
   ╚═════════════════════════════════════════╝ */
INSERT INTO modulo (descripcion_modulo) VALUES 
('Catálogo'), 
('Usuarios'), 
('Clientes'), 
('Ventas'), 
('Inventario'), 
('Productos'),
('Pedidos'),
('Configuración'), 
('Blog interno'), 
('Reportes'),
('Cósmeticos'), 
('Blog externo'), 
('Sobre nosotros'),
('Home'); 

/* ╔════════════════════════════════╗
   ║ASIGNACIÓN DE MÓDULOS POR PERFIL║
   ╚════════════════════════════════╝ */
-- ADMINISTRADOR (acceso total)
INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil) VALUES
(1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1);

-- EMPLEADO (acceso parcial)
INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil) VALUES
(1, 2), (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (9, 2);

-- REPARTIDOR (solo pedidos)
INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil) VALUES (7, 3);

-- CLIENTE (catálogo, pedidos y blog)
INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil) VALUES (1, 4), (7, 4), (9, 4);

-- INVITADO (acceso externo: cósmeticos, blog externo y sobre nosotros)
INSERT INTO modulo_perfil (relacion_modulo, relacion_perfil) VALUES (11, 5), (12, 5), (13, 5), (14, 5);
/* ╔═══════════════════════════════════════════════════════════════════════╗
   ║      INSERCIÓN DE USUARIOS BASE DEL SISTEMA CON DATOS ASOCIADOS       ║
   ╚═══════════════════════════════════════════════════════════════════════╝ */

START TRANSACTION;

/* ╔══════════════════════════════════════════════════╗
   ║USUARIO / PERSONA 1 → PERFIL ADMINISTRADOR (ID=1) ║
   ╚══════════════════════════════════════════════════╝ */
-- Domicilio (id_domicilio = 1)
INSERT INTO domicilio (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
VALUES ('Fuerza Aérea Argentina', '772', NULL, NULL, 1);

-- Documento (id_detalle_documento = 1)
INSERT INTO detalle_documento (descripcion_documento, id_tipo_documento)
VALUES ('44343341', 1);

-- Contactos (id_detalle_contacto = 1 y 2)
INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
VALUES ('milovargasb@gmail.com', 1);
INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
VALUES ('3704224812', 2);

-- Persona (id_persona = 1)
INSERT INTO persona (nombre_persona, apellido_persona, fecha_nac_persona, id_domicilio, id_detalle_documento, id_detalle_contacto)
VALUES ('Milagros Belén', 'Vargas', '2002-10-02', 1, 1, 1);

-- Usuario (perfil 1 = administrador)
-- Contraseña en texto plano: AdminPass!2025
INSERT INTO usuario (
    nombre_usuario, password_usuario, relacion_persona, relacion_perfil,
    estado_usuario, cuenta_activada
)
VALUES (
    'administrador',
    '$2b$10$CjEuLrG5b/0ixITLbykT8eBrxJbBKk1k5BtTntFAkvCMv3KuVIdCy',
    1, 1,
    1, 1
);

/* ╔═══════════════════════════════════════════════╗
   ║USUARIO / PERSONA 2 → PERFIL EMPLEADO (ID=2)   ║
   ╚═══════════════════════════════════════════════╝ */
-- Domicilio (id_domicilio = 2)
INSERT INTO domicilio (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
VALUES ('Almafuerte', '789', NULL, 'Depto 5A', 3);

-- Documento (id_detalle_documento = 2)
INSERT INTO detalle_documento (descripcion_documento, id_tipo_documento)
VALUES ('44156607', 1);

-- Contacto (id_detalle_contacto = 3)
INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
VALUES ('juan.guzman@gmail.com', 1);

-- Persona (id_persona = 2)
INSERT INTO persona (nombre_persona, apellido_persona, fecha_nac_persona, id_domicilio, id_detalle_documento, id_detalle_contacto)
VALUES ('Juan', 'Guzmán', '1990-02-20', 2, 2, 3);

-- Usuario (perfil 2 = empleado)
-- Contraseña en texto plano: Empleado#2025
INSERT INTO usuario (
    nombre_usuario, password_usuario, relacion_persona, relacion_perfil,
    estado_usuario, cuenta_activada
)
VALUES (
    'juan_guzman',
    '$2b$10$OB7cMW2454Z2I0i1Fq7V5eOJuZRKNjXwS9bjBRuzgKCz7OIWEDP1S',
    2, 2,
    1, 1
);

/* ╔═══════════════════════════════════════════════╗
   ║USUARIO / PERSONA 3 → PERFIL REPARTIDOR (ID=3) ║
   ╚═══════════════════════════════════════════════╝ */
-- Domicilio (id_domicilio = 3)
INSERT INTO domicilio (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
VALUES ('Mitre', '55', NULL, NULL, 4);

-- Documento (id_detalle_documento = 3)
INSERT INTO detalle_documento (descripcion_documento, id_tipo_documento)
VALUES ('42563044', 1);

-- Contacto (id_detalle_contacto = 4)
INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
VALUES ('luis.reparto@gmail.com', 1);

-- Persona (id_persona = 3)
INSERT INTO persona (nombre_persona, apellido_persona, fecha_nac_persona, id_domicilio, id_detalle_documento, id_detalle_contacto)
VALUES ('Luis', 'García', '1992-08-10', 3, 3, 4);

-- Usuario (perfil 3 = repartidor)
-- Contraseña en texto plano: Repartidor#2025
INSERT INTO usuario (
    nombre_usuario, password_usuario, relacion_persona, relacion_perfil,
    estado_usuario, cuenta_activada
)
VALUES (
    'luis_repartidor',
    '$2b$10$0r3ZV6ctw76.lmYv8dsKWuX0qEeBBm0AgYoTUk9MUqfXyreeyUpL6',
    3, 3,
    1, 1
);

/* ╔═══════════════════════════════════════════════╗
   ║USUARIO / PERSONA 4 → PERFIL CLIENTE (ID=4)    ║
   ╚═══════════════════════════════════════════════╝ */
-- Domicilio (id_domicilio = 4)
INSERT INTO domicilio (calle_direccion, numero_direccion, piso_direccion, info_extra_direccion, id_barrio)
VALUES ('Oliva', '496', NULL, NULL, 2);

-- Documento (id_detalle_documento = 4)
INSERT INTO detalle_documento (descripcion_documento, id_tipo_documento)
VALUES ('32849531', 1);

-- Contacto (id_detalle_contacto = 5)
INSERT INTO detalle_contacto (descripcion_contacto, id_tipo_contacto)
VALUES ('sofia.cliente@gmail.com', 1);

-- Persona (id_persona = 4)
INSERT INTO persona (nombre_persona, apellido_persona, fecha_nac_persona, id_domicilio, id_detalle_documento, id_detalle_contacto)
VALUES ('Sofía', 'Alarcón', '1990-05-20', 4, 4, 5);

-- Usuario (perfil 4 = cliente)
-- Contraseña en texto plano: Cliente#2025
INSERT INTO usuario (
    nombre_usuario, password_usuario, relacion_persona, relacion_perfil,
    estado_usuario, cuenta_activada
)
VALUES (
    'sofia_cliente',
    '$2b$10$Sjb3vpMCWQ1uTm1hL76ele4SO/u5g81Z2g7K/Sq.0DOdz61PcwGBG',
    4, 4,
    1, 1
);

/* ╔══════════════════════════════════════╗
   ║INSERCIÓN DE DATOS DE PRODUCTOS (20)  ║
   ╚══════════════════════════════════════╝ */
INSERT INTO producto (
    codigo_barras,
    nombre_producto,
    descripcion_producto,
    precio_compra,
    precio_venta,
    stock_minimo,
    stock_actual,
    imagen_producto,
    id_marca,
    id_categoria,
    id_sub_categoria,
    id_unidad_medida,
    id_estado_logico
) VALUES
-- SKINCARE
('SK001', 'Espuma Limpiadora Suave', 'Espuma facial suave que elimina impurezas sin resecar.', 10.00, 18.00, 5, 40, NULL, 10, 1, 1, 1, 8),
('SK002', 'Tónico Refrescante Hidratante', 'Refresca y tonifica la piel preparándola para la hidratación.', 8.00, 15.00, 5, 35, NULL, 9, 1, 2, 1, 8),
('SK003', 'Crema Hidratante con Ácido Hialurónico', 'Hidrata profundamente y mejora la elasticidad de la piel.', 12.00, 22.00, 3, 28, NULL, 10, 1, 3, 1, 8),
('SK004', 'Exfoliante Facial Suave', 'Exfoliante con microgránulos naturales para una piel radiante.', 9.00, 17.00, 4, 25, NULL, 11, 1, 4, 3, 8),
('SK005', 'Mascarilla Purificante de Arcilla', 'Elimina el exceso de grasa y limpia los poros en profundidad.', 7.00, 14.00, 5, 20, NULL, 9, 1, 5, 3, 8),

-- MAQUILLAJE
('MK001', 'Base de Maquillaje Líquida Tono Claro', 'Cubre imperfecciones y deja un acabado natural.', 15.00, 28.00, 5, 30, NULL, 1, 2, 6, 1, 8),
('MK002', 'Polvo Compacto Mate', 'Fija el maquillaje y controla el brillo.', 10.00, 19.00, 4, 25, NULL, 2, 2, 7, 5, 8),
('MK003', 'Rubor en Polvo Rosa', 'Añade un toque de color saludable a tus mejillas.', 8.00, 16.00, 5, 22, NULL, 3, 2, 8, 3, 8),
('MK004', 'Iluminador en Polvo Dorado', 'Ilumina el rostro con un brillo sutil y natural.', 12.00, 23.00, 3, 20, NULL, 4, 2, 9, 3, 8),
('MK005', 'Rímel Volumen Extremo', 'Aporta volumen y longitud a las pestañas.', 9.00, 18.00, 5, 35, NULL, 2, 2, 10, 5, 8),
('MK006', 'Labial Mate Rojo Intenso', 'Color duradero y textura suave.', 6.00, 12.00, 5, 40, NULL, 1, 2, 11, 5, 8),
('MK007', 'Lápiz para Cejas Marrón', 'Define y rellena las cejas de manera natural.', 4.00, 9.00, 4, 30, NULL, 6, 2, 12, 5, 8),

-- BROCHAS Y PINCELES
('BR001', 'Brocha Kabuki para Base', 'Brocha densa ideal para aplicar base líquida o en polvo.', 5.00, 10.00, 3, 25, NULL, 5, 3, 13, 6, 8),
('BR002', 'Set de Brochas para Ojos 5pzs', 'Set profesional para sombras y delineado.', 8.00, 18.00, 4, 18, NULL, 5, 3, 14, 7, 8),
('BR003', 'Pincel Delineador Fino', 'Ideal para trazos precisos con gel o líquido.', 3.00, 7.00, 5, 30, NULL, 3, 3, 15, 6, 8),
('BR004', 'Esponja de Maquillaje Blender', 'Perfecta para aplicar base líquida o corrector.', 4.00, 9.00, 5, 40, NULL, 6, 3, 16, 6, 8),
('BR005', 'Brocha para Rubor Profesional', 'Brocha de cerdas suaves para aplicar rubor.', 5.50, 11.00, 4, 25, NULL, 5, 3, 13, 6, 8),

-- SKINCARE ADICIONALES
('SK006', 'Sérum Antioxidante con Vitamina C', 'Ilumina y unifica el tono de la piel.', 14.00, 27.00, 3, 22, NULL, 8, 1, 3, 1, 8),
('SK007', 'Aceite Facial Nutritivo', 'Aceite liviano con omega 3 y 6.', 13.00, 26.00, 3, 18, NULL, 12, 1, 3, 1, 8),
('SK008', 'Desmaquillante Bifásico', 'Elimina maquillaje resistente al agua.', 9.00, 17.00, 4, 25, NULL, 7, 1, 1, 1, 8),
('SK009', 'Mascarilla de Noche Revitalizante', 'Repara la piel durante el descanso nocturno.', 11.00, 22.00, 3, 20, NULL, 13, 1, 5, 1, 8);

COMMIT;
/* ╔═══════════════════════════════════════════════════════════════════════╗
   ║      FIN DE INSERCIÓN DE USUARIOS BASE DEL SISTEMA (TRANSACCIÓN OK)   ║
   ╚═══════════════════════════════════════════════════════════════════════╝ */
