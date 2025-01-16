<?php
require 'models/User.php';

class UserController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['name'], $data['email'], $data['password'], $data['role'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }
    
        try {
            User::registerUser(
                $this->db,
                htmlspecialchars($data['name']),
                htmlspecialchars($data['email']),
                $data['password'],
                $data['role'],
                $data['isValidated'] ?? false
            );
            
            http_response_code(201);
            echo json_encode(["message" => "User registered successfully"]);
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }
    
        try {
            $result = User::login($this->db, htmlspecialchars($data['email']), $data['password']);
    
            if ($result) {
                http_response_code(200);
                echo json_encode($result);
                return;
            }
    
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }

    
    public function logout() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }
        try{
            User::logout($this->db,$data['id']);

            http_response_code(200);
            echo json_encode(["message" => "Logged out successfully"]);

        }catch(PDOException $e){
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
        
    }

    public function viewCourses() {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10; // Default 10
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0; // Default 0
    
        try {
            $courses = User::viewCourses($this->db, $limit, $offset);
            
            http_response_code(200);
            echo json_encode($courses);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }
    

    public function searchCourses() {
        if (!isset($_GET['keyword'])) {
            http_response_code(400);
            echo json_encode(["message" => "Keyword is required"]);
            return;
        }

        try {
            $courses = User::searchCourses($this->db, htmlspecialchars($_GET['keyword']));
            http_response_code(200);
            echo json_encode($courses);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }

    
}
