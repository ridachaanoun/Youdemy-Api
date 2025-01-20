<?php
require_once 'User.php';

class Administrator extends User {
    private PDO $db;

    public function __construct(PDO $dbConnection, int $id) {
        $query = "SELECT * FROM Users WHERE id = :id AND role = 'admin'";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Administrator with ID $id not found.");
        }
        $this->db = $dbConnection;
        parent::__construct(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role'],
            $user['status'],
            (bool) $user['is_validated']
        );
    }

    // Activate or suspend a teacher's account
    public function updateTeacherStatus(int $teacherId, string $status): bool {
        if (!in_array($status, ['active', 'suspended'])) {
            throw new Exception("Invalid status: $status");
        }

        $query = "UPDATE Users SET status = :status WHERE id = :teacherId AND role = 'Teacher'";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':status' => $status, ':teacherId' => $teacherId]);
    }

    // Delete a user or teacher
    public function deleteUser(int $userId): bool {
        $query = "DELETE FROM Users WHERE id = :userId";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':userId' => $userId]);
    }

    // Manage tags (add, update, delete)
    public function manageTags(int $tagId = null, string $action, ?string $tagName = null): bool {
        if ($action === 'add') {
            if (!$tagName) throw new Exception("Tag name is required.");
            $query = "INSERT INTO Tags (name) VALUES (:tagName)";
            $params = [':tagName' => $tagName];
        } elseif ($action === 'update') {
            if (!$tagName || !$tagId) throw new Exception("Tag ID and name are required.");
            $query = "UPDATE Tags SET name = :tagName WHERE id = :tagId";
            $params = [':tagId' => $tagId, ':tagName' => $tagName];
        } elseif ($action === 'delete') {
            if (!$tagId) throw new Exception("Tag ID is required.");
            $query = "DELETE FROM Tags WHERE id = :tagId";
            $params = [':tagId' => $tagId];
        } else {
            throw new Exception("Invalid action: $action");
        }

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    // Manage categories (add, update, delete)
    public function manageCategories(int $categoryId = null, string $action, ?string $categoryName = null): bool {
        if ($action === 'add') {
            if (!$categoryName) throw new Exception("Category name is required.");
            $query = "INSERT INTO Categories (name) VALUES (:categoryName)";
            $params = [':categoryName' => $categoryName];
        } elseif ($action === 'update') {
            if (!$categoryName || !$categoryId) throw new Exception("Category ID and name are required.");
            $query = "UPDATE Categories SET name = :categoryName WHERE id = :categoryId";
            $params = [':categoryId' => $categoryId, ':categoryName' => $categoryName];
        } elseif ($action === 'delete') {
            if (!$categoryId) throw new Exception("Category ID is required.");
            $query = "DELETE FROM Categories WHERE id = :categoryId";
            $params = [':categoryId' => $categoryId];
        } else {
            throw new Exception("Invalid action: $action");
        }

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    // View global statistics
    public function viewGlobalStatistics(): array {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM Users WHERE role = 'Teacher') AS total_teachers,
                (SELECT COUNT(*) FROM Users WHERE role = 'Student') AS total_students,
                (SELECT COUNT(*) FROM Courses) AS total_courses,
                (SELECT COUNT(*) FROM Students_Courses) AS total_enrollments
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Bulk insert tags for a course
    public function bulkInsertTags(int $courseId, array $tagIds): bool {
        $query = "INSERT IGNORE INTO Courses_Tags (course_id, tag_id) VALUES (:courseId, :tagId)";
        $stmt = $this->db->prepare($query);

        foreach ($tagIds as $tagId) {
            $stmt->execute([':courseId' => $courseId, ':tagId' => $tagId]);
        }

        return true;
    }

    // Delete a course
    public function deleteCourse(int $courseId): bool {
        $query = "DELETE FROM Courses WHERE id = :courseId";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':courseId' => $courseId]);
    }
    public static function getAll(pdo $db): array{
        $stmt = $db->prepare("select * from users where role = 'Admin'");
        $user =$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $user ;

    }
}
?>
