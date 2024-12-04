<?php

namespace app\models;

class Book extends Model {

    protected $table = 'books';

    public function getBooksByLibrary($library_name) {
        $query = "SELECT * FROM $this->table WHERE library_name = :library_name";
        error_log('Executing query: ' . $query);  // Log the query being executed
        $books = $this->query($query, ['library_name' => $library_name]);
        
        return $books;
    }
    
    public function getAllBooks() {
        $query = "SELECT * FROM $this->table";  // Select all columns from the books table
        return $this->query($query);  
    }

    public function addBook($data) {
        // SQL query to insert the book data into the books table
        $query = "INSERT INTO $this->table (title, author, cover_url, published_date, synopsis, rating, review, genre, library_name)
                  VALUES (:title, :author, :cover_url, :published_date, :synopsis, :rating, :review, :genre, :library_name)";
        
        try {
            // Perform the query and pass the data
            $stmt = $this->db->prepare($query);
            $stmt->execute($data); // Execute the statement
    
            // Get the last inserted ID
            $bookId = $this->db->lastInsertId();
            if ($bookId) {
                return $bookId;  // Return the last inserted book ID
            } else {
                throw new \Exception('Failed to retrieve the last inserted ID');
            }
    
        } catch (\PDOException $e) {
            // Catch any errors and log them
            echo json_encode(['error' => 'Failed to insert book', 'message' => $e->getMessage()]);
            exit();
        }
    }    

    // Update a book's rating and review
    public function updateBook($data) {
        // Prepare the query to update the rating and/or review
        $query = "UPDATE $this->table SET ";

        // Dynamically build the update query for provided fields
        $fields = [];
        $params = [];

        if (isset($data['rating'])) {
            $fields[] = "rating = :rating";
            $params['rating'] = $data['rating'];
        }

        if (isset($data['review'])) {
            $fields[] = "review = :review";
            $params['review'] = $data['review'];
        }

        // If no fields are provided for update, return early
        if (empty($fields)) {
            return false;
        }

        // Add the WHERE clause for the book ID
        $query .= implode(', ', $fields) . " WHERE id = :id";
        $params['id'] = $data['id'];  // Add the book ID to the parameters

        // Execute the query
        return $this->query($query, $params);
    }
    
    public function deleteBook($id) {
        error_log('the book id is ' . $id);
        try {
            // Start a transaction
            $this->db->beginTransaction();
    
            // Now, delete the book from the books table
            $query = "DELETE FROM $this->table WHERE id = :id";
            $this->query($query, ['id' => $id]);
    
            // Commit the transaction
            $this->db->commit();
    
            return true;
        } catch (\PDOException $e) {
            // If something fails, roll back the transaction
            $this->db->rollBack();
            echo json_encode(['error' => 'Failed to delete book', 'message' => $e->getMessage()]);
            exit();
        }
    }    

    public function getBookById($id) {
        $query = "SELECT * FROM books WHERE id = :id";  
        $book = $this->query($query, ['id' => $id]);

        if ($book) {
            return $book[0];  // Return the book if found
        }

        return null;  // Return null if no book is found
    }
    
    // Search books by title or author
    public function searchBooks($library_name, $term) {
        $query = "SELECT * FROM $this->table 
                  WHERE library_name = :library_name 
                    AND (title LIKE :term OR author LIKE :term)";
        return $this->query($query, [
            'library_name' => $library_name,
            'term' => '%' . $term . '%'
        ]);
    }

    // Sort books by a given column
    public function sortBooks($library_name, $column) {
        $allowedColumns = ['title', 'author', 'rating', 'published_date'];
        if (!in_array($column, $allowedColumns)) {
            return false; // Prevent SQL injection
        }

        $query = "SELECT * FROM $this->table WHERE library_name = :library_name ORDER BY $column";
        return $this->query($query, ['library_name' => $library_name]);
    }
}
