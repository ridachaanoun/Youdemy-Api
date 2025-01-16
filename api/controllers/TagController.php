<?php
require 'models/Tag.php';

class TagController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all tags
    public function getTags() {
        try {
            $tags = Tag::getTags($this->db);
            http_response_code(200);
            echo json_encode($tags);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error: " . $e->getMessage()]);
        }
    }
}
