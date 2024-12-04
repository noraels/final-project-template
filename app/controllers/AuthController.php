<?php
namespace app\controllers;

use app\core\AuthHelper;
use app\models\User;

class AuthController {

    // Show the login form
    public function loginView() {
        include 'views/login.php';  
    }

    // Show the signup form
    public function signupView() {
        include 'views/signup.php';  
    }

    public function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        // Call the User model's login method directly
        $userModel = new User();
        $user = $userModel->login(['email' => $username, 'password' => $password]); 
    
        if ($user) {
            // Set session data for the logged-in user
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['firstName'];
    
            // If "Remember Me" is checked, set a cookie
            if (isset($_POST['remember_me'])) {
                $cookieValue = bin2hex(random_bytes(16));  
                setcookie('remember_me', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', false, true);  // Set for 30 days
                $userModel->updateUserSessionExp(['sessionExpiration' => time() + (30 * 24 * 60 * 60), 'id' => $user['id']]);  // Update session expiration in DB
            }
    
            header("Location: /assets/views/main/homepage.html");  
            exit();
        } else {
            echo 'Invalid login credentials';
        }
    }
    

    // Handle signup logic
    public function signup() {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];

        // Validate password match
        if ($password === $confirmPassword) {
            // Create user
            if (User::createUser($email, $username, $password)) {
                echo 'User created successfully';
            } else {
                echo 'Failed to create user';
            }
        } else {
            echo 'Passwords do not match';
        }
    }

    // Handle logout logic
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /login');
    }
}
