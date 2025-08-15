<?php
require_once 'db.php';

class TaskModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllTasks($sort = 'title', $filter = 'all') {
        $query = 'SELECT * FROM tasks';
        if ($filter === 'completed') {
            $query .= ' WHERE is_completed = 1';
        } elseif ($filter === 'incomplete') {
            $query .= ' WHERE is_completed = 0';
        }
        if ($sort === 'title') {
            $query .= ' ORDER BY title ASC';
        } elseif ($sort === 'status') {
            $query .= ' ORDER BY is_completed ASC, title ASC';
        }
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask($title, $comment, $deadline) {
        $stmt = $this->pdo->prepare('INSERT INTO tasks (title, comment, is_completed, deadline, created_at) VALUES (?, ?, 0, ?, NOW())');
        $stmt->execute([$title, $comment, $deadline ?: null]);
    }

    public function editTask($id, $title, $comment, $deadline) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET title = ?, comment = ?, deadline = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$title, $comment, $deadline ?: null, $id]);
    }

    public function updateTask($id) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = NOT is_completed, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function deleteTask($id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }
}
?>