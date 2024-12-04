<?php

// Require core files and setup environment variables
require '../app/core/Router.php';
require '../app/models/Model.php';
require '../app/controllers/Controller.php';
require '../app/controllers/MainController.php';
require '../app/controllers/UserController.php';
require '../app/controllers/AuthController.php';
require '../app/controllers/LibraryController.php';
require '../app/controllers/BookController.php';
require '../app/models/User.php';
require '../app/models/Library.php';
require '../app/models/Book.php';
require '../app/core/AuthHelper.php';

// Set up env variables
$env = parse_ini_file('../.env');

define('DBNAME', $env['DBNAME']);
define('DBHOST', $env['DBHOST']);
define('DBUSER', $env['DBUSER']);
define('DBPASS', $env['DBPASS']);
define('DBDRIVER', '');

// Set up other configs
define('DEBUG', true);
