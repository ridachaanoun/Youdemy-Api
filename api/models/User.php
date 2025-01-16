<?php

class User {
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $password;
    protected string $role; 
    protected string $status;
    protected bool $isValidated;

    public function __construct(int $id, string $name, string $email, string $password, string $role, string $status, bool $isValidated) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
        $this->isValidated = $isValidated;
    }

    // Private method for registration
    private static function register(PDO $dbConnection, string $name, string $email, string $password, string $role, bool $isValidated): bool {
        try {
            $query = "INSERT INTO Users (name, email, password, role, status, is_validated) 
                      VALUES (:name, :email, :password, :role, 'suspended', :isValidated)";
            $stmt = $dbConnection->prepare($query);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            return $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => $role,
                ':isValidated' => (int)$isValidated,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Unique constraint violation (duplicate email)
                throw new Exception("The email '$email' is already registered.", 409);
            }
            throw new Exception("An unexpected error occurred: " . $e->getMessage(), 500);
        }
    }

    // Public method for user registration
    public static function registerUser(PDO $dbConnection, string $name, string $email, string $password, string $role, bool $isValidated): bool {
        return self::register($dbConnection, $name, $email, $password, $role, $isValidated);
    }

    // Fetch all courses with pagination
    public static function viewCourses(PDO $dbConnection, int $limit, int $offset): array {
        $query = "SELECT * FROM Courses LIMIT :limit OFFSET :offset";
        $stmt = $dbConnection->prepare($query);
    
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Search for courses by keyword
    public static function searchCourses(PDO $dbConnection, string $keyword): array {
        $query = "SELECT * FROM Courses WHERE title LIKE :keyword OR description LIKE :keyword";
        $stmt = $dbConnection->prepare($query);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Login method
    public static function login(PDO $dbConnection, string $email, string $password): ?array {
        try {
            $query = "SELECT * FROM Users WHERE email = :email";
            $stmt = $dbConnection->prepare($query);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['password'])) {
                $apiKey = bin2hex(random_bytes(32)); // Secure API key
    
                $updateQuery = "UPDATE Users SET api_key = :apiKey WHERE id = :id";
                $updateStmt = $dbConnection->prepare($updateQuery);
                $updateStmt->execute([':apiKey' => $apiKey, ':id' => $user['id']]);
    
                return [
                    "message" => "Login successful",
                    "id" => $user['id'],
                    "role" => $user['role'],
                    "api_key" => $apiKey
                ];
            }
    
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Logout method
    public static function logout(PDO $dbConnection, int $userId): bool {
        try {
            $query = "UPDATE Users SET api_key = NULL WHERE id = :id";
            $stmt = $dbConnection->prepare($query);
            return $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) {
            throw new Exception("Error: " . $e->getMessage(), 500);
        }
    }

    // Method to get the role of the user
    public function getRole(): string {
        return $this->role;
    }
    public static function getEmail($db,$email): array {
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
}
