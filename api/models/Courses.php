<?php

class Course {
    private int $id;
    private string $title;
    private string $description;
    private string $content;
    private array $tags;
    private ?Category $category;
    private int $teacher_id;
    private int $students;

    public function __construct(
        int $id,
        string $title,
        string $description,
        string $content,
        array $tags = [],
        ?Category $category = null,
        int $teacher_id = 0,
        int $students = 0
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

    public function getStudents(): int {
        return $this->students;
    }

    // Convert object to array for JSON encoding
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'tags' => array_map(fn($tag) => ['id' => $tag->getId(), 'name' => $tag->getName()], $this->tags),
            'category' => $this->category ? ['id' => $this->category->getId(), 'name' => $this->category->getName()] : null,
            'teacher_id' => $this->teacher_id,
            'students' => $this->students
        ];
    }

    public static function getCoursee(PDO $dbConnection, int $cid): array {
        $query = "SELECT * FROM Courses WHERE id = :cid";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':cid' => $cid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
