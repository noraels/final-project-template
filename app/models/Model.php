<?php

namespace app\models;

use PDO;

abstract class Model {

    private static $connection; // Shared connection for all queries
    protected $table;
    protected $db;

    public function __construct() {
        // Check if the connection already exists, if not, create a new one
        if (!self::$connection) {
            self::$connection = new PDO("mysql:host=localhost;dbname=library", "root", "root");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        // Use the shared connection
        $this->db = self::$connection;
    }

    // Fetch all rows from the table
    public function findAll() {
        $query = "SELECT * FROM $this->table";
        return $this->query($query);
    }

    protected function connect() {
        return $this->db; 
    }

    public function query($query, $params = []) {
        // Prepare the statement
        $stmt = $this->db->prepare($query);
    
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        // Execute the statement
        $stmt->execute();
    
        // If the query is an UPDATE/DELETE, return the number of affected rows
        if (strpos($query, 'UPDATE') !== false || strpos($query, 'DELETE') !== false) {
            return $stmt->rowCount(); 
        }
    
        // Otherwise, return the result set (for SELECT queries)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the last inserted ID
    public function lastInsertId() {
        return $this->db->lastInsertId();  // Fetch the last inserted ID from the same connection
    }

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $query = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        return $this->query($query, $data);
    }
    
}
