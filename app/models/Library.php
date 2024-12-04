<?php

namespace app\models;
use PDO; 
class Library extends Model {

    protected $table = 'libraries';

    // Get all libraries
    public function getAllLibraries() {
        return $this->findAll();
    }

    public function addLibrary($data) {
        $query = "INSERT INTO $this->table (name) VALUES (:name)";
        try {
            // Execute the insert query
            $this->query($query, $data);
            
            // Get the last inserted ID to confirm the record was created
            $library_id = $this->db->lastInsertId();
            
            if (!$library_id) {
                // If no ID is returned, output an error and debug the issue
                throw new Exception("Failed to get last insert ID. No rows inserted.");
            }
    
            // Return the library ID if the insert is successful
            return $library_id;
        } catch (Exception $e) {
            // If there was an error during the insert or retrieving the ID, handle it
            echo json_encode(['error' => 'Error occurred while creating library', 'message' => $e->getMessage()]);
            exit();
        }
    }
    
    // Get the last inserted library ID
    public function lastInsertId() {
        $con = $this->connect(); 
        return $con->lastInsertId();
    }

    public function deleteLibrary($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stm = $this->connect()->prepare($query);
        $stm->execute(['id' => $id]);
    
        // Return true if at least one row was deleted
        return $stm->rowCount() > 0;
    }    
    
    public function updateLibraryName($id, $name) {
        $query = "UPDATE $this->table SET name = :name WHERE id = :id";
        return $this->query($query, ['id' => $id, 'name' => $name]);
    }
    
    // Get library statistics (total books, average rating, etc.)
    public function getLibraryStats($id) {
        $query = "SELECT COUNT(*) AS total_books, AVG(books.star_rating) AS average_rating
                  FROM books WHERE library_id = :library_id";
        return $this->query($query, ['library_id' => $id]);
    }

    public function getLibraryByName($name) {
        $query = "SELECT id, name FROM libraries WHERE name = :name";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['name' => $name]);

        // Return the result as an associative array (or null if not found)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
