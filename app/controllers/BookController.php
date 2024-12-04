<?php

namespace app\controllers;

use app\models\Book;
use app\models\Library;

class BookController {
    public function getBooksByLibrary($library_name) {
        try {
            $bookModel = new Book();
            error_log('Received library name: ' . $library_name);
            $books = $bookModel->getBooksByLibrary($library_name); // Fetch books by library name

    
            if ($books) {
                header('Content-Type: application/json');
                echo json_encode($books); // Return the books as JSON
            } else {
                throw new \Exception('No books found for this library');
            }
        } catch (\Exception $e) {
            error_log('Error fetching books: ' . $e->getMessage());  // Log the error message
            http_response_code(500);  // Set proper status code for server errors
            echo json_encode(['error' => 'Something went wrong while fetching books.']);
        }
        exit();
    }

    public function getAllBooks() {
        try {
            $bookModel = new Book();
            $books = $bookModel->getAllBooks();  // Fetch all books
        
            if ($books) {
                header('Content-Type: application/json');
                echo json_encode($books); // Return the books as JSON
            } else {
                throw new \Exception('No books found');
            }
        } catch (\Exception $e) {
            error_log('Error fetching books: ' . $e->getMessage());  // Log the error message
            http_response_code(500);  // Set proper status code for server errors
            echo json_encode(['error' => 'Something went wrong while fetching books.']);
        }
        exit();
    }
    
    
    public function addBook() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            exit();
        }
    
        $libraryName = $input['library_name'] ?? null;
        if (!$libraryName) {
            http_response_code(400);
            echo json_encode(['error' => 'Library name is required']);
            exit();
        }
    
        // Debugging the library name
        error_log("Received library name: " . $libraryName);
    
        $libraryModel = new Library();
        $library = $libraryModel->getLibraryByName($libraryName);
    
        if (!$library) {
            http_response_code(404);
            echo json_encode(['error' => 'Library not found']);
            exit();
        }
    
        // Debugging the library id
        error_log("Library found with ID: " . $library['id']);
    
        // Check if 'published_date' is provided and set a default if not
        $publishedDate = $input['published_date'] ?? '0000-00-00';  // Default if no date is provided
    
        $data = [
            'title' => $input['title'] ?? null,
            'author' => $input['author'] ?? null,
            'cover_url' => $input['cover_url'] ?? null,
            'synopsis' => $input['synopsis'] ?? null,
            'rating' => $input['rating'] ?? null,
            'review' => $input['review'] ?? null,
            'genre' => $input['genre'] ?? 'Unknown',
            'library_name' => $library['name'],
            'published_date' => $publishedDate
        ];
    
        if (!$data['title']) {
            http_response_code(400);
            echo json_encode(['error' => 'Book title is required']);
            exit();
        }
    
        $bookModel = new Book();
        $insertedBookId = $bookModel->addBook($data);
    
        if ($insertedBookId) {
            echo json_encode(['success' => true, 'book_id' => $insertedBookId]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to insert book']);
        }
        exit();
    }    
    
    public function updateBook($id) {
        // Get the JSON data from the request
        $input = json_decode(file_get_contents('php://input'), true);
    
        // Prepare the data for the update
        $data = [
            'id' => $id,
            'rating' => $input['rating'] ?? null,   
            'review' => $input['review'] ?? null,   
        ];
    
        // Ensure at least one field is provided (rating or review)
        if ($data['rating'] === null && $data['review'] === null) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided to update']);
            exit();
        }
    
        // Create a book model and update the book with the new data
        $bookModel = new Book();
        $rowsAffected = $bookModel->updateBook($data);
    
        // Check if any rows were updated
        if ($rowsAffected > 0) {
            echo json_encode(['success' => true]);
        } else {
            // If no rows were affected, return an error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update book']);
        }
        exit();
    }
    
    public function deleteBook($id) {
        error_log('Received DELETE request for book ID: ' . $id);
        try {
            // Proceed with deleting the book
            $this->db->beginTransaction();
            $query = "DELETE FROM $this->table WHERE id = :id";
            $this->query($query, ['id' => $id]);
            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('Error deleting book: ' . $e->getMessage());
            echo json_encode(['error' => 'Failed to delete book']);
        }
        exit();
    }
    

    public function getBookById($bookId) {
        $bookModel = new Book();
        $book = $bookModel->getBookById($bookId);  // This method retrieves the book from the database based on the bookId
    
        if ($book) {
            echo json_encode($book);  
        } else {
            echo json_encode(['error' => 'Book not found']); 
        }
    }
    
    // Search for books by title or author
    public function searchBooks($library_id, $term) {
        $bookModel = new Book();
        $books = $bookModel->searchBooks($library_id, $term);
        header('Content-Type: application/json');
        echo json_encode($books);
        exit();
    }

    // Sort books by a given column
    public function sortBooks($library_id, $column) {
        $bookModel = new Book();
        $books = $bookModel->sortBooks($library_id, $column);
        if ($books === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid sort column']);
            exit();
        }
        header('Content-Type: application/json');
        echo json_encode($books);
        exit();
    }

    // Reset book cover to the default
    public function resetCover($id) {
        $bookModel = new Book();
        $defaultCoverUrl = "default_cover_url_here"; 
        $bookModel->updateBook(['id' => $id, 'cover_url' => $defaultCoverUrl]);
        echo json_encode(['success' => true, 'message' => 'Cover reset to default']);
        exit();
    }

    // Handle custom cover uploads (drag-and-drop or file upload)
    public function uploadCover($id) {
        if (!isset($_FILES['cover_image'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit();
        }

        $uploadDir = './uploads/'; // Ensure this directory exists and is writable
        $uploadFile = $uploadDir . basename($_FILES['cover_image']['name']);

        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; 

        if (!in_array($_FILES['cover_image']['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed']);
            exit();
        }

        if ($_FILES['cover_image']['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['error' => 'File size exceeds the limit of 5MB']);
            exit();
        }

        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadFile)) {
            $bookModel = new Book();
            $bookModel->updateBook(['id' => $id, 'cover_url' => $uploadFile]);
            echo json_encode(['success' => true, 'message' => 'Cover updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload cover image']);
        }
        exit();
    }
}
