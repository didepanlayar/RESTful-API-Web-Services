<?php

class Database {
    // Database Configuration
    private $db_host     = 'mariadb';
    private $db_name     = 'db_restfulapi';
    private $db_username = 'root';
    private $db_password = 'root';

    public function dbConnection() {
        try {
            $conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_username, $this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            echo "Error! " . $e->getMessage();
            exit;
        }
    }
}

?>