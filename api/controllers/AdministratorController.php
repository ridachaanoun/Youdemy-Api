<?php
require_once 'models/Administrator.php';

class AdministratorController {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Update teacher status
    public function updateTeacherStatus() {
        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['teacherId']) || !isset($data['status'])) {
                throw new Exception("Missing teacherId or status.");
            }

            $success = $admin->updateTeacherStatus($data['teacherId'], $data['status']);
            echo json_encode(["message" => $success ? "Teacher status updated successfully." : "Failed to update teacher status."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // Delete user
    public function deleteUser() {
        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['userId'])) {
                throw new Exception("Missing userId.");
            }

            $success = $admin->deleteUser($data['userId']);
            echo json_encode(["message" => $success ? "User deleted successfully." : "Failed to delete user."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // View global statistics
    public function viewGlobalStatistics() {
        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            echo json_encode($admin->viewGlobalStatistics());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // Manage Tags (add, update, delete)
    public function manageTags() {
        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['action'])) {
                throw new Exception("Action is required.");
            }

            $success = $admin->manageTags(
                $data['tagId'] ?? null,
                $data['action'],
                $data['tagName'] ?? null
            );

            echo json_encode([
                "message" => $success ? "Tag action executed successfully." : "Failed to execute tag action.",
            ]);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                http_response_code(400);
                echo json_encode(["message" => "tag already exists."]);
            }else{

                http_response_code(500);
                echo json_encode(["message" => $e->getMessage()]);
            }
        }
    }

    // Manage Categories (add, update, delete)
    public function manageCategories() {
        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['action'])) {
                throw new Exception("Action is required.");
            }

            $success = $admin->manageCategories(
                $data['categoryId'] ?? null,
                $data['action'],
                $data['categoryName'] ?? null
            );

            echo json_encode([
                "message" => $success ? "Category action executed successfully." : "Failed to execute category action.",
            ]);
        } catch (Exception $e) {
            if ($e->getCode() == 23000) {
                http_response_code(400);
                echo json_encode(["message" => "tag already exists."]);
            }else{

                http_response_code(500);
                echo json_encode(["message" => $e->getMessage()]);
            }
        }
    }

    // Authenticate administrator using API key
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

        if (!$user || $user['role'] !== 'Admin') {
            http_response_code(403);
            echo json_encode(["message" => "Invalid API Key or not authorized"]);
            exit;
        }

        return $user;
    }

    // Bulk insert tags for a course
    public function bulkInsertTags() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['courseId']) || !isset($data['tagIds']) || !is_array($data['tagIds'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid request data."]);
            return;
        }

        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $success = $admin->bulkInsertTags($data['courseId'], $data['tagIds']);
            echo json_encode(["message" => $success ? "Tags added successfully." : "Failed to add tags."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    // Delete a course
    public function deleteCourse() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['courseId'])) {
            http_response_code(400);
            echo json_encode(["message" => "Course ID is required."]);
            return;
        }

        try {
            $user = $this->authenticate();
            $admin = new Administrator($this->db, $user['id']);

            $success = $admin->deleteCourse($data['courseId']);
            echo json_encode(["message" => $success ? "Course deleted successfully." : "Failed to delete course."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
}
?>
