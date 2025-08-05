<?php
require_once 'TaskModel.php';

class TaskController {
    private $model;

    public function __construct($pdo) {
        $this->model = new TaskModel($pdo);
    }

    public function index() {
        $tasks = $this->model->getAllTasks();
        require 'views/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
            $this->model->createTask($_POST['title'], $_POST['comment']);
        }
        header('Location: index.php');
        exit;
    }

    public function update($id) {
        $this->model->updateTask($id);
        header('Location: index.php');
        exit;
    }

    public function destroy($id) {
        $this->model->deleteTask($id);
        header('Location: index.php');
        exit;
    }
}
?>