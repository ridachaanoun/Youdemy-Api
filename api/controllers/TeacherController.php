<?php
require 'models/Teacher.php';

class TeacherController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all courses created by the teacher
    public function getCreatedCourses() {
        try {
            $user = $this->authenticate(); // authenticate the teacher
            $teacher = new Teacher($this->db, $user['id']);
            $courses = $teacher->getCreatedCourses();
            http_response_code(200);
            echo json_encode($courses,);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // Add a new course
    public function addCourse() {
        try {
            $user = $this->authenticate();
            $teacher = new Teacher($this->db, $user['id']);
    
            // Check if all fields are set in the form data
            if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['tags']) || !isset($_POST['categoryId']) || !isset($_FILES['video'])) {
                throw new Exception("Invalid data");
            }
    
            // Get form data
            $title = $_POST['title'];
            $description = $_POST['description'];
            $tags = is_array($_POST['tags']) ? $_POST['tags'] : explode(',', $_POST['tags']);
            $categoryId = $_POST['categoryId'];
    
            // Get the video file from the uploaded form
            $videoFile = $_FILES['video'];
    
            // Add the course
            $success = $teacher->addCourse($this->db, $title, $description, $tags, $categoryId, $videoFile);
            if ($success) {
                http_response_code(201);
                echo json_encode(["message" => "Course created successfully."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Failed to create course."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
    

    // Edit
    public function editCourse() {
        try {
            $user = $this->authenticate();
            $teacher = new Teacher($this->db, $user['id']);
    
    
            // Validate request data (all fields except video)
            if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['tags']) || !isset($_POST['categoryId'])) {
                throw new Exception("Invalid or missing data. Please provide courseId, title, description, tags, and categoryId.");
            }
    
            // Extract data
            $courseId = (int) $_POST['courseId'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $tags = is_array($_POST['tags']) ? $_POST['tags'] : explode(',', $_POST['tags']); // Convert tags if needed
            $categoryId = (int) $_POST['categoryId'];
            $videoFile = isset($_FILES['video']) ? $_FILES['video'] : null;
    
            // Ensure all fields are non-empty
            if (empty($title) || empty($description) || empty($tags) || empty($categoryId)) {
                throw new Exception("All fields (title, description, tags, categoryId) are required.");
            }
    
            // Update the course
            $success = $teacher->editCourse($this->db, $courseId, $title, $description, $tags, $categoryId, $videoFile);
            
            if ($success) {
                http_response_code(200);
                echo json_encode(["message" => "Course updated successfully."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Failed to update course."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
    
    // Delete a course
    public function deleteCourse() {
        try {
            $user = $this->authenticate();
            $teacher = new Teacher($this->db, $user['id']);

            // Get data from the request
            $data = json_decode(file_get_contents("php://input"), true);
            $courseId = $data['courseId'];

            // Delete the course
            $success = $teacher->deleteCourse($this->db, $courseId);
            if ($success) {
                http_response_code(200);
                echo json_encode(["message" => "Course deleted successfully."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Failed to delete course."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // Authenticate teacher using API key
    private function authenticate() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            exit;
        }

        $apiKey = $headers['Authorization'];
        $query = "SELECT * FROM Users WHERE api_key = :apiKey";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':apiKey' => $apiKey]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['role'] !== 'Teacher') {
            http_response_code(403);
            echo json_encode(["message" => "Invalid API Key or not authorized"]);
            exit;
        }

        return $user;
    }

    public function getStatistics() {
        try {
            $user = $this->authenticate(); // authenticate the teacher
            $teacher = new Teacher($this->db, $user['id']);
            $statistics = $teacher->getStatistics($this->db);
            http_response_code(200);
            echo json_encode($statistics);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
    public function getAllStudent(){
        try {

            $users = Teacher::getAll($this->db);
            if (isset($users)){
                http_response_code(200);
                echo json_encode($users);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }

    }
}

