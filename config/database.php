

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

    public function getStockConnection() {
        $stockConn = null;
        try {
            $connectionInfo = array(
                "Database" => "stock",
                "UID" => "sa",
                "PWD" => "sistemasi",
                "CharacterSet" => "UTF-8"
            );
            $stockConn = sqlsrv_connect("192.168.10.13", $connectionInfo);
            if ($stockConn === false) {
                throw new Exception("Error de conexión a BD Stock: " . print_r(sqlsrv_errors(), true));
            }
        } catch (Exception $exception) {
            // No morir, solo loggear o algo, pero por ahora devolver null
            error_log("Error conexión stock: " . $exception->getMessage());
            return null;
        }
        return $stockConn;
    }

    public function getRsfaccar12Connection() {
        $rsfaccarConn = null;
        try {
            $connectionInfo = array(
                "Database" => "RSFACCAR12",
                "UID" => $this->username,
                "PWD" => $this->password,
                "CharacterSet" => "UTF-8"
            );
            $rsfaccarConn = sqlsrv_connect($this->host, $connectionInfo);
            if ($rsfaccarConn === false) {
                throw new Exception("Error de conexión a BD RSFACCAR12: " . print_r(sqlsrv_errors(), true));
            }
        } catch (Exception $exception) {
            error_log("Error conexión RSFACCAR12: " . $exception->getMessage());
            return null;
        }
        return $rsfaccarConn;
    }

    public function getDocDigitalesConnection() {
        $docDigitalesConn = null;
        try {
            $connectionInfo = array(
                "Database" => "DOC_DIGITALES",
                "UID" => $this->username,
                "PWD" => $this->password,
                "CharacterSet" => "UTF-8"
            );
            $docDigitalesConn = sqlsrv_connect($this->host, $connectionInfo);
            if ($docDigitalesConn === false) {
                throw new Exception("Error de conexión a BD DOC_DIGITALES: " . print_r(sqlsrv_errors(), true));
            }
        } catch (Exception $exception) {
            error_log("Error conexión DOC_DIGITALES: " . $exception->getMessage());
            return null;
        }
        return $docDigitalesConn;
    }
}
