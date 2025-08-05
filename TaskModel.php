<?php
require_once 'db.php';

class TaskModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllTasks() {
        $stmt = $this->pdo->query('SELECT * FROM tasks');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask($title, $comment) {
        $stmt = $this->pdo->prepare('INSERT INTO tasks (title, comment, is_completed) VALUES (?, ?, 0)');
        $stmt->execute([$title, $comment]);
    }

    public function updateTask($id) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = NOT is_completed WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteTask($id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }
}
?>