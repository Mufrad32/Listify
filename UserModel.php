<?php
require_once 'db.php';

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserByUsername($username) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $password) {
        $hashedPassword = md5($password); // Replace with password_hash() for security
        $stmt = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $hashedPassword]);
    }
}
?>