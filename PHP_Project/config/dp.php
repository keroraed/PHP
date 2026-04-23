<?php
class Service {
    private $host = "localhost";
    private $dbname = "PHP_Project";
    private $user = "root";
    private $password = "";
    public $connection;
    public function __construct() {
        try {
            $this->connection = new PDO("mysql:host=" .
            $this->host .";dbname=" . 
            $this->dbname,$this->user,$this->password);

        }

        catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        
        }
    }
}
class db extends Service {}