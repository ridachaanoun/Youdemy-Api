<?php

class Router {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function run() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = trim($requestUri, '/');
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Define routes
        $routes = [
            'api/user/login' => ['POST', 'UserController', 'login'],
            'api/user/register' => ['POST', 'UserController', 'register'],
            'api/user/logout' => ['POST', 'UserController', 'logout'],
            'api/user/courses' => ['GET', 'UserController', 'viewCourses'],
            'api/user/search' => ['GET', 'UserController', 'searchCourses'],
            'api/category/list' => ['GET', 'CategoryController', 'getCategories'],
            'api/tag/get' => ['GET', 'TagController', 'getTags'],
            'api/student/details' => ['GET', 'StudentController', 'getStudentDetails'],
            'api/student/enroll' => ['POST', 'StudentController', 'enrollCourse']
        ];

        if (isset($routes[$requestUri])) {
            list($method, $controller, $action) = $routes[$requestUri];

            if ($method === $requestMethod) {
                $controllerInstance = new $controller($this->db);
                call_user_func([$controllerInstance, $action]);
                return;
            } else {
                http_response_code(405);
                echo json_encode(["message" => "Method Not Allowed"]);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["message" => "Route Not Found", "requested" => $requestUri]);
    }
}
