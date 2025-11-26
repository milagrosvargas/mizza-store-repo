<?php
class Conexion {
    private $host = 'localhost';
    private $db_name = 'mizzastore';
    private $username = 'root';
    private $password = '';
    private $port = 3306;
    public $conection;

    public function Conectar() {
        $this->conection = null;

        try {
            $this->conection = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error al establecer la conexión: ' . $e->getMessage();
            exit;
        }

        return $this->conection;
    }
}
?>
