<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Includes
require_once 'Database.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/TagController.php';
require_once 'routes/Router.php';

$database = new Database();
$db = $database->connect();

$router = new Router($db);
$router->run();
