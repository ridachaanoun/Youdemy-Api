<?php

class Category {
    private int $id;
    private string $name;

    public function __construct (int $id = null, string $name = '') {
        $this->id = $id;
        $this->name = $name;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    // Method to get all categories
    public static function getCategories($db): array {
        $query = "SELECT * FROM Categories";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
