<?php

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
}
