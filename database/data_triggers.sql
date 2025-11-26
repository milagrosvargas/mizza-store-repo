USE mizzastore;
/* ╔═══════════════════════════════════════════════════════════════╗
   ║  TRIGGER AUTOMÁTICO DE AUDITORÍA                              ║
   ╚═══════════════════════════════════════════════════════════════╝ */
DELIMITER $$

CREATE TRIGGER trg_auditoria_cambio_password
AFTER UPDATE ON usuario
FOR EACH ROW
BEGIN
    -- Solo registrar si el password realmente cambió
    IF (OLD.password_usuario <> NEW.password_usuario) THEN
        INSERT INTO auditoria_contrasenas (id_usuario, fecha_cambio)
        VALUES (NEW.id_usuario, NOW());
    END IF;
END $$

DELIMITER ;
DELIMITER $$

CREATE TRIGGER trg_envio_marcar_en_camino
AFTER UPDATE ON envio
FOR EACH ROW
BEGIN
    -- Solo ejecutar si el estado realmente cambió a 'en camino'
    IF NEW.estado = 'en camino' AND OLD.estado <> 'en camino' THEN

        -- Actualizar el estado del pedido a 'Enviado' usando un ID existente
        UPDATE pedido
        SET id_estado_logico = (
            SELECT MIN(id_estado_logico)
            FROM estado_logico
            WHERE nombre_estado = 'Enviado'
        )
        WHERE id_pedido = NEW.id_pedido;

    END IF;
END $$

DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_actualizar_cantidad_vendida
AFTER INSERT ON periodo_pedido
FOR EACH ROW
BEGIN
    UPDATE periodo
    SET cantidad_vendida = (
        SELECT COALESCE(SUM(dp.cantidad_producto), 0)
        FROM periodo_pedido pp
        INNER JOIN detalle_pedido dp 
            ON dp.id_pedido = pp.id_pedido
        WHERE pp.id_periodo = NEW.id_periodo
    )
    WHERE id_periodo = NEW.id_periodo;
END //

DELIMITER ;



