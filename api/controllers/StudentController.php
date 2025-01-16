<?php
require 'models/Student.php';
class StudentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

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

        if (!$user) {
            http_response_code(403);
            echo json_encode(["message" => "Invalid API Key"]);
            exit;
        }

        return $user;
    }

    // Get student details 
    public function getStudentDetails(): void {
        try {
            // get id 
            $user = $this->authenticate();
            if (!$user['id']) {
                http_response_code(403);
                echo json_encode(["message" => "authorization failed"]);
                return;
            }
            $id = $user["id"];
            if ($id === null) {
                throw new Exception("Student ID is required.");
            }

            // Instantiate the Student 
            $student = new Student($this->db, $id);
            $enrolledCourses = $student->getEnrolledCourses();

            // Return the student's enrolled courses
            http_response_code(200);
            echo json_encode([
                'id' => $student->getId(),
                'name' => $student->getName(),
                'email' => $student->getSEmail(),
                'role' => $student->getRole(),
                'enrolled_courses' => $enrolledCourses
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }

    // Enroll student
    public function enrollCourse(): void {
        try {
            $user = $this->authenticate();
            if (!$user['id']) {
                http_response_code(403);
                echo json_encode(["message" => "authorization failed"]);
                return;
            }

            $id = $user['id'];
            $courseId = $_POST['course_id'] ?? null;

            if ($id === null || $courseId === null) {
                throw new Exception("Student ID and Course ID are required.");
            }


            $student = new Student($this->db, $id);
            $student->enrollCourse($this->db, $courseId);

            // success 
            http_response_code(200);
            echo json_encode(["message" => "Enrolled successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }
}
