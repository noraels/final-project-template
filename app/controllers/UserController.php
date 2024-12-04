<?php

namespace app\controllers;

use app\models\User;

class UserController {

    // Login view method
    public function loginView() {
        // Load the login view
        include('../../public/login.php');
    }

    // Handle the login process
    public function login() {
        // Get the input data
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        // Create a new instance of the User model
        $userModel = new User();
    
        // Call the login method in the User model
        $user = $userModel->login(['email' => $email, 'password' => $password]);
    
        if ($user) {
            // If login is successful, start the session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
    
            if (isset($_POST['remember_me'])) {
                $cookieValue = bin2hex(random_bytes(16));  // Generate a secure cookie value
                setcookie('remember_me', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', false, true);  // Set the cookie
                $userModel->updateUserSessionExp(['sessionExpiration' => time() + (30 * 24 * 60 * 60), 'id' => $user['id']]);  // Update session expiration
            }
    
            // Redirect to homepage after successful login
            header("Location: /homepage");  
            exit();
        } else {
            // If login failed, display an error message
            echo 'Invalid credentials';
        }
    }
    

    // Handle logout
    public function logout() {
        session_start();
        session_destroy();
        setcookie('remember_me', '', time() - 3600, '/');  // Clear remember_me cookie
        header("Location: /login");  // Redirect to login page
        exit();
    }
}
