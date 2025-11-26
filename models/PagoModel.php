<?php

class PagoModel
{
    private $db;

    public function __construct($db)
    {
        // Recibe conexión PDO desde afuera (inyección compartida)
        $this->db = $db;
    }

    // ==========================================================
    // 1️⃣ REGISTRAR PAGO PENDIENTE (cuando inicia el checkout)
    // ==========================================================
    public function crearPagoPendiente(int $idPedido, float $monto): int
    {
        try {
            $sql = "INSERT INTO pago (id_pedido, id_metodo_pago, estado_pago, monto_pago)
                    VALUES (:id_pedido, 3, 'pendiente', :monto_pago)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':monto_pago', $monto);
            $stmt->execute();

            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error crearPagoPendiente: " . $e->getMessage());
            return 0;
        }
    }

    // ==========================================================
    // 2️⃣ ACTUALIZAR PAYMENT_MP_ID (cuando MercadoPago devuelve ID)
    // ==========================================================
    public function actualizarPaymentId(int $idPedido, string $paymentId): bool
    {
        try {
            $sql = "UPDATE pago
                    SET payment_mp_id = :payment_mp_id
                    WHERE id_pedido = :id_pedido
                    ORDER BY id_pago DESC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':payment_mp_id', $paymentId);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizarPaymentId: " . $e->getMessage());
            return false;
        }
    }

    // ==========================================================
    // 3️⃣ ACTUALIZAR ESTADO DE PAGO SEGÚN MP (approved, pending, rejected...)
    // ==========================================================
    public function actualizarEstadoPago(string $paymentId, string $estadoMP): bool
    {
        // Convertir estado de MercadoPago → ENUM interno
        switch ($estadoMP) {
            case 'approved':
                $estadoBD = 'completado';
                break;
            case 'pending':
                $estadoBD = 'pendiente';
                break;
            case 'rejected':
            case 'cancelled':
            case 'charged_back':
                $estadoBD = 'fallido';
                break;
            default:
                $estadoBD = 'pendiente';
        }

        try {
            $sql = "UPDATE pago
                    SET estado_pago = :estado
                    WHERE payment_mp_id = :payment_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estadoBD);
            $stmt->bindParam(':payment_id', $paymentId);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizarEstadoPago: " . $e->getMessage());
            return false;
        }
    }

    // ==========================================================
    // 4️⃣ OBTENER ÚLTIMO PAGO BÁSICO POR PEDIDO
    // ==========================================================
    public function obtenerPagoPorPedido(int $idPedido): ?array
    {
        try {
            $sql = "SELECT *
                    FROM pago
                    WHERE id_pedido = :id_pedido
                    ORDER BY id_pago DESC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error obtenerPagoPorPedido: " . $e->getMessage());
            return null;
        }
    }

    // ==========================================================
    // 5️⃣ OBTENER PAGO COMPLETO CON MÉTODO DE PAGO
    // ==========================================================
    public function obtenerPagoCompletoPorPedido(int $idPedido): ?array
    {
        try {
            $sql = "SELECT 
                        p.id_pago,
                        p.payment_mp_id,
                        p.id_pedido,
                        p.estado_pago,
                        p.monto_pago,
                        mp.nombre_metodo_pago,
                        p.id_metodo_pago
                    FROM pago p
                    INNER JOIN metodo_pago mp ON mp.id_metodo_pago = p.id_metodo_pago
                    WHERE p.id_pedido = :id_pedido
                    ORDER BY p.id_pago DESC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error obtenerPagoCompletoPorPedido: " . $e->getMessage());
            return null;
        }
    }
}