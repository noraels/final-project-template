<?php

namespace app\core;

use app\controllers\MainController;
use app\controllers\UserController;
use app\controllers\LibraryController;
use app\controllers\BookController;


class Router {
    public $urlArray;

    function __construct()
    {
        $this->urlArray = $this->routeSplit();
        $this->handleMainRoutes();
        $this->handleUserRoutes();
        $this->handleLibraryRoutes();
        $this->handleBookRoutes();
        $this->handleAuthRoutes();
    }

    protected function routeSplit() {
        $removeQueryParams = strtok($_SERVER["REQUEST_URI"], '?');
        return explode("/", $removeQueryParams);
    }
    protected function handleMainRoutes() {
        if ($this->urlArray[1] === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->homepage();
        }
    }

        // Add authentication routes
        protected function handleAuthRoutes() {
            if ($this->urlArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                // Display login page
                $authController = new AuthController();
                $authController->loginView();
            }
    
            if ($this->urlArray[1] === 'signup' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                // Display signup page
                $authController = new AuthController();
                $authController->signupView();
            }
    
            if ($this->urlArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Process login
                $authController = new AuthController();
                $authController->login();
            }
    
            if ($this->urlArray[1] === 'signup' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Process signup
                $authController = new AuthController();
                $authController->signup();
            }
    
            if ($this->urlArray[1] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                // Logout user
                $authController = new AuthController();
                $authController->logout();
            }
        }

    protected function handleUserRoutes() {
        if ($this->urlArray[1] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->usersView();
        }

        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->getUsers();
        }
    }

    protected function handleLibraryRoutes() {
        $libraryController = new LibraryController();
    
        // Handle GET all libraries
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $libraryController->getAllLibraries();
        }
        
        // Handle POST add library
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $libraryController->addLibrary();
        }
    
        // Handle DELETE library
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($this->urlArray[3])) {
            $libraryController->deleteLibrary($this->urlArray[3]);
        }

        // Handle PUT update library name
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $_SERVER['REQUEST_METHOD'] === 'PUT' && isset($this->urlArray[3])) {
            $libraryController->updateLibraryName($this->urlArray[3]);
        }
    
        // Handle GET library stats (assuming stats endpoint is like /api/libraries/stats/{id})
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $this->urlArray[3] === 'stats' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($this->urlArray[4])) {
            $libraryController->getLibraryStats($this->urlArray[4]);
        }
    
        // Handle GET library by name (e.g. /api/libraries/name/{name})
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'libraries' && $this->urlArray[3] === 'name' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($this->urlArray[4])) {
            $libraryController->getLibraryByName();
        }
    }

    protected function handleBookRoutes() {
        $bookController = new BookController();
    
        // Get books by library (requires library_name)
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['library_name'])) {
            $libraryName = $_GET['library_name'];  // Get the library_name from query parameter
            $bookController->getBooksByLibrary($libraryName);  // Fetch books for that specific library
        }
        // Get all books (no library_name required)
        elseif ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && $_SERVER['REQUEST_METHOD'] === 'GET' && count($this->urlArray) === 3) {
            $bookController->getAllBooks();  // Fetch all books if no library_name is provided
        }
    
        // Add book to one or multiple libraries
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookController->addBook();
        }
    
        // Update book's rating and review (PUT request)
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && isset($this->urlArray[3]) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
            $bookId = intval($this->urlArray[3]);  // Get the book ID from the URL
            $bookController->updateBook($bookId);
        }
    
        // Delete a book (DELETE request)
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && isset($this->urlArray[3]) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            error_log('Matched DELETE request for book ID: ' . $this->urlArray[3]);
            $bookId = intval($this->urlArray[3]);  // Get the book ID from the URL
            $bookController->deleteBook($bookId);  // Call the deleteBook method in BookController
        }
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'books' && isset($this->urlArray[3]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $bookId = intval($this->urlArray[3]);  // Get the book ID from the URL
            $bookController->getBookById($bookId);  // Fetch a single book by its ID
        }
    }
    
    
    
}
