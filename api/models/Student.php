<?php
 class Student extends User {
    private array $courses = [];

    public function __construct(PDO $dbConnection, int $id) {
        // Fetch the user 
        $query = "SELECT * FROM Users WHERE id = :id AND role = 'Student'";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no student 
        if (!$user) {
            throw new Exception("Student with ID $id not found.");
        }

        // Call parent constructor to initialize the user
        parent::__construct(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['role'],
            $user['status'],
            (bool) $user['is_validated']
        );

        // Load the student's enrolled courses
        $this->loadEnrolledCourses($dbConnection);
    }

    private function loadEnrolledCourses(PDO $dbConnection): void {
        $query = "SELECT c.* FROM Courses c 
                  JOIN Students_Courses sc ON c.id = sc.course_id 
                  WHERE sc.student_id = :student_id";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':student_id' => $this->id]);
        $this->courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get enrolled courses
    public function getEnrolledCourses(): array {
        return $this->courses;
    }

    // Set enrolled courses
    public function setEnrolledCourses(array $courses): void {
        $this->courses = $courses;
    }

    // Get ID
    public function getId(): int {
        return $this->id;
    }
    // Get student email
    public function getSEmail(): string {
        return $this->email;
    }
    // Get name
    public function getName(): string {
        return $this->name;
    }

    // Setname
    public function setName(string $name): void {
        $this->name = $name;
    }


    // Set email
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    // Get  password
    public function getPassword(): string {
        return $this->password;
    }

    // Set password
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    // Get role
    public function getRole(): string {
        return $this->role;
    }

    // Set ole
    public function setRole(string $role): void {
        $this->role = $role;
    }

    // Get student status
    public function getStatus(): string {
        return $this->status;
    }

    // Set student status
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    // Get whether the student is validated
    public function isValidated(): bool {
        return $this->isValidated;
    }
    // Set whether the student is validated
    public function setValidated(bool $isValidated): void {
        $this->isValidated = $isValidated;
    }

    // Enroll the student in a course
    public function enrollCourse(PDO $dbConnection, int $courseId): bool {
        try {
            // Check if the student is already enrolled in the course
            $query = "SELECT * FROM Students_Courses WHERE student_id = :student_id AND course_id = :course_id";
            $stmt = $dbConnection->prepare($query);
            $stmt->execute([':student_id' => $this->id, ':course_id' => $courseId]);
            $existingEnrollment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingEnrollment) {
                throw new Exception("Student is already enrolled in this course.");
            }

            // Enroll the student in the course
            $query = "INSERT INTO Students_Courses (student_id, course_id) VALUES (:student_id, :course_id)";
            $stmt = $dbConnection->prepare($query);
            return $stmt->execute([
                ':student_id' => $this->id,
                ':course_id' => $courseId
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error enrolling in course: " . $e->getMessage());
        }
    }
}
