<?php
require_once "models/Courses.php";
class Teacher extends User {
    private array $createdCourses = [];

    public function __construct(PDO $dbConnection, int $id) {
        $query = "SELECT * FROM Users WHERE id = :id AND role = 'Teacher'";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Teacher with ID $id not found.");
        }

        parent::__construct(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role'],
            $user['status'],
            (bool) $user['is_validated']
        );

        $this->loadCreatedCourses($dbConnection);
    }

    public function loadCreatedCourses(PDO $dbConnection): void {
        $query = "SELECT c.id, c.title, c.description, c.content, c.teacher_id, 
                         c.category_id, cat.name AS category_name 
                  FROM Courses c
                  LEFT JOIN Categories cat ON c.category_id = cat.id
                  WHERE c.teacher_id = :teacherId";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':teacherId' => $this->id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->createdCourses = [];
        foreach ($result as $courseData) {
            // Fetch tags for the course
            $tagsQuery = "SELECT t.id, t.name FROM Tags t
                          INNER JOIN Courses_Tags ct ON t.id = ct.tag_id
                          WHERE ct.course_id = :courseId";
            $tagsStmt = $dbConnection->prepare($tagsQuery);
            $tagsStmt->execute([':courseId' => $courseData['id']]);
            $tagsData = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

            $tags = array_map(fn($tag) => new Tag($tag['id'], $tag['name']), $tagsData);

            // Create Category object
            $category = null;
            if ($courseData['category_id']) {
                $category = new Category($courseData['category_id'], $courseData['category_name']);
            }

            // Create Course object
            $course = new Course(
                $courseData['id'],
                $courseData['title'],
                $courseData['description'],
                $courseData['content'],
                $tags,
                $category,
                $this->id
            );

            $this->createdCourses[] = $course;
        }
    }

    // Getter and Setter for Created Courses
    public function getCreatedCourses(): array {
        return $this->createdCourses;
    }

    public function setCreatedCourses(array $courses): void {
        $this->createdCourses = $courses;
    }

    // Getters and Setters for Parent Attributes
    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getTEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function isValidated(): bool {
        return $this->isValidated;
    }

    public function setValidated(bool $validated): void {
        $this->is_validated = $validated;
    }
    public function addCourse(PDO $dbConnection, string $title, string $description, array $tags, int $categoryId, array $videoFile): bool {
        // Upload video
        $videoPath = "uploads/videos/" . basename($videoFile['name']);
        if (!move_uploaded_file($videoFile['tmp_name'], $videoPath)) {
            throw new Exception("Failed to upload video.");
        }

        // Insert course into database
        $query = "INSERT INTO Courses (title, description, content, teacher_id, category_id) VALUES (:title, :description, :content, :teacher_id, :category_id)";
        $stmt = $dbConnection->prepare($query);
        $success = $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':content' => $videoPath,
            ':teacher_id' => $this->id,
            ':category_id' => $categoryId
        ]);
        
        if (!$success) return false;

        $courseId = $dbConnection->lastInsertId();
        foreach ($tags as $tagId) {
            $tagQuery = "INSERT INTO Courses_Tags (course_id, tag_id) VALUES (:course_id, :tag_id)";
            $tagStmt = $dbConnection->prepare($tagQuery);
            $tagStmt->execute([':course_id' => $courseId, ':tag_id' => $tagId]);
        }
        return true;
    }

    public function editCourse(PDO $dbConnection, int $courseId, string $title, string $description, array $tags, int $categoryId, ?array $videoFile = null): bool {
        // Verify ownership
        $checkQuery = "SELECT id FROM Courses WHERE id = :courseId AND teacher_id = :teacherId";
        $checkStmt = $dbConnection->prepare($checkQuery);
        $checkStmt->execute([':courseId' => $courseId, ':teacherId' => $this->id]);
        if (!$checkStmt->fetch()) {
            throw new Exception("Unauthorized: You do not own this course.");
        }
    
        // Handle video file (if provided)
        $videoPath = null;
        if ($videoFile) {
            $videoPath = "uploads/videos/" . basename($videoFile['name']);
            if (!move_uploaded_file($videoFile['tmp_name'], $videoPath)) {
                throw new Exception("Failed to upload new video.");
            }
        }
    
        // Update Course details
        $updateQuery = "UPDATE Courses SET title = :title, description = :description, category_id = :categoryId" . ($videoPath ? ", content = :content" : "") . " WHERE id = :courseId";
        $updateStmt = $dbConnection->prepare($updateQuery);
        $params = [
            ':title' => $title,
            ':description' => $description,
            ':categoryId' => $categoryId,
            ':courseId' => $courseId
        ];
        if ($videoPath) $params[':content'] = $videoPath;
    
        if (!$updateStmt->execute($params)) {
            return false;
        }
    
        // Update tags (delete old tags and insert new ones)
        $dbConnection->prepare("DELETE FROM Courses_Tags WHERE course_id = :courseId")->execute([':courseId' => $courseId]);
        $tagInsertQuery = "INSERT INTO Courses_Tags (course_id, tag_id) VALUES (:courseId, :tag)";
        $tagStmt = $dbConnection->prepare($tagInsertQuery);
        foreach ($tags as $tag) {
            $tagStmt->execute([':courseId' => $courseId, ':tag' => trim($tag)]);
        }
    
        return true;
    }
    
    public function deleteCourse(PDO $dbConnection, int $courseId): bool {
        $checkQuery = "SELECT content FROM Courses WHERE id = :courseId AND teacher_id = :teacherId";
        $checkStmt = $dbConnection->prepare($checkQuery);
        $checkStmt->execute([':courseId' => $courseId, ':teacherId' => $this->id]);
        $course = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            throw new Exception("Unauthorized: You do not own this course.");
        }

        if ($course['content'] && file_exists($course['content'])) {
            unlink($course['content']); // Delete the video file
        }

        $deleteQuery = "DELETE FROM Courses WHERE id = :courseId";
        $deleteStmt = $dbConnection->prepare($deleteQuery);
        return $deleteStmt->execute([':courseId' => $courseId]);
    }

    public function getStatistics(PDO $dbConnection): array {
        // Get the number of created courses by the teacher
        $courseQuery = "SELECT COUNT(*) AS total_courses FROM Courses WHERE teacher_id = :teacherId";
        $stmt = $dbConnection->prepare($courseQuery);
        $stmt->execute([':teacherId' => $this->id]);
        $coursesCount = $stmt->fetch(PDO::FETCH_ASSOC)['total_courses'];

        // Get the number of students enrolled in the teacher's courses
        $studentsQuery = "SELECT COUNT(DISTINCT student_id) AS total_students
                          FROM Students_Courses
                          WHERE course_id IN (SELECT id FROM Courses WHERE teacher_id = :teacherId)";
        $stmt = $dbConnection->prepare($studentsQuery);
        $stmt->execute([':teacherId' => $this->id]);
        $studentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

        return [
            'total_courses' => $coursesCount,
            'total_students' => $studentsCount
        ];
    }

}
