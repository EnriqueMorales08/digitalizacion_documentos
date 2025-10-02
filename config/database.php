

<?php
class Database {
    private $host = "192.168.10.10";   // instancia SQL Server
    private $db_name = "FACCARPRUEBA";
    private $username = "sa";      // usuario SQL Server
    private $password = "sistemasi";  // contraseña SQL Server
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Usar sqlsrv para SQL Server
            $connectionInfo = array(
                "Database" => $this->db_name,
                "UID" => $this->username,
                "PWD" => $this->password,
                "CharacterSet" => "UTF-8"
            );
            $this->conn = sqlsrv_connect($this->host, $connectionInfo);
            if ($this->conn === false) {
                throw new Exception("Error de conexión: " . print_r(sqlsrv_errors(), true));
            }
        } catch (Exception $exception) {
            die("❌ Error de conexión a la BD: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
