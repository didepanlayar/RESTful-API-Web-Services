<?php

class Items {
    private $itemsTable = "Items";
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $created; 
    public $modified; 
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }

    function create() {
		
		$query_database = $this->conn->prepare("INSERT INTO $this->itemsTable (name, description, price, category_id, created) VALUES(?,?,?,?,?)");
		
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		$this->created = htmlspecialchars(strip_tags($this->created));

		$query_database->bind_param("ssiis", $this->name, $this->description, $this->price, $this->category_id, $this->created);

		if($query_database->execute()) {
			return true;
		}

		return false;
	}

    function read() {	
		if($this->id) {
			$query_database = $this->conn->prepare("SELECT * FROM $this->itemsTable WHERE id = ?");
			$query_database->bind_param("i", $this->id);					
		} else {
			$query_database = $this->conn->prepare("SELECT * FROM $this->itemsTable");		
		}		
		$query_database->execute();			
		$result = $query_database->get_result();		
		return $result;	
	}
}

?>