

<?php
class Database {
    private $host = "localhost\\SQLEXPRESS";   // instancia SQL Server
    private $db_name = "documentos_db";
    private $username = "sa";      // usuario SQL Server
    private $password = "Enrique123";  // contraseña SQL Server
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Usar PDO_SQLSRV para SQL Server
            $this->conn = new PDO(
                "sqlsrv:Server=" . $this->host . ";Database=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            die("❌ Error de conexión a la BD: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
