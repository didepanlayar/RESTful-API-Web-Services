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
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            echo "Error! " . $e->getMessage();
            exit;
        }
    }

    public function getConnection() {		
		$conn = new mysqli($this->db_host, $this->db_username, $this->db_password, $this->db_name);
		if($conn->connect_error) {
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}

?>