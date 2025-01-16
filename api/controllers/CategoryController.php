<?php
require 'models/Category.php';

class CategoryController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getCategories() {

        $categories = Category::getCategories($this->db);

        http_response_code(200);
        echo json_encode($categories);
    }
}
