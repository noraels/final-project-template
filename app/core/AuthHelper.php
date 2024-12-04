<?php
namespace app\core;

use app\models\User;
use DateTime;
use DateInterval;

class AuthHelper {

    // Validate login credentials
    public static function validateLogin($username, $password) {
        // Assuming $user is fetched from database
        $user = User::getUserByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    // Hash password for signup
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
