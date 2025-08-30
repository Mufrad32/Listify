<?php
require_once 'db.php';

class TaskModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllTasks($sort = 'created_at', $filter = 'all', $searchTags = '') {
        $query = 'SELECT * FROM tasks';
        $hasWhere = false;

        if ($filter === 'completed') {
            $query .= ' WHERE is_completed = 1';
            $hasWhere = true;
        } elseif ($filter === 'incomplete') {
            $query .= ' WHERE is_completed = 0';
            $hasWhere = true;
        }

        if (!empty($searchTags)) {
            $tagsArray = explode(' ', trim($searchTags));
            foreach ($tagsArray as $index => $tag) {
                $query .= ($hasWhere || $index > 0 ? ' AND' : ' WHERE') . " tags LIKE ?";
                if ($index === 0 && !$hasWhere) $hasWhere = true;
            }
        }

        $query .= ' ORDER BY is_completed ASC';
        if ($sort === 'title') {
            $query .= ', title ASC';
        } elseif ($sort === 'status') {
            $query .= ', is_completed ASC, title ASC';
        } elseif ($sort === 'priority') {
            $query .= ', CASE priority 
                WHEN "high" THEN 1 
                WHEN "normal" THEN 2 
                WHEN "low" THEN 3 
            END, title ASC';
        } elseif ($sort === 'deadline') {
            $query .= ', deadline IS NULL, deadline ASC';
        } else {
            $query .= ', created_at DESC';
        }

        $stmt = $this->pdo->prepare($query);
        if (!empty($searchTags)) {
            $tagsArray = explode(' ', trim($searchTags));
            $params = array_map(function($tag) { return "%$tag%"; }, $tagsArray);
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTask($title, $comment, $deadline, $priority, $tags = null, $isRepeatable = 0, $repeatCadence = 'once') {
        $stmt = $this->pdo->prepare('INSERT INTO tasks (title, comment, is_completed, deadline, priority, created_at, tags, is_repeatable, repeat_cadence) VALUES (?, ?, 0, ?, ?, NOW(), ?, ?, ?)');
        $stmt->execute([$title, $comment, $deadline ?: null, $priority ?: 'normal', $tags, $isRepeatable, $repeatCadence]);
    }

    public function editTask($id, $title, $comment, $deadline, $priority, $tags = null, $isRepeatable = 0, $repeatCadence = 'once') {
        $stmt = $this->pdo->prepare('UPDATE tasks SET title = ?, comment = ?, deadline = ?, priority = ?, updated_at = NOW(), tags = ?, is_repeatable = ?, repeat_cadence = ? WHERE id = ?');
        $stmt->execute([$title, $comment, $deadline ?: null, $priority ?: 'normal', $tags, $isRepeatable, $repeatCadence, $id]);
    }

    public function updateTask($id) {
        $stmt = $this->pdo->prepare('SELECT is_completed, is_repeatable, deadline, repeat_cadence, title, comment, priority, tags FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($task === false) {
            return;
        }

        // Update is_completed first
        $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = NOT is_completed, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);

        // Check if it was just marked complete and is repeatable
        $stmt = $this->pdo->prepare('SELECT is_completed FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        $updatedTask = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($updatedTask['is_completed'] && $task['is_repeatable']) {
            $newDeadline = $this->calculateNextDeadline($task['deadline'], $task['repeat_cadence']);
            $this->createTask($task['title'], $task['comment'], $newDeadline, $task['priority'], $task['tags'], 1, $task['repeat_cadence']);
        }
    }

    private function calculateNextDeadline($currentDeadline, $cadence) {
        if (!$currentDeadline) return null;
        $date = new DateTime($currentDeadline, new DateTimeZone('Asia/Dhaka'));
        switch ($cadence) {
            case 'daily':
                $date->modify('+1 day');
                break;
            case 'weekly':
                $date->modify('+1 week');
                break;
            case 'weekday':
                do {
                    $date->modify('+1 day');
                } while (in_array($date->format('N'), [6, 7])); // Skip Saturday (6) and Sunday (7)
                break;
            case 'monthly':
                $date->modify('+1 month');
                break;
            case 'annually':
                $date->modify('+1 year');
                break;
            default:
                return null;
        }
        return $date->format('Y-m-d H:i:s');
    }

    public function deleteTask($id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
    }
}
?>