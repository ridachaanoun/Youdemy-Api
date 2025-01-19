<?php

class CorsesController{
    private pdo $db;
    public function __construct(pdo $db) {
        $this->db = $db;
    }
    public function getcourse():void{
    try {
        if (!isset($_GET["id"])) {
            http_response_code(405);
            echo json_encode(["message" => "Invalid request data."]);
            return;
        }
        $CId = $_GET["id"];
        $course = Course::getCoursee($this->db,$CId);
        http_response_code(200);
        echo json_encode($course);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error: " . $e->getMessage()]);
        
    }
    }
}
