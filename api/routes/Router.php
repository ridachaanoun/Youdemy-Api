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
            // user routes
            'api/user/login' => ['POST', 'UserController', 'login'],
            'api/user/register' => ['POST', 'UserController', 'register'],
            'api/user/logout' => ['POST', 'UserController', 'logout'],
            'api/user/courses' => ['GET', 'UserController', 'viewCourses'],
            'api/user/search' => ['GET', 'UserController', 'searchCourses'],
            'api/user/role' => ['GET', 'UserController', 'getRoles'],
            // Category Tag routes
            'api/category/list' => ['GET', 'CategoryController', 'getCategories'],
            'api/tag/get' => ['GET', 'TagController', 'getTags'],
            // student routes
            'api/student/details' => ['GET', 'StudentController', 'getStudentDetails'],
            'api/student/enroll' => ['POST', 'StudentController', 'enrollCourse'],
            'api/student/getAll' => ['GET', 'StudentController', 'getAllStudent'],
            // teacher routes
            'api/teacher/courses' => ['GET', 'TeacherController', 'getCreatedCourses'],
            'api/teacher/course/add' => ['POST', 'TeacherController', 'addCourse'],
            'api/teacher/course/edit' => ['POST', 'TeacherController', 'editCourse'],
            'api/teacher/course/delete' => ['POST', 'TeacherController', 'deleteCourse'],
            'api/teacher/statistics' => ['POST', 'TeacherController', 'getStatistics'],
            // addmin routes
            'api/admin/teacher/update' => ['POST', 'AdministratorController', 'updateTeacherStatus'],
            'api/admin/user/delete' => ['POST', 'AdministratorController', 'deleteUser'],
            'api/admin/statistics' => ['GET', 'AdministratorController', 'viewGlobalStatistics'],

            'api/admin/tag/manage' => ['POST', 'AdministratorController', 'manageTags'],
            'api/admin/category/manage' => ['POST', 'AdministratorController', 'manageCategories'],
            'api/admin/courses/delete' => ['POST', 'AdministratorController', 'deleteCourse'],
            'api/admin/courses/tags' => ['POST', 'AdministratorController', 'bulkInsertTags'],
            'api/course' => ['GET', 'CorsesController', 'getcourse']


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
