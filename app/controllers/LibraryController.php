<?php

namespace app\controllers;

use app\models\Library;

class LibraryController {

    // Get all libraries
    public function getAllLibraries() {
        $libraryModel = new Library();
        $libraries = $libraryModel->getAllLibraries();
        header('Content-Type: application/json');
        echo json_encode($libraries);
        exit();
    }
    
    // Add a new library
    public function addLibrary() {
        // Read the raw JSON data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Ensure the data was parsed correctly
        if (!$data || !isset($data['name'])) {
            http_response_code(400); // Bad request
            echo json_encode(['error' => 'Library name is required']);
            exit();
        }
    
        // Validate the library name
        $name = $data['name'];
        if (strlen($name) < 2) {
            http_response_code(400); 
            echo json_encode(['error' => 'Library name is too short']);
            exit();
        }
    
        // Add the library using the model
        $libraryModel = new Library();
        $libraryModel->addLibrary(['name' => htmlspecialchars($name)]);
    
        // Get the last inserted library ID
        $library_id = $libraryModel->lastInsertId();
    
        // Return success response with the library ID
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'library_id' => $library_id
        ]);
        exit();
    }
    

    public function deleteLibrary($id) {
        // Validate the library ID
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid library ID']);
            exit();
        }
    
        // Call the model method to delete the library
        $libraryModel = new Library();
        $success = $libraryModel->deleteLibrary($id);  
    
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete library']);
        }
        exit();
    }

    public function updateLibraryName($id) {
        $data = json_decode(file_get_contents("php://input"), true);
    
        $name = $data['name'] ?? null;
    
        $name = trim($name);
        
        if (!$name || strlen($name) < 2) {
            http_response_code(400);
            echo json_encode(['error' => 'Library name is too short']);
            exit();
        }
        
        $libraryModel = new Library();
        $libraryModel->updateLibraryName($id, htmlspecialchars($name));
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    }
     
    // Get statistics for a specific library
    public function getLibraryStats($id) {
        // Validate the library ID
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid library ID']);
            exit();
        }

        $libraryModel = new Library();
        $stats = $libraryModel->getLibraryStats($id);

        header('Content-Type: application/json');
        echo json_encode($stats);
        exit();
    }

    public function viewLibrary($libraryId) {
        // Fetch the library details using the model
        $library = $this->getLibraryById($libraryId);
        $books = $this->getBooksByLibrary($libraryId);  
    
        include 'views/library-page.php';
    }
    
    public function getLibraryById($id) {
        $query = "SELECT * FROM libraries WHERE id = :id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getBooksByLibrary($libraryId) {
        // Fetch books associated with this library
        $query = "SELECT * FROM books WHERE library_id = :library_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(['library_id' => $libraryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLibraryByName() {
        // Get the library name from the query parameter
        $libraryName = urldecode($_GET['name'] ?? null); 
    
        if (!$libraryName) {
            http_response_code(400);
            echo json_encode(['error' => 'Library name is required']);
            exit();
        }
    
        $libraryModel = new Library();
        try {
            // Query the model for the library by name
            $library = $libraryModel->getLibraryByName($libraryName);
    
            if ($library) {
                echo json_encode($library);  // Return the library ID
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Library not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    
        exit();
    }    
    
}
