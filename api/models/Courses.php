<?php

class Course {
    private int $id;
    private string $title;
    private string $description;
    private string $content;
    private array $tags;
    private ?Category $category;
    private int $teacher_id;
    private array $students;

    public function __construct(
        int $id,
        string $title,
        string $description,
        string $content,
        array $tags = [],
        ?Category $category = null,
        int $teacher_id = 0,
        array $students = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->tags = $tags;
        $this->category = $category;
        $this->teacher_id = $teacher_id;
        $this->students = $students;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getTags(): array {
        return $this->tags;
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function getTeacherId(): int {
        return $this->teacher_id;
    }

    public function getStudents(): array {
        return $this->students;
    }

    // Setters
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setTags(array $tags): void {
        $this->tags = $tags;
    }

    public function setCategory(?Category $category): void {
        $this->category = $category;
    }

    public function setTeacherId(int $teacher_id): void {
        $this->teacher_id = $teacher_id;
    }

    public function setStudents(array $students): void {
        $this->students = $students;
    }
    public static function getCoursee(PDO $dbConnection, int $cid): array {
        $query = "SELECT * FROM Courses where id = :cid";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':cid' => $cid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
